<?php

declare(strict_types=1);

namespace App\Core\Export\Services;

use App\Core\Money\Money;
use Throwable;

final class HumanReadableExportPresenter
{
    /** @var array<string, string> */
    private const LABELS = [
        'name' => 'Nome',
        'beneficiary' => 'Beneficiário',
        'document' => 'Documento',
        'payer' => 'Fonte pagadora',
        'year' => 'Ano-calendário',
        'competence' => 'Competência',
        'base_salary' => 'Salário-base',
        'taxable_additional_earnings' => 'Proventos tributáveis adicionais',
        'non_taxable_earnings' => 'Proventos não tributáveis',
        'dependents' => 'Dependentes para IRRF',
        'judicial_pension' => 'Pensão alimentícia dedutível',
        'transport_discount' => 'Desconto de vale-transporte',
        'meal_discount' => 'Desconto de alimentação/refeição',
        'health_plan_discount' => 'Desconto de plano de saúde',
        'other_discounts' => 'Outros descontos',
        'gross' => 'Rendimentos brutos',
        'inss' => 'INSS',
        'irrf' => 'IRRF',
        'other_deductions' => 'Outras deduções',
        'title' => 'Título',
        'declaration' => 'Declaração',
        'purpose' => 'Finalidade',
        'limitations' => 'Limitações importantes',
        'requires_review' => 'Revisão necessária',
        'requires_signature' => 'Assinatura necessária',
        'authenticity_validated' => 'Autenticidade validada pela plataforma',
        'taxable_gross_minor' => 'Remuneração tributável',
        'total_earnings_minor' => 'Proventos totais',
        'social_security_base_minor' => 'Base de cálculo do INSS',
        'inss_minor' => 'INSS',
        'legal_irrf_deductions_minor' => 'Deduções legais do IRRF',
        'simplified_irrf_deduction_minor' => 'Desconto simplificado mensal do IRRF',
        'irrf_deduction_method' => 'Método de dedução do IRRF',
        'irrf_base_minor' => 'Base de cálculo do IRRF',
        'irrf_before_reduction_minor' => 'IRRF antes da redução',
        'irrf_reduction_minor' => 'Redução mensal do IRRF',
        'user_discounts_minor' => 'Descontos informados pelo usuário',
        'total_discounts_minor' => 'Descontos totais',
        'net_minor' => 'Valor líquido',
        'gross_minor' => 'Valor bruto',
        'deductions_minor' => 'Deduções totais',
        'other_minor' => 'Outras deduções',
        'input' => 'Dados utilizados no cálculo',
        'notice' => 'Avisos e condições de uso',
    ];

    /** @param array<string, mixed> $data @return list<array{label:string,value:string,level:int}> */
    public function rows(array $data, bool $skipDuplicatedInput = false): array
    {
        $rows = [];
        $this->flatten($data, $rows, 0, $skipDuplicatedInput);

        return $rows;
    }

    /** @param array<string, mixed> $memory @return list<array{label:string,formula:string,result:string}> */
    public function memoryRows(array $memory): array
    {
        $rows = [];

        foreach (($memory['steps'] ?? []) as $step) {
            if (! is_array($step)) {
                continue;
            }

            $inputs = is_array($step['inputs'] ?? null) ? $step['inputs'] : [];
            $result = $step['result'] ?? null;
            $monetary = $this->containsMinorAmount($inputs)
                || str_contains((string) ($step['key'] ?? ''), 'amount')
                || str_contains((string) ($step['key'] ?? ''), 'net')
                || str_contains((string) ($step['key'] ?? ''), 'deduction');

            $rows[] = [
                'label' => (string) ($step['label'] ?? ''),
                'formula' => (string) ($step['formula'] ?? ''),
                'result' => $monetary && is_numeric($result)
                    ? $this->formatMinor((int) $result)
                    : $this->formatValue((string) ($step['key'] ?? ''), $result),
            ];
        }

        return $rows;
    }

    public function label(string $key): string
    {
        if (isset(self::LABELS[$key])) {
            return self::LABELS[$key];
        }

        $clean = preg_replace('/_minor$/', '', $key) ?? $key;
        $clean = str_replace(['_', '.'], ' ', $clean);
        $clean = preg_replace('/(?<!^)([A-Z])/', ' $1', $clean) ?? $clean;

        $translations = [
            'base' => 'base', 'salary' => 'salário', 'taxable' => 'tributável',
            'additional' => 'adicional', 'earnings' => 'proventos', 'non' => 'não',
            'dependents' => 'dependentes', 'judicial' => 'judicial', 'pension' => 'pensão',
            'transport' => 'transporte', 'meal' => 'alimentação', 'health' => 'saúde',
            'plan' => 'plano', 'discount' => 'desconto', 'discounts' => 'descontos',
            'gross' => 'bruto', 'net' => 'líquido', 'total' => 'total',
            'social' => 'previdenciária', 'security' => 'social', 'deduction' => 'dedução',
            'deductions' => 'deduções', 'reduction' => 'redução', 'before' => 'antes',
            'method' => 'método', 'legal' => 'legal', 'simplified' => 'simplificado',
            'monthly' => 'mensal', 'value' => 'valor', 'amount' => 'valor',
            'rate' => 'alíquota', 'percentage' => 'percentual', 'date' => 'data',
            'description' => 'descrição', 'status' => 'situação', 'company' => 'empresa',
            'employee' => 'empregado', 'employer' => 'empregador', 'revenue' => 'receita',
            'cost' => 'custo', 'costs' => 'custos', 'tax' => 'tributo', 'taxes' => 'tributos',
        ];

        $words = array_map(
            static fn (string $word): string => $translations[strtolower($word)] ?? $word,
            preg_split('/\s+/', trim($clean)) ?: [],
        );

        return ucfirst(implode(' ', $words));
    }

    public function formatValue(string $key, mixed $value): string
    {
        if ($value === null || $value === '') {
            return 'Não informado';
        }

        if (is_bool($value)) {
            return $value ? 'Sim' : 'Não';
        }

        if ($key === 'competence' && is_string($value) && preg_match('/^(\d{4})-(\d{2})$/', $value, $matches)) {
            $months = [1 => 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];

            return ucfirst($months[(int) $matches[2]] ?? $matches[2]).' de '.$matches[1];
        }

        if (str_ends_with($key, '_minor') && is_numeric($value)) {
            return $this->formatMinor((int) $value);
        }

        if ($key === 'irrf_deduction_method') {
            return match ((string) $value) {
                'legal' => 'Deduções legais',
                'simplified' => 'Desconto simplificado mensal',
                default => (string) $value,
            };
        }

        if ($this->looksMonetary($key) && (is_numeric($value) || (is_string($value) && preg_match('/^-?[\d.]+(?:,\d{1,2})?$/', $value)))) {
            try {
                return Money::fromDecimal((string) $value)->formatPtBr();
            } catch (Throwable) {
                // Mantém o valor original se não representar um decimal monetário válido.
            }
        }

        if (is_array($value)) {
            return implode('; ', array_map(fn (mixed $item): string => $this->formatValue($key, $item), $value));
        }

        if (is_object($value)) {
            return get_debug_type($value);
        }

        return (string) $value;
    }

    /** @param array<string, mixed> $data @param list<array{label:string,value:string,level:int}> $rows */
    private function flatten(array $data, array &$rows, int $level, bool $skipDuplicatedInput): void
    {
        foreach ($data as $key => $value) {
            $key = (string) $key;

            if (is_object($value) && method_exists($value, 'toArray')) {
                $converted = $value->toArray();
                if (is_array($converted)) {
                    $value = $converted;
                }
            }

            if ($skipDuplicatedInput && $key === 'input') {
                continue;
            }

            if (is_array($value)) {
                if ($value === []) {
                    continue;
                }

                if (array_is_list($value) && $this->isScalarList($value)) {
                    foreach ($value as $index => $item) {
                        $rows[] = [
                            'label' => $this->label($key).' '.((int) $index + 1),
                            'value' => $this->formatValue($key, $item),
                            'level' => $level,
                        ];
                    }

                    continue;
                }

                $rows[] = ['label' => $this->label($key), 'value' => '', 'level' => $level];
                $this->flatten($value, $rows, $level + 1, $skipDuplicatedInput);

                continue;
            }

            $rows[] = [
                'label' => $this->label($key),
                'value' => $this->formatValue($key, $value),
                'level' => $level,
            ];
        }
    }

    /** @param array<mixed> $items */
    private function isScalarList(array $items): bool
    {
        foreach ($items as $item) {
            if (! is_scalar($item) && $item !== null) {
                return false;
            }
        }

        return true;
    }

    /** @param array<string, mixed> $data */
    private function containsMinorAmount(array $data): bool
    {
        foreach ($data as $key => $value) {
            if (str_ends_with((string) $key, '_minor')) {
                return true;
            }
            if (is_array($value) && $this->containsMinorAmount($value)) {
                return true;
            }
        }

        return false;
    }

    private function formatMinor(int $minor): string
    {
        return Money::fromMinor($minor)->formatPtBr();
    }

    private function looksMonetary(string $key): bool
    {
        return (bool) preg_match(
            '/(?:salary|earning|discount|pension|gross|net|inss|irrf|deduction|amount|revenue|cost|tax|price|fee|payment|profit|expense|income|value)/',
            $key,
        );
    }
}
