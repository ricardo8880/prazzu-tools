<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Tests\Unit;

use App\Tools\FiscalXmlConverter\Domain\Exceptions\InvalidFiscalXml;
use App\Tools\FiscalXmlConverter\Domain\Services\NfeXmlParser;
use PHPUnit\Framework\TestCase;

final class NfeXmlParserTest extends TestCase
{
    public function test_it_extracts_a_minimal_nfe(): void
    {
        $xml = <<<'XML'
<NFe xmlns="http://www.portalfiscal.inf.br/nfe"><infNFe Id="NFe35123456789012345678550010000000011000000010"><ide><mod>55</mod><serie>1</serie><nNF>1</nNF><dhEmi>2026-07-21T09:00:00-03:00</dhEmi></ide><emit><CNPJ>12345678000190</CNPJ><xNome>Emitente</xNome><IE>123</IE></emit><dest><CPF>12345678909</CPF><xNome>Destinatário</xNome></dest><det nItem="1"><prod><cProd>A1</cProd><xProd>Produto</xProd><NCM>01012100</NCM><CFOP>5102</CFOP><uCom>UN</uCom><qCom>2.0000</qCom><vUnCom>50.0000</vUnCom><vProd>100.00</vProd></prod><imposto><ICMS><ICMS00><vICMS>18.00</vICMS></ICMS00></ICMS><PIS><PISAliq><vPIS>1.65</vPIS></PISAliq></PIS><COFINS><COFINSAliq><vCOFINS>7.60</vCOFINS></COFINSAliq></COFINS></imposto></det><total><ICMSTot><vProd>100.00</vProd><vFrete>0</vFrete><vDesc>0</vDesc><vICMS>18.00</vICMS><vIPI>0</vIPI><vPIS>1.65</vPIS><vCOFINS>7.60</vCOFINS><vNF>100.00</vNF></ICMSTot></total></infNFe></NFe>
XML;
        $result = (new NfeXmlParser())->parse($xml);
        self::assertSame('55', $result->model);
        self::assertSame('1', $result->number);
        self::assertCount(1, $result->items);
        self::assertSame('01012100', $result->items[0]->ncm);
        self::assertSame('100.00', $result->totals['document']);
    }

    public function test_it_rejects_doctype(): void
    {
        $this->expectException(InvalidFiscalXml::class);
        (new NfeXmlParser())->parse('<!DOCTYPE NFe [<!ENTITY xxe SYSTEM "file:///etc/passwd">]><NFe>&xxe;</NFe>');
    }

    public function test_it_rejects_unsupported_model(): void
    {
        $this->expectException(InvalidFiscalXml::class);
        (new NfeXmlParser())->parse('<NFe><infNFe Id="NFe1"><ide><mod>57</mod></ide><det nItem="1"><prod><cProd>1</cProd><xProd>X</xProd><uCom>UN</uCom><qCom>1</qCom><vUnCom>1</vUnCom><vProd>1</vProd></prod></det></infNFe></NFe>');
    }
}
