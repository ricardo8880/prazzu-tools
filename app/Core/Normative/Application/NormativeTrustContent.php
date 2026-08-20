<?php

declare(strict_types=1);

namespace App\Core\Normative\Application;

use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleSnapshot;
use DateTimeImmutable;
use Throwable;

final class NormativeTrustContent
{
    /**
     * @param list<NormativeRuleSnapshot|NormativeRuleMetadata|array<string, mixed>> $rules
     * @param list<string> $assumptions
     * @return array{rules:list<array<string, mixed>>,assumptions:list<string>,is_estimate:bool,source_count:int}|null
     */
    public function for(array $rules, array $assumptions = [], bool $isEstimate = false): ?array
    {
        $normalizedRules = [];
        $sourceUrls = [];

        foreach ($rules as $rule) {
            $normalized = $this->normalizeRule($rule);

            if ($normalized === null) {
                continue;
            }

            foreach ($normalized['references'] as $reference) {
                $url = $reference['official_url'] ?? null;
                if (is_string($url) && $url !== '') {
                    $sourceUrls[$url] = true;
                }
            }

            $normalizedRules[] = $normalized;
        }

        if ($normalizedRules === []) {
            return null;
        }

        $cleanAssumptions = array_values(array_filter(array_map(
            static fn (mixed $assumption): string => is_string($assumption) ? trim($assumption) : '',
            $assumptions,
        ), static fn (string $assumption): bool => $assumption !== ''));

        return [
            'rules' => $normalizedRules,
            'assumptions' => $cleanAssumptions,
            'is_estimate' => $isEstimate,
            'source_count' => count($sourceUrls),
        ];
    }

    /** @return array<string, mixed>|null */
    private function normalizeRule(mixed $rule): ?array
    {
        if ($rule instanceof NormativeRuleSnapshot) {
            $data = $rule->toArray();
        } elseif ($rule instanceof NormativeRuleMetadata) {
            $data = $rule->toArray();
        } elseif (is_array($rule)) {
            $data = $rule;
        } else {
            return null;
        }

        $references = [];
        foreach ((array) ($data['references'] ?? []) as $reference) {
            if (! is_array($reference)) {
                continue;
            }

            $url = $reference['official_url'] ?? null;
            if (! is_string($url) || filter_var($url, FILTER_VALIDATE_URL) === false) {
                continue;
            }

            $references[] = [
                'identifier' => trim((string) ($reference['identifier'] ?? '')),
                'title' => trim((string) ($reference['title'] ?? $reference['identifier'] ?? 'Fonte oficial')),
                'official_url' => $url,
                'article' => $this->nullableString($reference['article'] ?? null),
                'published_at' => $this->formatDate($reference['published_at'] ?? null),
            ];
        }

        if ($references === []) {
            return null;
        }

        return [
            'identifier' => trim((string) ($data['identifier'] ?? '')),
            'version' => trim((string) ($data['version'] ?? '')),
            'reference_date' => $this->formatDate($data['reference_date'] ?? null),
            'effective_from' => $this->formatDate($data['effective_from'] ?? null),
            'effective_until' => $this->formatDate($data['effective_until'] ?? null),
            'verified_at' => $this->formatDate($data['verified_at'] ?? null),
            'verified_by' => $this->nullableString($data['verified_by'] ?? null),
            'references' => $references,
        ];
    }

    private function formatDate(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return (new DateTimeImmutable($value))->format('d/m/Y');
        } catch (Throwable) {
            return trim($value);
        }
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
