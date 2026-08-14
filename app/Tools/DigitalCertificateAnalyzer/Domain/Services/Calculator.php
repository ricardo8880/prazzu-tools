<?php

declare(strict_types=1);

namespace App\Tools\DigitalCertificateAnalyzer\Domain\Services;

use App\Core\Identifiers\Cnpj;
use App\Core\Identifiers\Cpf;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\DigitalCertificateAnalyzer\Application\Data\CalculationInput;
use DateTimeImmutable;
use InvalidArgumentException;
use OpenSSLCertificate;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com o analisador de certificado digital A1.');
        }

        if (! function_exists('openssl_pkcs12_read') || ! function_exists('openssl_x509_parse')) {
            throw new InvalidArgumentException('O servidor não possui suporte OpenSSL necessário para analisar certificados A1.');
        }

        while (openssl_error_string() !== false) {}
        $store = [];
        if (! @openssl_pkcs12_read($input->pkcs12, $store, $input->password)) {
            while (openssl_error_string() !== false) {}
            throw new InvalidArgumentException('Não foi possível abrir o certificado. Confira se o arquivo é .pfx/.p12 válido e se a senha está correta.');
        }

        $certificatePem = (string) ($store['cert'] ?? '');
        $certificate = openssl_x509_read($certificatePem);
        if (! $certificate instanceof OpenSSLCertificate) {
            throw new InvalidArgumentException('O arquivo foi aberto, mas não contém um certificado X.509 legível.');
        }

        $parsed = openssl_x509_parse($certificate, false);
        if (! is_array($parsed)) {
            throw new InvalidArgumentException('Não foi possível interpretar os metadados X.509 do certificado.');
        }

        $validFrom = $this->dateFromTimestamp((int) ($parsed['validFrom_time_t'] ?? 0));
        $validTo = $this->dateFromTimestamp((int) ($parsed['validTo_time_t'] ?? 0));
        $reference = $input->referenceDate;
        [$status, $statusLabel, $daysRemaining] = $this->validity($validFrom, $validTo, $reference);
        $subject = is_array($parsed['subject'] ?? null) ? $parsed['subject'] : [];
        $issuer = is_array($parsed['issuer'] ?? null) ? $parsed['issuer'] : [];
        $document = $this->detectBrazilianDocument($subject, (string) ($parsed['extensions']['subjectAltName'] ?? ''));
        $type = $document['type'] === 'CNPJ' ? 'Provável e-CNPJ' : ($document['type'] === 'CPF' ? 'Provável e-CPF' : 'Não identificado');

        $summary = [
            new ToolCalculationSummaryItem('status', 'Situação temporal', $statusLabel),
            new ToolCalculationSummaryItem('type', 'Tipo identificado', $type, 'A classificação e-CPF/e-CNPJ é inferida somente quando um CPF/CNPJ válido é encontrado nos campos lidos.'),
            new ToolCalculationSummaryItem('holder', 'Titular', $this->firstString($subject['commonName'] ?? $subject['CN'] ?? null) ?: 'Não informado'),
            new ToolCalculationSummaryItem('expires_at', 'Válido até', $validTo?->format('d/m/Y H:i:s') ?? 'Não informado'),
        ];

        $details = [
            'file' => ['name' => $input->originalName, 'size_bytes' => $input->size],
            'status' => $status,
            'days_remaining' => $daysRemaining,
            'valid_from' => $validFrom?->format(DATE_ATOM),
            'valid_to' => $validTo?->format(DATE_ATOM),
            'holder' => [
                'common_name' => $this->firstString($subject['commonName'] ?? $subject['CN'] ?? null),
                'organization' => $this->firstString($subject['organizationName'] ?? $subject['O'] ?? null),
                'organizational_unit' => $this->strings($subject['organizationalUnitName'] ?? $subject['OU'] ?? null),
                'country' => $this->firstString($subject['countryName'] ?? $subject['C'] ?? null),
                'document_type' => $document['type'],
                'document' => $document['formatted'],
            ],
            'issuer' => [
                'common_name' => $this->firstString($issuer['commonName'] ?? $issuer['CN'] ?? null),
                'organization' => $this->firstString($issuer['organizationName'] ?? $issuer['O'] ?? null),
                'country' => $this->firstString($issuer['countryName'] ?? $issuer['C'] ?? null),
            ],
            'chain_certificates_in_file' => count((array) ($store['extracerts'] ?? [])),
        ];

        if ($input->includeTechnicalDetails) {
            $publicKey = openssl_pkey_get_public($certificatePem);
            $keyDetails = $publicKey === false ? [] : (openssl_pkey_get_details($publicKey) ?: []);
            $details['technical'] = [
                'serial_number' => (string) ($parsed['serialNumberHex'] ?? $parsed['serialNumber'] ?? ''),
                'signature_algorithm' => (string) ($parsed['signatureTypeLN'] ?? $parsed['signatureTypeSN'] ?? ''),
                'sha256_fingerprint' => strtoupper(implode(':', str_split((string) openssl_x509_fingerprint($certificate, 'sha256'), 2))),
                'public_key_bits' => (int) ($keyDetails['bits'] ?? 0),
                'public_key_type' => $this->keyType((int) ($keyDetails['type'] ?? -1)),
                'subject_alt_name' => (string) ($parsed['extensions']['subjectAltName'] ?? ''),
            ];
        }

        $warnings = [
            new ToolCalculationWarning('temporal_only', 'O status exibido verifica o período de validade do certificado. Ele não comprova confiança da cadeia, revogação ou aceitação em um serviço externo.', ToolCalculationWarningLevel::Info),
            new ToolCalculationWarning('no_persistence', 'O Prazzu não salva o arquivo nem a senha nesta ferramenta; o conteúdo é usado apenas durante a requisição de análise.', ToolCalculationWarningLevel::Info),
        ];
        if ($status === 'expiring_soon') {
            $warnings[] = new ToolCalculationWarning('expiring_soon', "O certificado vence em {$daysRemaining} dia(s). Planeje a renovação antes do vencimento.");
        } elseif ($status === 'expired') {
            $warnings[] = new ToolCalculationWarning('expired', 'O período de validade informado no certificado já terminou.');
        } elseif ($status === 'not_yet_valid') {
            $warnings[] = new ToolCalculationWarning('not_yet_valid', 'A data inicial de validade do certificado ainda não foi alcançada.');
        }

        return new ToolCalculationResult(
            toolSlug: 'analisador-certificado-digital-a1',
            schemaVersion: '1.0.0',
            summary: $summary,
            details: $details,
            warnings: $warnings,
        );
    }

    private function dateFromTimestamp(int $timestamp): ?DateTimeImmutable
    {
        return $timestamp > 0 ? (new DateTimeImmutable('@'.$timestamp))->setTimezone(new \DateTimeZone('America/Sao_Paulo')) : null;
    }

    /** @return array{0:string,1:string,2:int|null} */
    private function validity(?DateTimeImmutable $from, ?DateTimeImmutable $to, DateTimeImmutable $reference): array
    {
        if ($from === null || $to === null) return ['unknown', 'Indeterminada', null];
        if ($reference < $from) return ['not_yet_valid', 'Ainda não válido', null];
        if ($reference > $to) return ['expired', 'Vencido', 0];
        $days = max(0, (int) $reference->diff($to)->format('%a'));
        return $days <= 30 ? ['expiring_soon', 'Válido — vence em breve', $days] : ['valid', 'Válido no período', $days];
    }

    /** @param array<string,mixed> $subject @return array{type:?string,formatted:?string} */
    private function detectBrazilianDocument(array $subject, string $subjectAltName): array
    {
        $haystack = implode(' ', array_merge($this->flattenStrings($subject), [$subjectAltName]));
        preg_match_all('/(?<!\d)(?:\d[.\/-]?){10,14}\d(?!\d)/', $haystack, $matches);
        foreach ($matches[0] ?? [] as $candidate) {
            $digits = preg_replace('/\D+/', '', $candidate) ?? '';
            if (strlen($digits) === 14 && Cnpj::isValid($digits)) return ['type' => 'CNPJ', 'formatted' => Cnpj::fromString($digits)->formatted()];
            if (strlen($digits) === 11 && Cpf::isValid($digits)) return ['type' => 'CPF', 'formatted' => Cpf::fromString($digits)->formatted()];
        }
        return ['type' => null, 'formatted' => null];
    }

    /** @param array<string,mixed> $values @return list<string> */
    private function flattenStrings(array $values): array
    {
        $out = [];
        array_walk_recursive($values, static function (mixed $value) use (&$out): void { if (is_scalar($value)) $out[] = (string) $value; });
        return $out;
    }

    /** @return list<string> */
    private function strings(mixed $value): array
    {
        if (is_array($value)) return array_values(array_filter(array_map('strval', $value), static fn (string $v): bool => trim($v) !== ''));
        $single = $this->firstString($value);
        return $single === null ? [] : [$single];
    }

    private function firstString(mixed $value): ?string
    {
        if (is_array($value)) $value = reset($value);
        return is_scalar($value) && trim((string) $value) !== '' ? trim((string) $value) : null;
    }

    private function keyType(int $type): string
    {
        return match ($type) {
            OPENSSL_KEYTYPE_RSA => 'RSA',
            OPENSSL_KEYTYPE_DSA => 'DSA',
            OPENSSL_KEYTYPE_DH => 'DH',
            defined('OPENSSL_KEYTYPE_EC') ? OPENSSL_KEYTYPE_EC : -999 => 'EC',
            default => 'Desconhecido',
        };
    }
}
