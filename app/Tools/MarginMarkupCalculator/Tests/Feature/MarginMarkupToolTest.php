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
            ->assertSee('Calculadora de Margem e Markup');
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
            ->assertRedirect(route('tools.calculadora-margem-markup.index'))
            ->assertSessionHas('calculation_result', static function (array $result): bool {
                return $result['sale_price'] === 'R$ 160,00'
                    && $result['gross_profit'] === 'R$ 40,00'
                    && $result['net_profit'] === 'R$ 40,00'
                    && $result['rule_version'] === '2.0.0';
            });

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
            ->assertRedirect(route('tools.calculadora-margem-markup.index'))
            ->assertSessionHas('batch_calculation_results', static function (array $results): bool {
                return count($results) === 2
                    && $results[0]['name'] === 'Produto A'
                    && $results[0]['sale_price'] === 'R$ 125,00'
                    && $results[1]['name'] === 'Produto B'
                    && $results[1]['sale_price'] === 'R$ 266,67';
            });

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

        $previewResponse->assertRedirect(route('tools.calculadora-margem-markup.index'))
            ->assertSessionHas('product_import_preview');

        $preview = session('product_import_preview');
        $this->assertSame(2, $preview['total_rows']);
        $this->assertSame('Produto', $preview['suggested_mapping']['name_column']);

        $this->from(route('tools.calculadora-margem-markup.index'))
            ->post(route('tools.calculadora-margem-markup.import.process'), [
                'import_token' => $preview['token'],
                'available_headers' => $preview['headers'],
                'name_column' => 'Produto',
                'code_column' => 'Código',
                'category_column' => 'Categoria',
                'base_cost_column' => 'Custo base',
                'desired_margin_column' => 'Margem %',
            ])
            ->assertRedirect(route('tools.calculadora-margem-markup.index'))
            ->assertSessionHas('product_import_result', fn (array $result): bool => $result['imported'] === 2)
            ->assertSessionHasInput('products.0.name', 'Produto A');
    }

    public function test_import_template_is_downloadable(): void
    {
        $this->get(route('tools.calculadora-margem-markup.import.template'))
            ->assertOk()
            ->assertDownload('modelo-importacao-margem-markup.csv');
    }

}
