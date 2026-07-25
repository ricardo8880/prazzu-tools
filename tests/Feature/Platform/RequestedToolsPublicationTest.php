<?php

declare(strict_types=1);

namespace Tests\Feature\Platform;

use App\Core\Tools\ToolCatalog;
use App\Core\Tools\ToolRegistry;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class RequestedToolsPublicationTest extends TestCase
{
    /**
     * @return array<string, array{string, string, string, int, string}>
     */
    public static function requestedTools(): array
    {
        return [
            'employee cost calculator' => [
                'custo-funcionario-clt',
                'Calculadora de Custo de Funcionário CLT',
                'trabalhista',
                110,
                'folha de pagamento',
            ],
            'factor r simulator' => [
                'simulador-fator-r',
                'Simulador de Fator R',
                'fiscal',
                120,
                'rbt12',
            ],
            'late das calculator' => [
                'das-em-atraso',
                'Calculadora de DAS em Atraso',
                'fiscal',
                130,
                'selic',
            ],
            'labor charges calculator' => [
                'encargos-trabalhistas',
                'Calculadora de Encargos Trabalhistas',
                'trabalhista',
                140,
                'décimo terceiro',
            ],
            'employment model comparator' => [
                'comparador-clt-pj-autonomo',
                'Simulador CLT × PJ × Autônomo',
                'trabalhista',
                150,
                'pejotização',
            ],
            'employer inss calculator' => [
                'inss-patronal',
                'Calculadora de INSS Patronal',
                'trabalhista',
                160,
                'fap',
            ],
            'working capital calculator' => [
                'capital-de-giro',
                'Calculadora de Capital de Giro',
                'calculadoras',
                170,
                'liquidez',
            ],
            'cash flow calculator' => [
                'fluxo-de-caixa',
                'Calculadora de Fluxo de Caixa',
                'calculadoras',
                180,
                'tesouraria',
            ],
            'break even calculator' => [
                'ponto-de-equilibrio',
                'Calculadora de Ponto de Equilíbrio',
                'calculadoras',
                190,
                'margem de contribuição',
            ],
            'sales commission calculator' => [
                'comissao-vendedores',
                'Calculadora de Comissão de Vendedores',
                'calculadoras',
                200,
                'bônus',
            ],
            'payslip generator' => [
                'gerador-holerite',
                'Gerador de Holerite',
                'documentos',
                210,
                'contracheque',
            ],
            'admission simulator' => [
                'simulador-admissao',
                'Simulador de Admissão',
                'trabalhista',
                220,
                'custo admissional',
            ],
            'salary adjustment calculator' => [
                'reajuste-salarial',
                'Calculadora de Reajuste Salarial',
                'trabalhista',
                230,
                'dissídio',
            ],
            'income statement generator' => [
                'declaracao-rendimentos',
                'Gerador de Declaração de Rendimentos',
                'documentos',
                240,
                'irrf',
            ],
            'work income statement generator' => [
                'declaracao-trabalho-renda',
                'Gerador de Declaração de Trabalho/Renda',
                'documentos',
                250,
                'comprovante de vínculo',
            ],
        ];
    }

    public function test_requested_publication_set_is_complete_unique_and_ordered(): void
    {
        $expectedSlugs = array_column(self::requestedTools(), 0);
        $expectedPositions = array_column(self::requestedTools(), 3);
        $published = $this->app->make(ToolCatalog::class)
            ->all()
            ->whereIn('slug', $expectedSlugs)
            ->values();

        self::assertCount(15, $expectedSlugs);
        self::assertCount(15, array_unique($expectedSlugs));
        self::assertSame(range(110, 250, 10), $expectedPositions);
        self::assertCount(15, $published);
        self::assertSame($expectedSlugs, $published->pluck('slug')->all());
        self::assertSame($expectedPositions, $published->pluck('position')->all());
    }

    #[DataProvider('requestedTools')]
    public function test_manifest_registry_and_primary_route_are_publicly_consistent(
        string $slug,
        string $name,
        string $category,
        int $position,
        string $searchKeyword,
    ): void {
        $registry = $this->app->make(ToolRegistry::class);
        $module = $registry->findModule($slug);

        self::assertNotNull($module, "A ferramenta [{$slug}] não está registrada.");

        $manifest = $module->manifest();
        $routeName = "tools.{$slug}.index";

        self::assertSame($slug, $manifest->slug);
        self::assertSame($name, $manifest->name);
        self::assertSame($category, $manifest->category->value);
        self::assertSame($position, $manifest->position);
        self::assertSame($routeName, $manifest->routeName);
        self::assertTrue($manifest->status->isVisibleInCatalog());
        self::assertTrue($manifest->status->acceptsNewExecutions());
        self::assertContains($searchKeyword, $manifest->keywords);

        $catalogTool = $this->app->make(ToolCatalog::class)->find($slug);

        self::assertNotNull($catalogTool, "A ferramenta [{$slug}] não está no catálogo público.");
        self::assertSame($routeName, $catalogTool['route_name']);

        $route = Route::getRoutes()->getByName($routeName);

        self::assertNotNull($route, "A rota [{$routeName}] não está registrada.");
        self::assertSame("ferramentas/{$slug}", $route->uri());
        self::assertContains('GET', $route->methods());
        self::assertContains('HEAD', $route->methods());
    }

    public function test_catalog_renders_every_requested_tool_with_its_public_link(): void
    {
        $response = $this->get(route('tools.index'))->assertOk();

        foreach (self::requestedTools() as [$slug, $name]) {
            $url = route("tools.{$slug}.index");

            $response
                ->assertSee($name)
                ->assertSee('href="'.$url.'"', false);
        }
    }

    public function test_category_pages_render_every_requested_tool_in_the_declared_category(): void
    {
        $toolsByCategory = collect(self::requestedTools())
            ->groupBy(static fn (array $tool): string => $tool[2]);

        foreach ($toolsByCategory as $category => $tools) {
            $response = $this->get(route('tools.category', ['category' => $category]))
                ->assertOk();
            $listedSlugs = $response->viewData('tools')->pluck('slug')->all();

            foreach ($tools as [$slug, $name]) {
                self::assertContains($slug, $listedSlugs);

                $response
                    ->assertSee($name)
                    ->assertSee('href="'.route("tools.{$slug}.index").'"', false);
            }
        }
    }

    #[DataProvider('requestedTools')]
    public function test_tool_page_is_a_complete_indexable_document_with_expected_metadata(
        string $slug,
        string $name,
        string $category,
        int $position,
        string $searchKeyword,
    ): void {
        $tool = $this->app->make(ToolCatalog::class)->find($slug);

        self::assertNotNull($tool);
        self::assertNotEmpty($tool['description']);

        $url = route("tools.{$slug}.index");
        $response = $this->get($url)->assertOk();
        $html = (string) $response->getContent();

        $response
            ->assertSee('<!doctype html>', false)
            ->assertSee('<html lang="pt-BR"', false)
            ->assertSee('<body class="prazzu-app">', false)
            ->assertSee('<main id="main-content"', false)
            ->assertSee($name)
            ->assertSee($tool['description'])
            ->assertSee('<meta name="robots" content="index,follow">', false)
            ->assertSee('<link rel="canonical" href="'.$url.'">', false)
            ->assertSee('</html>', false);

        self::assertMatchesRegularExpression(
            '/<meta\s+name="description"\s+content="[^"]+"\s*>/s',
            $html,
        );
    }

    #[DataProvider('requestedTools')]
    public function test_catalog_search_finds_tool_by_full_name_and_relevant_manifest_keyword(
        string $slug,
        string $name,
        string $category,
        int $position,
        string $searchKeyword,
    ): void {
        foreach ([$name, $searchKeyword] as $query) {
            $response = $this->get(route('tools.index', ['q' => $query]))
                ->assertOk();
            $resultSlugs = $response->viewData('tools')->pluck('slug')->all();

            self::assertContains(
                $slug,
                $resultSlugs,
                "A busca por [{$query}] não encontrou [{$slug}].",
            );

            $response
                ->assertSee($name)
                ->assertSee('href="'.route("tools.{$slug}.index").'"', false);
        }
    }
}
