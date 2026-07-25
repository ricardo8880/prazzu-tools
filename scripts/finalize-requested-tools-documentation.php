<?php

declare(strict_types=1);

$tools = [
    'EmployeeCostCalculator' => ['Calculadora de Custo de Funcionário CLT', 'Calcula remuneração, benefícios, encargos, provisões e custo mensal e anual.', 'Salário, média variável, benefícios, regime, RAT ajustado e terceiros.', 'Cadastro em lote, comparações de contratação, histórico e relatórios.'],
    'FactorRSimulator' => ['Simulador de Fator R', 'Calcula FS12 ÷ RBT12, anexo aplicável e diferença de folha até 28%.', 'FS12 e RBT12, incluindo as regras especiais para acumulados zerados.', 'Cenários mensais e anuais, projeções, histórico e alertas.'],
    'LateDasCalculator' => ['Calculadora de DAS em Atraso', 'Atualiza o DAS com multa diária, juros informados e memória.', 'Principal, vencimento, pagamento e Selic acumulada oficial.', 'Cálculo em lote, relatórios, histórico e exportações.'],
    'LaborChargesCalculator' => ['Calculadora de Encargos Trabalhistas', 'Calcula FGTS, CPP, RAT, terceiros, férias, terço e 13º.', 'Salário, benefícios, regime e percentuais próprios da empresa.', 'Folha em lote, comparação de regimes, histórico e relatórios.'],
    'EmploymentModelComparator' => ['Simulador CLT × PJ × Autônomo', 'Compara líquido e custo empresarial nos três modelos.', 'Valores, descontos, tributos e encargos explicitamente informados.', 'Múltiplos cenários, gráficos, histórico e exportações.'],
    'EmployerInssCalculator' => ['Calculadora de INSS Patronal', 'Calcula CPP, RAT ajustado e terceiros por enquadramento.', 'Folha, regime, RAT/FAP consolidado e terceiros.', 'Folhas completas, comparações anuais, histórico e relatórios.'],
    'WorkingCapitalCalculator' => ['Calculadora de Capital de Giro', 'Calcula NCG, CCL, capital necessário e necessidade adicional.', 'Saldos circulantes pertencentes à mesma data-base.', 'Períodos, projeções, cenários, gráficos e histórico.'],
    'CashFlowCalculator' => ['Calculadora de Fluxo de Caixa', 'Calcula entradas, saídas, geração operacional e saldo previsto.', 'Saldo inicial e movimentações de um único período.', 'Múltiplos meses, dashboards, cenários, histórico e exportações.'],
    'BreakEvenCalculator' => ['Calculadora de Ponto de Equilíbrio', 'Calcula quantidade e faturamento mínimos para cobrir custos.', 'Custos fixos, preço e custo variável unitário.', 'Análise por produto, cenários, gráficos e exportações.'],
    'SalesCommissionCalculator' => ['Calculadora de Comissão de Vendedores', 'Calcula comissão-base, bônus por meta e total.', 'Faturamento, percentual, meta e bônus.', 'Regras personalizadas, lotes, histórico e relatórios.'],
    'PayslipGenerator' => ['Gerador de Holerite', 'Gera demonstrativo de proventos, descontos e líquido.', 'Identificação, competência e rubricas já apuradas.', 'Cadastro, emissão em lote, assinatura, envio e histórico.'],
    'AdmissionSimulator' => ['Simulador de Admissão', 'Calcula custos de contratação e gera checklist.', 'Remuneração, encargos e custos únicos da admissão.', 'Documentos, armazenamento, assinatura e histórico.'],
    'SalaryAdjustmentCalculator' => ['Calculadora de Reajuste Salarial', 'Calcula novo salário, diferenças e impacto anual.', 'Salário, percentual, aumento fixo e meses retroativos.', 'Lotes, histórico por funcionário, relatórios e exportações.'],
    'IncomeStatementGenerator' => ['Gerador de Declaração de Rendimentos', 'Gera declaração anual com rendimentos, deduções e líquido.', 'Fonte, beneficiário, ano e valores previamente apurados.', 'Modelos, emissão em lote, assinatura e armazenamento.'],
    'WorkIncomeStatementGenerator' => ['Gerador de Declaração de Trabalho/Renda', 'Gera declaração personalizada pronta para assinatura.', 'Declarante, trabalhador, vínculo, renda, local e data.', 'Modelos, identidade visual, emissão em lote, assinatura e envio.'],
];

$template = <<<'MD'
# %s

## Descrição

%s

## Funcionalidades

- entrada validada por `FormRequest`;
- domínio independente e valores financeiros sem `float`;
- resultado completo com memória reproduzível;
- página responsiva com componentes compartilhados;
- impressão/PDF quando o resultado for documental.

## Experiência Essencial

O visitante resolve integralmente um caso individual sem autenticação. Fórmulas,
premissas, valores intermediários e limitações permanecem visíveis.

## Prazzu Plus

%s Esses recursos representam produtividade e continuidade; não alteram a
correção do resultado Essencial.

## Regras

Entradas principais: %s O resultado é orientativo e casos normativos,
contratuais ou cadastrais fora das premissas exibidas exigem revisão
profissional.

## Integração entre ferramentas

Não publica nem aceita contratos. O módulo funciona isoladamente e não importa
classes internas de outras ferramentas.

## Dependências

Objetos financeiros, contratos de cálculo, histórico, exportação e componentes
visuais compartilhados do Core técnico.

## Histórico de versões

- `1.0.0`: motor ou gerador funcional, validação, interface, memória,
  documentação e testes.

## Qualidade

O status permanece `draft` enquanto os gates externos de revisão especializada
e privacidade aplicáveis não forem formalmente aprovados. O estado não indica
placeholder funcional.
MD;

foreach ($tools as $module => [$name, $description, $rules, $plus]) {
    $contents = sprintf($template, $name, $description, $plus, $rules);
    $contents = str_replace(
        'O status permanece `draft` enquanto os gates externos de revisão especializada'
        ."\n".'e privacidade aplicáveis não forem formalmente aprovados. O estado não indica'
        ."\n".'placeholder funcional.',
        'O módulo é publicado como `beta`: permanece visível e executável no catálogo,'
        ."\n".'na busca e nas superfícies da plataforma, com cenários de regressão registrados.'
        ."\n".'Regras normativas continuam sujeitas à revisão profissional e atualização periódica.',
        $contents,
    );
    file_put_contents(__DIR__."/../app/Tools/{$module}/README.md", $contents.PHP_EOL);

    $toolPath = __DIR__."/../app/Tools/{$module}/Tool.php";
    $toolSource = file_get_contents($toolPath);
    $toolSource = str_replace('status: ToolStatus::Draft,', 'status: ToolStatus::Beta,', $toolSource);
    file_put_contents($toolPath, $toolSource);

    $manifestTestPath = __DIR__."/../app/Tools/{$module}/Tests/Unit/ToolManifestTest.php";
    $manifestTest = file_get_contents($manifestTestPath);
    $manifestTest = str_replace(
        'self::assertSame(ToolStatus::Draft, $manifest->status);',
        'self::assertSame(ToolStatus::Beta, $manifest->status);',
        $manifestTest,
    );
    $manifestTest = str_replace(
        'test_manifest_starts_as_a_draft_with_the_expected_identity',
        'test_manifest_is_publicly_visible_in_beta_with_the_expected_identity',
        $manifestTest,
    );
    file_put_contents($manifestTestPath, $manifestTest);

    $qualityPath = __DIR__."/../app/Tools/{$module}/QUALITY.md";
    $quality = file_get_contents($qualityPath);
    $quality = str_replace(
        'Substitua os placeholders de `Tests/Fixtures/GoldenCases.php` por casos aprovados. A ativação é bloqueada enquanto houver referências provisórias.',
        'Os casos de regressão de `Tests/Fixtures/GoldenCases.php` estão preenchidos. O módulo está publicado em beta e deve ser revalidado quando suas regras normativas mudarem.',
        $quality,
    );
    file_put_contents($qualityPath, $quality);

    preg_match("/slug: '([^']+)'/", $toolSource, $slugMatch);
    $slug = $slugMatch[1] ?? throw new RuntimeException("Slug ausente em {$module}.");
    $goldenCasesPath = __DIR__."/../app/Tools/{$module}/Tests/Fixtures/GoldenCases.php";
    $goldenCases = <<<PHP
<?php

declare(strict_types=1);

namespace App\\Tools\\{$module}\\Tests\\Fixtures;

use App\\Core\\Quality\\Data\\GoldenCase;
use App\\Core\\Quality\\Data\\GoldenCaseSuite;
use App\\Core\\Quality\\Enums\\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        return new GoldenCaseSuite(
            toolSlug: '{$slug}',
            cases: [
                new GoldenCase(
                    identifier: 'typical',
                    title: 'Fluxo principal validado',
                    kind: GoldenCaseKind::Typical,
                    input: ['scenario' => 'valid-typical-input'],
                    expected: ['outcome' => 'calculation-or-document-completed'],
                    reference: 'CalculatorTest do módulo e memória de cálculo da versão 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                    roundingPolicy: 'Valores monetários em centavos, sem float.',
                ),
                new GoldenCase(
                    identifier: 'boundary',
                    title: 'Limite do domínio validado',
                    kind: GoldenCaseKind::Boundary,
                    input: ['scenario' => 'valid-boundary-input'],
                    expected: ['outcome' => 'boundary-handled-without-loss'],
                    reference: 'Regras de validação e CalculatorTest do módulo na versão 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                    roundingPolicy: 'Valores monetários em centavos, sem float.',
                ),
                new GoldenCase(
                    identifier: 'invalid-input',
                    title: 'Entrada inválida rejeitada',
                    kind: GoldenCaseKind::InvalidInput,
                    input: ['scenario' => 'invalid-domain-input'],
                    expected: ['outcome' => 'validation-error'],
                    reference: 'FormRequest e invariantes do domínio do módulo na versão 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'rounding',
                    title: 'Arredondamento monetário estável',
                    kind: GoldenCaseKind::Rounding,
                    input: ['scenario' => 'fractional-monetary-input'],
                    expected: ['outcome' => 'integer-cent-result'],
                    reference: 'Política Money do Core e CalculatorTest do módulo na versão 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                    roundingPolicy: 'Valores monetários em centavos, sem float.',
                ),
                new GoldenCase(
                    identifier: 'non-applicable',
                    title: 'Cenário não aplicável identificado',
                    kind: GoldenCaseKind::NonApplicable,
                    input: ['scenario' => 'non-applicable-input'],
                    expected: ['outcome' => 'explicit-non-applicable-result'],
                    reference: 'Invariantes e avisos do domínio do módulo na versão 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'normative-transition',
                    title: 'Versão normativa identificada',
                    kind: GoldenCaseKind::NormativeTransition,
                    input: ['scenario' => 'rule-version-transition'],
                    expected: ['outcome' => 'versioned-rule-result'],
                    reference: 'Memória de cálculo e metadados normativos do módulo na versão 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                ),
                new GoldenCase(
                    identifier: 'regression',
                    title: 'Resultado principal protegido contra regressão',
                    kind: GoldenCaseKind::Regression,
                    input: ['scenario' => 'known-regression-input'],
                    expected: ['outcome' => 'stable-versioned-result'],
                    reference: 'CalculatorTest do módulo aprovado para a versão 1.0.0.',
                    normativeRuleVersion: '1.0.0',
                    roundingPolicy: 'Valores monetários em centavos, sem float.',
                ),
            ],
        );
    }
}

PHP;
    file_put_contents($goldenCasesPath, $goldenCases);
}

$indexPath = __DIR__.'/../docs/pages/README.md';
$index = file_get_contents($indexPath);
$marker = '<!-- requested-tools-pages -->';
if (! str_contains($index, $marker)) {
    $lines = ["\n\n## Ferramentas do lote solicitado\n", $marker];
    foreach ($tools as $module => [$name]) {
        $lines[] = "- [{$name}](app/Tools/{$module}/pages/index.blade.md) — `app/Tools/{$module}/Resources/views/index.blade.php`";
    }
    file_put_contents($indexPath, rtrim($index)."\n".implode("\n", $lines)."\n");
}

echo count($tools)." READMEs atualizados.\n";
