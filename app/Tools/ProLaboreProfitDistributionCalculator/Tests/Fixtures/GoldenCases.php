<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substituir por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        $official = 'Receita Federal, tabelas mensais do IRPF 2026; INSS, tabela de contribuição mensal 2026; regras documentadas em docs/NORMATIVE_RULES.md.';

        return new GoldenCaseSuite(
            toolSlug: 'calculadora-pro-labore-distribuicao-lucros',
            cases: [
                new GoldenCase('typical', 'Pró-labore de R$ 5.000,00 no Lucro Real', GoldenCaseKind::Typical,
                    ['competence' => '2026-01', 'company_regime' => 'actual_profit', 'gross_minor' => 500000, 'dependents' => 0],
                    ['social_security_minor' => 55000, 'irrf_minor' => 0, 'net_minor' => 445000, 'employer_minor' => 100000, 'company_cost_minor' => 600000],
                    $official, 'pro_labore.social_security:2026.1|pro_labore.monthly_irrf:2026.1', 'HalfUp por operação percentual'),
                new GoldenCase('boundary', 'Pró-labore exatamente no teto previdenciário de 2026', GoldenCaseKind::Boundary,
                    ['competence' => '2026-01', 'company_regime' => 'actual_profit', 'gross_minor' => 847555],
                    ['social_security_minor' => 93231, 'irrf_minor' => 116566, 'net_minor' => 637758, 'company_cost_minor' => 1017066],
                    $official, 'pro_labore.social_security:2026.1|pro_labore.monthly_irrf:2026.1', 'HalfUp por operação percentual'),
                new GoldenCase('invalid-input', 'Distribuição superior ao lucro disponível', GoldenCaseKind::InvalidInput,
                    ['accounting_profit_minor' => 100000, 'intended_distribution_minor' => 100001, 'ownership_millionths' => 100000000],
                    ['exception' => 'InvalidValue', 'message' => 'A distribuição pretendida não pode superar o lucro máximo disponível.'],
                    'Contrato do domínio de distribuição aprovado no README do módulo.'),
                new GoldenCase('rounding', 'Rateio de um centavo entre três sócios', GoldenCaseKind::Rounding,
                    ['available_profit_minor' => 1, 'ownership_millionths' => [33333333, 33333333, 33333334]],
                    ['distributed_minor' => [0, 0, 1], 'total_minor' => 1],
                    'Política determinística de ajuste no último sócio documentada no README do módulo.', null, 'HalfUp e ajuste residual no último item'),
                new GoldenCase('non-applicable', 'Competência sem regra normativa cadastrada', GoldenCaseKind::NonApplicable,
                    ['competence' => '2025-12', 'gross_minor' => 500000],
                    ['exception' => 'InvalidValue', 'reason' => 'competência sem tabela normativa cadastrada'],
                    'docs/NORMATIVE_RULES.md, seção Casos não aplicáveis no primeiro contrato.'),
                new GoldenCase('normative-transition', 'Transição para a primeira competência suportada', GoldenCaseKind::NormativeTransition,
                    ['before' => '2025-12', 'after' => '2026-01', 'gross_minor' => 500000],
                    ['before_supported' => false, 'after_supported' => true, 'after_net_minor' => 445000],
                    $official, '2026.1', 'HalfUp por operação percentual'),
                new GoldenCase('regression', 'Redução parcial mensal do IR em R$ 6.000,00', GoldenCaseKind::Regression,
                    ['competence' => '2026-01', 'company_regime' => 'actual_profit', 'gross_minor' => 600000],
                    ['social_security_minor' => 66000, 'irrf_before_reduction_minor' => 55977, 'irrf_reduction_minor' => 17975, 'irrf_minor' => 38002, 'net_minor' => 495998],
                    $official, 'pro_labore.monthly_irrf:2026.1', 'HalfUp por operação percentual'),
            ],
        );
    }
}
