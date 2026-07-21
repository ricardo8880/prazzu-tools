<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';
    private const REFERENCE = 'Manual de Orientação do Contribuinte NF-e/NFC-e e política de segurança XML do módulo. Casos revisados internamente.';

    public static function suite(): GoldenCaseSuite
    {
        return new GoldenCaseSuite('conversor-fiscal-xml', [
            new GoldenCase('nfe-55-minima', 'NF-e modelo 55 com um item', GoldenCaseKind::Typical, ['fixture' => 'nfe55-minimal.xml'], ['model' => '55', 'items' => 1, 'document_total' => '100.00'], self::REFERENCE),
            new GoldenCase('nfce-65-minima', 'NFC-e modelo 65 com um item', GoldenCaseKind::Boundary, ['fixture' => 'nfce65-minimal.xml'], ['model' => '65', 'items' => 1], self::REFERENCE),
            new GoldenCase('doctype-rejeitado', 'XML com DTD', GoldenCaseKind::InvalidInput, ['xml' => '<!DOCTYPE nfe><NFe/>'], ['exception' => 'InvalidFiscalXml'], self::REFERENCE),
            new GoldenCase('decimal-preserved', 'Valor decimal permanece textual sem float', GoldenCaseKind::Rounding, ['fixture' => 'nfe55-minimal.xml', 'field' => 'document_total'], ['document_total' => '100.00'], self::REFERENCE),
            new GoldenCase('unsupported-model', 'Modelo fiscal fora do escopo', GoldenCaseKind::NonApplicable, ['xml' => '<NFe><infNFe><ide><mod>57</mod></ide></infNFe></NFe>'], ['exception' => 'UnsupportedFiscalDocument'], self::REFERENCE),
            new GoldenCase('nfe-to-nfce-model-transition', 'Transição entre modelos 55 e 65 suportados', GoldenCaseKind::NormativeTransition, ['fixtures' => ['nfe55-minimal.xml', 'nfce65-minimal.xml']], ['models' => ['55', '65']], self::REFERENCE),
            new GoldenCase('secure-parser-regression', 'Parser continua bloqueando entidades externas', GoldenCaseKind::Regression, ['xml' => '<!DOCTYPE nfe [<!ENTITY xxe SYSTEM "file:///etc/passwd">]><NFe>&xxe;</NFe>'], ['exception' => 'InvalidFiscalXml'], self::REFERENCE),
        ]);
    }
}
