<?php

declare(strict_types=1);

namespace App\Core\Export\Services;

use App\Core\Money\Money;
use Throwable;

final class HumanReadableExportPresenter
{
    /** @var array<string, string> */
    private const LABELS = [
        'name' => 'Name', 'beneficiary' => 'Beneficiary', 'document' => 'Document',
        'payer' => 'Payer', 'year' => 'Year', 'competence' => 'Period',
        'base_salary' => 'Base pay', 'gross' => 'Gross amount', 'net' => 'Net amount',
        'federal_income_tax' => 'Federal income tax', 'social_security' => 'Social Security',
        'medicare' => 'Medicare', 'state_local_tax' => 'State/local tax',
        'other_deductions' => 'Other deductions', 'title' => 'Title',
        'declaration' => 'Statement', 'purpose' => 'Purpose', 'limitations' => 'Important limitations',
        'requires_review' => 'Review required', 'requires_signature' => 'Signature required',
        'taxable_gross_minor' => 'Taxable gross', 'total_earnings_minor' => 'Total earnings',
        'social_security_minor' => 'Social Security', 'medicare_minor' => 'Medicare',
        'federal_income_tax_minor' => 'Federal income tax', 'state_local_tax_minor' => 'State/local tax',
        'user_discounts_minor' => 'User-entered deductions', 'total_discounts_minor' => 'Total deductions',
        'net_minor' => 'Net amount', 'gross_minor' => 'Gross amount', 'deductions_minor' => 'Total deductions',
        'other_minor' => 'Other deductions', 'input' => 'Calculation inputs', 'notice' => 'Notices and conditions',
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

        return ucfirst(trim($clean));
    }

    public function formatValue(string $key, mixed $value): string
    {
        if ($value === null || $value === '') {
            return 'Not provided';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }


        if (str_ends_with($key, '_minor') && is_numeric($value)) {
            return $this->formatMinor((int) $value);
        }


        if ($this->looksMonetary($key) && (is_numeric($value) || (is_string($value) && preg_match('/^-?[\d.]+(?:,\d{1,2})?$/', $value)))) {
            try {
                return Money::fromDecimal((string) $value)->formatEnUs();
            } catch (Throwable) {
                // Keep the original value if it is not a valid monetary decimal.
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
        return Money::fromMinor($minor)->formatEnUs();
    }

    private function looksMonetary(string $key): bool
    {
        return (bool) preg_match(
            '/(?:salary|earning|discount|pension|gross|net|fica|federal_tax|state_tax|deduction|amount|revenue|cost|tax|price|fee|payment|profit|expense|income|value)/',
            $key,
        );
    }
}
