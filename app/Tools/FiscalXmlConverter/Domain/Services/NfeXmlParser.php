<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Domain\Services;

use App\Tools\FiscalXmlConverter\Domain\Data\FiscalDocument;
use App\Tools\FiscalXmlConverter\Domain\Data\FiscalItem;
use App\Tools\FiscalXmlConverter\Domain\Data\FiscalParty;
use App\Tools\FiscalXmlConverter\Domain\Exceptions\InvalidFiscalXml;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

final class NfeXmlParser
{
    public const MAX_XML_BYTES = 10_485_760;

    public function parse(string $xml): FiscalDocument
    {
        if ($xml === '' || strlen($xml) > self::MAX_XML_BYTES) {
            throw new InvalidFiscalXml('O XML está vazio ou excede o limite de 10 MB.');
        }
        if (preg_match('/<!DOCTYPE|<!ENTITY/i', $xml) === 1) {
            throw new InvalidFiscalXml('DOCTYPE e entidades externas não são permitidos.');
        }

        $previous = libxml_use_internal_errors(true);
        $document = new DOMDocument();
        $loaded = $document->loadXML($xml, LIBXML_NONET | LIBXML_NOBLANKS | LIBXML_NOCDATA | LIBXML_COMPACT);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        if (! $loaded) {
            throw new InvalidFiscalXml('O arquivo não contém um XML válido.');
        }

        $xpath = new DOMXPath($document);
        $infNfe = $xpath->query('//*[local-name()="infNFe"]')->item(0);
        if (! $infNfe instanceof DOMElement) {
            throw new InvalidFiscalXml('O XML não contém uma NF-e ou NFC-e reconhecida.');
        }

        $model = $this->text($xpath, './/*[local-name()="ide"]/*[local-name()="mod"]', $infNfe);
        if (! in_array($model, ['55', '65'], true)) {
            throw new InvalidFiscalXml('Neste lote são aceitos apenas documentos modelo 55 ou 65.');
        }

        $items = [];
        foreach ($xpath->query('.//*[local-name()="det"]', $infNfe) ?: [] as $det) {
            if (! $det instanceof DOMElement) continue;
            $product = $xpath->query('./*[local-name()="prod"]', $det)->item(0);
            if (! $product instanceof DOMElement) continue;
            $items[] = new FiscalItem(
                number: max(1, (int) $det->getAttribute('nItem')),
                code: $this->text($xpath, './*[local-name()="cProd"]', $product),
                description: $this->text($xpath, './*[local-name()="xProd"]', $product),
                ncm: $this->nullable($this->text($xpath, './*[local-name()="NCM"]', $product)),
                cfop: $this->nullable($this->text($xpath, './*[local-name()="CFOP"]', $product)),
                unit: $this->text($xpath, './*[local-name()="uCom"]', $product),
                quantity: $this->decimal($this->text($xpath, './*[local-name()="qCom"]', $product)),
                unitValue: $this->decimal($this->text($xpath, './*[local-name()="vUnCom"]', $product)),
                totalValue: $this->decimal($this->text($xpath, './*[local-name()="vProd"]', $product)),
                taxes: $this->extractTaxes($xpath, $det),
            );
        }

        if ($items === []) throw new InvalidFiscalXml('A nota não possui itens de produto reconhecíveis.');

        $issuer = $xpath->query('.//*[local-name()="emit"]', $infNfe)->item(0);
        $recipient = $xpath->query('.//*[local-name()="dest"]', $infNfe)->item(0);
        $id = preg_replace('/^NFe/', '', $infNfe->getAttribute('Id')) ?: '';
        $warnings = [];
        if ($id === '' || strlen($id) !== 44) $warnings[] = 'A chave de acesso não foi localizada ou não possui 44 dígitos.';

        return new FiscalDocument(
            model: $model,
            accessKey: $id,
            number: $this->text($xpath, './/*[local-name()="ide"]/*[local-name()="nNF"]', $infNfe),
            series: $this->text($xpath, './/*[local-name()="ide"]/*[local-name()="serie"]', $infNfe),
            issuedAt: $this->nullable($this->text($xpath, './/*[local-name()="ide"]/*[local-name()="dhEmi" or local-name()="dEmi"]', $infNfe)),
            issuer: $this->party($xpath, $issuer),
            recipient: $this->party($xpath, $recipient),
            totals: $this->extractTotals($xpath, $infNfe),
            items: $items,
            warnings: $warnings,
        );
    }

    private function party(DOMXPath $xpath, ?DOMNode $node): FiscalParty
    {
        if (! $node) return new FiscalParty('', null, null);
        $taxId = $this->text($xpath, './*[local-name()="CNPJ" or local-name()="CPF"]', $node);
        return new FiscalParty(
            $this->text($xpath, './*[local-name()="xNome"]', $node),
            $this->nullable($taxId),
            $this->nullable($this->text($xpath, './*[local-name()="IE"]', $node)),
        );
    }

    private function extractTotals(DOMXPath $xpath, DOMNode $context): array
    {
        $total = $xpath->query('.//*[local-name()="ICMSTot"]', $context)->item(0);
        $map = ['products'=>'vProd','freight'=>'vFrete','discount'=>'vDesc','icms'=>'vICMS','ipi'=>'vIPI','pis'=>'vPIS','cofins'=>'vCOFINS','document'=>'vNF'];
        $values = [];
        foreach ($map as $key => $tag) $values[$key] = $this->decimal($this->text($xpath, './*[local-name()="'.$tag.'"]', $total));
        return $values;
    }

    private function extractTaxes(DOMXPath $xpath, DOMNode $context): array
    {
        $map = ['icms'=>'vICMS','ipi'=>'vIPI','pis'=>'vPIS','cofins'=>'vCOFINS'];
        $values = [];
        foreach ($map as $key => $tag) $values[$key] = $this->decimal($this->text($xpath, './/*[local-name()="'.$tag.'"]', $context));
        return $values;
    }

    private function text(DOMXPath $xpath, string $query, ?DOMNode $context = null): string
    {
        if (! $context) return '';
        return trim((string) $xpath->evaluate('string('.$query.')', $context));
    }

    private function nullable(string $value): ?string { return $value === '' ? null : $value; }
    private function decimal(string $value): string { return is_numeric($value) ? $value : '0'; }
}
