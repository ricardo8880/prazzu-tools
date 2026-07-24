<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

final class MarginMarkupToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.calculadora-margem-markup.index'))
            ->assertOk()
            ->assertSee('Calculadora de Margem, Markup e Formação de Preço');
    }

    public function test_calculation_returns_expected_result(): void
    {
        $response = $this->from(route('tools.calculadora-margem-markup.index'))
            ->post(route('tools.calculadora-margem-markup.calculate'), [
                'reference_date' => '2026-07-13',
                'base_cost' => '100,00',
                'additional_costs' => '20,00',
                'desired_margin' => '25',
            ]);

        $response
            ->assertOk()
            ->assertSee('R$ 160,00')
            ->assertSee('R$ 40,00')
            ->assertSee('2.0.0');

        $this->assertDatabaseCount('tool_runs', 0);
        $this->assertDatabaseHas('tool_usage_events', [
            'tool_slug' => 'calculadora-margem-markup',
            'event' => 'calculated',
        ]);
    }

    public function test_export_reuses_calculation_validation(): void
    {
        $this->from(route('tools.calculadora-margem-markup.index'))
            ->post(route('tools.calculadora-margem-markup.export'), [
                'reference_date' => '2026-07-13',
                'base_cost' => 'valor-invalido',
                'additional_costs' => '0,00',
                'desired_margin' => '25',
            ])
            ->assertRedirect(route('tools.calculadora-margem-markup.index'))
            ->assertSessionHasErrors('base_cost');
    }

    public function test_export_returns_csv_and_records_metric(): void
    {
        $response = $this->post(route('tools.calculadora-margem-markup.export'), [
            'reference_date' => '2026-07-13',
            'base_cost' => '100,00',
            'additional_costs' => '20,00',
            'desired_margin' => '25',
        ]);

        $response
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8')
            ->assertDownload('margem-markup.csv');

        $this->assertDatabaseHas('tool_usage_events', [
            'tool_slug' => 'calculadora-margem-markup',
            'event' => 'exported',
        ]);
    }

    public function test_zero_total_cost_returns_validation_error_instead_of_server_error(): void
    {
        $this->from(route('tools.calculadora-margem-markup.index'))
            ->post(route('tools.calculadora-margem-markup.calculate'), [
                'reference_date' => '2026-07-13',
                'base_cost' => '0,00',
                'additional_costs' => '0,00',
                'desired_margin' => '25',
            ])
            ->assertRedirect(route('tools.calculadora-margem-markup.index'))
            ->assertSessionHasErrors('base_cost');
    }

    public function test_batch_calculation_returns_results_for_multiple_products(): void
    {
        $response = $this->from(route('tools.calculadora-margem-markup.index'))
            ->post(route('tools.calculadora-margem-markup.batch.calculate'), [
                'reference_date' => '2026-07-15',
                'products' => [
                    [
                        'name' => 'Produto A',
                        'code' => 'A-01',
                        'category' => 'Varejo',
                        'base_cost' => '100,00',
                        'desired_margin' => '20',
                    ],
                    [
                        'name' => 'Produto B',
                        'code' => 'B-01',
                        'category' => 'Atacado',
                        'base_cost' => '200,00',
                        'desired_margin' => '25',
                    ],
                ],
            ]);

        $response
            ->assertOk()
            ->assertSee('Produto A')
            ->assertSee('R$ 125,00')
            ->assertSee('Produto B')
            ->assertSee('R$ 266,67');

        $this->assertDatabaseHas('tool_usage_events', [
            'tool_slug' => 'calculadora-margem-markup',
            'event' => 'batch_calculated',
        ]);
    }

    public function test_batch_calculation_requires_at_least_one_product(): void
    {
        $this->from(route('tools.calculadora-margem-markup.index'))
            ->post(route('tools.calculadora-margem-markup.batch.calculate'), [
                'reference_date' => '2026-07-15',
                'products' => [],
            ])
            ->assertRedirect(route('tools.calculadora-margem-markup.index'))
            ->assertSessionHasErrors('products');
    }

    public function test_csv_import_can_be_previewed_and_loaded_into_batch_products(): void
    {
        $csv = "Produto;Código;Categoria;Custo base;Margem %\nProduto A;A-01;Varejo;100,00;25\nProduto B;B-01;Atacado;200,00;30\n";
        $file = UploadedFile::fake()->createWithContent('produtos.csv', $csv);

        $previewResponse = $this->from(route('tools.calculadora-margem-markup.index'))
            ->post(route('tools.calculadora-margem-markup.import.preview'), ['import_file' => $file]);

        $previewResponse->assertOk()
            ->assertSee('produtos.csv')
            ->assertSee('2 linha(s)');

        preg_match('/name="import_token" value="([^"]+)"/', $previewResponse->getContent(), $matches);
        $this->assertNotEmpty($matches[1] ?? null);
        $token = $matches[1];

        $this->from(route('tools.calculadora-margem-markup.index'))
            ->post(route('tools.calculadora-margem-markup.import.process'), [
                'import_token' => $token,
                'available_headers' => ['Produto', 'Código', 'Categoria', 'Custo base', 'Margem %'],
                'name_column' => 'Produto',
                'code_column' => 'Código',
                'category_column' => 'Categoria',
                'base_cost_column' => 'Custo base',
                'desired_margin_column' => 'Margem %',
            ])
            ->assertOk()
            ->assertSee('2 produto(s) importado(s)')
            ->assertSee('Produto A');
    }

    public function test_import_template_is_downloadable(): void
    {
        $this->get(route('tools.calculadora-margem-markup.import.template'))
            ->assertOk()
            ->assertDownload('modelo-importacao-margem-markup.csv');
    }

    public function test_alternative_exports_cannot_bypass_the_central_access_gate(): void
    {
        config()->set('features.tools.calculadora-margem-markup.enabled', false);

        $this->post(route('tools.calculadora-margem-markup.export.pdf'), [
            'reference_date' => '2026-07-15',
            'base_cost' => '100,00',
            'desired_margin' => '25',
        ])->assertServiceUnavailable();

        $this->post(route('tools.calculadora-margem-markup.batch.export'), [
            'reference_date' => '2026-07-15',
            'products' => [[
                'name' => 'Produto A',
                'base_cost' => '100,00',
                'desired_margin' => '25',
            ]],
        ])->assertServiceUnavailable();

        $this->post(route('tools.calculadora-margem-markup.scenarios.export'), [
            'reference_date' => '2026-07-15',
            'product_name' => 'Produto A',
            'base_cost' => '100,00',
            'scenarios' => [
                ['name' => 'Base', 'desired_margin' => '20'],
                ['name' => 'Meta', 'desired_margin' => '25'],
            ],
        ])->assertServiceUnavailable();
    }
}
