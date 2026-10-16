<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

final class MarginMarkupToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.margin-markup-calculator.index'))
            ->assertOk()
            ->assertSee('Calculadora de Margin, Markup & Pricing');
    }

    public function test_calculation_returns_expected_result(): void
    {
        $response = $this->from(route('tools.margin-markup-calculator.index'))
            ->post(route('tools.margin-markup-calculator.calculate'), [
                'reference_date' => '2026-07-13',
                'base_cost' => '100.00',
                'additional_costs' => '20.00',
                'desired_margin' => '25',
            ]);

        $response
            ->assertOk()
            ->assertSee('$160.00')
            ->assertSee('$40.00')
            ->assertSee('2.1.0');

        $this->assertDatabaseCount('tool_runs', 0);
        $this->assertDatabaseHas('tool_usage_events', [
            'tool_slug' => 'margin-markup-calculator',
            'event' => 'calculated',
        ]);
    }

    public function test_export_reuses_calculation_validation(): void
    {
        $this->from(route('tools.margin-markup-calculator.index'))
            ->post(route('tools.margin-markup-calculator.export'), [
                'reference_date' => '2026-07-13',
                'base_cost' => 'invalid-value',
                'additional_costs' => '0.00',
                'desired_margin' => '25',
            ])
            ->assertRedirect(route('tools.margin-markup-calculator.index'))
            ->assertSessionHasErrors('base_cost');
    }

    #[CoversPlusFeature('margin-markup-calculator', 'export')]
    public function test_export_returns_csv_and_records_metric(): void
    {
        $response = $this->post(route('tools.margin-markup-calculator.export'), [
            'reference_date' => '2026-07-13',
            'base_cost' => '100.00',
            'additional_costs' => '20.00',
            'desired_margin' => '25',
        ]);

        $response
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8')
            ->assertDownload('margin-markup.csv');

        $this->assertDatabaseHas('tool_usage_events', [
            'tool_slug' => 'margin-markup-calculator',
            'event' => 'exported',
        ]);
    }

    public function test_zero_total_cost_returns_validation_error_instead_of_server_error(): void
    {
        $this->from(route('tools.margin-markup-calculator.index'))
            ->post(route('tools.margin-markup-calculator.calculate'), [
                'reference_date' => '2026-07-13',
                'base_cost' => '0.00',
                'additional_costs' => '0.00',
                'desired_margin' => '25',
            ])
            ->assertRedirect(route('tools.margin-markup-calculator.index'))
            ->assertSessionHasErrors('base_cost');
    }

    #[CoversPlusFeature('margin-markup-calculator', 'batch_processing')]
    public function test_batch_calculation_returns_results_for_multiple_products(): void
    {
        $response = $this->from(route('tools.margin-markup-calculator.index'))
            ->post(route('tools.margin-markup-calculator.batch.calculate'), [
                'reference_date' => '2026-07-15',
                'products' => [
                    [
                        'name' => 'Product A',
                        'code' => 'A-01',
                        'category' => 'Retail',
                        'base_cost' => '100.00',
                        'desired_margin' => '20',
                    ],
                    [
                        'name' => 'Product B',
                        'code' => 'B-01',
                        'category' => 'Wholesale',
                        'base_cost' => '200.00',
                        'desired_margin' => '25',
                    ],
                ],
            ]);

        $response
            ->assertOk()
            ->assertSee('Product A')
            ->assertSee('$125.00')
            ->assertSee('Product B')
            ->assertSee('$266.67');

        $this->assertDatabaseHas('tool_usage_events', [
            'tool_slug' => 'margin-markup-calculator',
            'event' => 'batch_calculated',
        ]);
    }

    #[CoversPlusFeature('margin-markup-calculator', 'scenarios')]
    public function test_scenarios_compare_two_pricing_strategies(): void
    {
        $this->post(route('tools.margin-markup-calculator.scenarios.simulate'), [
            'reference_date' => '2026-07-15', 'product_name' => 'Product A', 'base_cost' => '100.00',
            'scenarios' => [
                ['name' => 'Conservative', 'desired_margin' => '20'],
                ['name' => 'Target', 'desired_margin' => '25'],
            ],
        ])->assertOk()->assertSee('Conservative')->assertSee('Target')->assertSee('$125.00')->assertSee('$133.33');
    }

    public function test_batch_calculation_requires_at_least_one_product(): void
    {
        $this->from(route('tools.margin-markup-calculator.index'))
            ->post(route('tools.margin-markup-calculator.batch.calculate'), [
                'reference_date' => '2026-07-15',
                'products' => [],
            ])
            ->assertRedirect(route('tools.margin-markup-calculator.index'))
            ->assertSessionHasErrors('products');
    }

    public function test_csv_import_can_be_previewed_and_loaded_into_batch_products(): void
    {
        $csv = "Product;Code;Category;Base cost;Margin %\nProduct A;A-01;Retail;100.00;25\nProduct B;B-01;Wholesale;200.00;30\n";
        $file = UploadedFile::fake()->createWithContent('products.csv', $csv);

        $previewResponse = $this->followingRedirects()
            ->from(route('tools.margin-markup-calculator.index'))
            ->post(route('tools.margin-markup-calculator.import.preview'), ['import_file' => $file]);

        $previewResponse->assertOk()
            ->assertSee('products.csv')
            ->assertSee('2 row(s)');

        preg_match('/name="import_token" value="([^"]+)"/', $previewResponse->getContent(), $matches);
        $this->assertNotEmpty($matches[1] ?? null);
        $token = $matches[1];

        $this->followingRedirects()
            ->from(route('tools.margin-markup-calculator.index'))
            ->post(route('tools.margin-markup-calculator.import.process'), [
                'import_token' => $token,
                'available_headers' => ['Product', 'Code', 'Category', 'Base cost', 'Margin %'],
                'name_column' => 'Product',
                'code_column' => 'Code',
                'category_column' => 'Category',
                'base_cost_column' => 'Base cost',
                'desired_margin_column' => 'Margin %',
            ])
            ->assertOk()
            ->assertSee('2 product(s) imported')
            ->assertSee('Product A');
    }

    public function test_import_template_is_downloadable(): void
    {
        $this->get(route('tools.margin-markup-calculator.import.template'))
            ->assertOk()
            ->assertDownload('margin-markup-import-template.csv');
    }

    public function test_alternative_exports_cannot_bypass_the_central_access_gate(): void
    {
        config()->set('features.tools.margin-markup-calculator.enabled', false);

        $this->post(route('tools.margin-markup-calculator.export.pdf'), [
            'reference_date' => '2026-07-15',
            'base_cost' => '100.00',
            'desired_margin' => '25',
        ])->assertServiceUnavailable();

        $this->post(route('tools.margin-markup-calculator.batch.export'), [
            'reference_date' => '2026-07-15',
            'products' => [[
                'name' => 'Product A',
                'base_cost' => '100.00',
                'desired_margin' => '25',
            ]],
        ])->assertServiceUnavailable();

        $this->post(route('tools.margin-markup-calculator.scenarios.export'), [
            'reference_date' => '2026-07-15',
            'product_name' => 'Product A',
            'base_cost' => '100.00',
            'scenarios' => [
                ['name' => 'Base', 'desired_margin' => '20'],
                ['name' => 'Target', 'desired_margin' => '25'],
            ],
        ])->assertServiceUnavailable();
    }
}
