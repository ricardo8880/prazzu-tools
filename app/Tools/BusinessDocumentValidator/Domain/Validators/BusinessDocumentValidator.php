<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Validators;

use App\Core\Identifiers\Cnpj;
use App\Core\Identifiers\Cpf;
use App\Core\Identifiers\Digits;
use App\Tools\BusinessDocumentValidator\Domain\Data\DocumentValidationResult;
use App\Tools\BusinessDocumentValidator\Domain\Enums\DocumentType;

final class BusinessDocumentValidator
{
    public const RULE_VERSION = '2026.07';

    public function validate(string $document, DocumentType $requestedType): DocumentValidationResult
    {
        $digits = Digits::only($document);
        $automaticallyDetected = $requestedType === DocumentType::Automatic;
        $type = $automaticallyDetected ? $this->detectType($digits) : $requestedType;

        return match ($type) {
            DocumentType::Cpf => $this->validateCpf($digits, $automaticallyDetected),
            DocumentType::Cnpj => $this->validateCnpj($digits, $automaticallyDetected),
            DocumentType::Automatic => $this->undetected($digits),
        };
    }

    private function detectType(string $digits): DocumentType
    {
        return match (strlen($digits)) {
            11 => DocumentType::Cpf,
            14 => DocumentType::Cnpj,
            default => DocumentType::Automatic,
        };
    }

    private function validateCpf(string $digits, bool $automaticallyDetected): DocumentValidationResult
    {
        $valid = Cpf::isValid($digits);

        return new DocumentValidationResult(
            type: DocumentType::Cpf,
            digits: $digits,
            formatted: $valid ? Cpf::fromString($digits)->formatted() : $this->formatCpfWhenPossible($digits),
            valid: $valid,
            automaticallyDetected: $automaticallyDetected,
            messages: $this->messagesFor(DocumentType::Cpf, $digits, $valid, 11, $automaticallyDetected),
        );
    }

    private function validateCnpj(string $digits, bool $automaticallyDetected): DocumentValidationResult
    {
        $valid = Cnpj::isValid($digits);

        return new DocumentValidationResult(
            type: DocumentType::Cnpj,
            digits: $digits,
            formatted: $valid ? Cnpj::fromString($digits)->formatted() : $this->formatCnpjWhenPossible($digits),
            valid: $valid,
            automaticallyDetected: $automaticallyDetected,
            messages: $this->messagesFor(DocumentType::Cnpj, $digits, $valid, 14, $automaticallyDetected),
        );
    }

    private function undetected(string $digits): DocumentValidationResult
    {
        return new DocumentValidationResult(
            type: DocumentType::Automatic,
            digits: $digits,
            formatted: $digits,
            valid: false,
            automaticallyDetected: true,
            messages: [
                'Não foi possível identificar o tipo pelo tamanho do número.',
                'Um CPF deve possuir 11 dígitos e um CNPJ deve possuir 14 dígitos.',
            ],
        );
    }

    /** @return list<string> */
    private function messagesFor(
        DocumentType $type,
        string $digits,
        bool $valid,
        int $expectedLength,
        bool $automaticallyDetected,
    ): array {
        $messages = [];

        if ($automaticallyDetected) {
            $messages[] = sprintf('O documento foi identificado automaticamente como %s.', $type->label());
        }

        if (strlen($digits) !== $expectedLength) {
            $messages[] = sprintf(
                'O %s informado possui %d dígitos; o esperado é %d.',
                $type->label(),
                strlen($digits),
                $expectedLength,
            );

            return $messages;
        }

        $messages[] = $valid
            ? sprintf('Os dígitos verificadores do %s são válidos.', $type->label())
            : sprintf('Os dígitos verificadores do %s não conferem.', $type->label());

        if (! $valid && $digits !== '' && count(array_unique(str_split($digits))) === 1) {
            $messages[] = 'Sequências formadas por um único dígito repetido não são documentos válidos.';
        }

        return $messages;
    }

    private function formatCpfWhenPossible(string $digits): string
    {
        if (strlen($digits) !== 11) {
            return $digits;
        }

        return substr($digits, 0, 3).'.'.substr($digits, 3, 3).'.'.substr($digits, 6, 3).'-'.substr($digits, 9, 2);
    }

    private function formatCnpjWhenPossible(string $digits): string
    {
        if (strlen($digits) !== 14) {
            return $digits;
        }

        return substr($digits, 0, 2).'.'.substr($digits, 2, 3).'.'.substr($digits, 5, 3).'/'.substr($digits, 8, 4).'-'.substr($digits, 12, 2);
    }
}
