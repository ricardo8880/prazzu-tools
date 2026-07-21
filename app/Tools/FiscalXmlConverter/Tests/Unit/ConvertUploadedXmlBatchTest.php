<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Tests\Unit;

use App\Tools\FiscalXmlConverter\Application\Actions\ConvertUploadedXmlBatch;
use App\Tools\FiscalXmlConverter\Domain\Services\NfeXmlParser;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\TestCase;

final class ConvertUploadedXmlBatchTest extends TestCase
{
    public function test_batch_consolidates_valid_documents_and_reports_invalid_files(): void
    {
        $xml = '<?xml version="1.0"?><nfeProc><NFe><infNFe Id="NFe'.str_repeat('1', 44).'"><ide><mod>55</mod><serie>1</serie><nNF>1</nNF></ide><emit><CNPJ>12345678000199</CNPJ><xNome>Emitente</xNome></emit><dest/><det nItem="1"><prod><cProd>A</cProd><xProd>Produto</xProd><NCM>01012100</NCM><CFOP>5102</CFOP><uCom>UN</uCom><qCom>1</qCom><vUnCom>10.00</vUnCom><vProd>10.00</vProd></prod><imposto/></det><total><ICMSTot><vProd>10.00</vProd><vNF>10.00</vNF></ICMSTot></total></infNFe></NFe></nfeProc>';
        $action = new ConvertUploadedXmlBatch(new NfeXmlParser());

        $result = $action->execute([
            UploadedFile::fake()->createWithContent('valido.xml', $xml),
            UploadedFile::fake()->createWithContent('invalido.xml', '<xml>'),
        ]);

        self::assertSame(2, $result['summary']['received']);
        self::assertSame(1, $result['summary']['processed']);
        self::assertSame(1, $result['summary']['failed']);
        self::assertSame('10.00', $result['summary']['document_total']);
    }
}
