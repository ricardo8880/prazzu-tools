<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Tests\Feature;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use Illuminate\Http\UploadedFile;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    use RefreshDatabase;
    public function test_page_is_available(): void
    {
        $this->get(route('tools.conversor-fiscal-xml.index'))
            ->assertOk()
            ->assertSee('Conversor Fiscal de XML')
            ->assertSee('Arquivo XML fiscal');
    }

    public function test_valid_nfe_is_processed_and_shown(): void
    {
        $analytics = Mockery::mock(PlatformAnalytics::class);
        $analytics->shouldReceive('record')->once();
        $analytics->shouldReceive('track')->zeroOrMoreTimes();
        $this->app->instance(PlatformAnalytics::class, $analytics);

        $xml = '<?xml version="1.0"?><nfeProc><NFe><infNFe Id="NFe'.str_repeat('1', 44).'"><ide><mod>55</mod><serie>1</serie><nNF>123</nNF><dhEmi>2026-01-10T10:00:00-03:00</dhEmi></ide><emit><CNPJ>12345678000199</CNPJ><xNome>Emitente Teste</xNome></emit><dest><CPF>12345678900</CPF><xNome>Cliente Teste</xNome></dest><det nItem="1"><prod><cProd>A1</cProd><xProd>Produto Teste</xProd><NCM>01012100</NCM><CFOP>5102</CFOP><uCom>UN</uCom><qCom>2</qCom><vUnCom>10</vUnCom><vProd>20</vProd></prod><imposto><ICMS><ICMS00><vICMS>3.60</vICMS></ICMS00></ICMS></imposto></det><total><ICMSTot><vProd>20</vProd><vFrete>0</vFrete><vDesc>0</vDesc><vICMS>3.60</vICMS><vIPI>0</vIPI><vPIS>0.33</vPIS><vCOFINS>1.52</vCOFINS><vNF>20</vNF></ICMSTot></total></infNFe></NFe></nfeProc>';

        $this->post(route('tools.conversor-fiscal-xml.calculate'), [
            'xml_file' => UploadedFile::fake()->createWithContent('nota.xml', $xml),
        ])->assertRedirect();

        $this->get(route('tools.conversor-fiscal-xml.index'))
            ->assertOk()->assertSee('Emitente Teste')->assertSee('Produto Teste')->assertSee('123');
    }

    public function test_invalid_xml_returns_validation_error(): void
    {
        $this->post(route('tools.conversor-fiscal-xml.calculate'), [
            'xml_file' => UploadedFile::fake()->createWithContent('invalido.xml', '<xml>'),
        ])->assertSessionHasErrors('xml_file');
    }

    public function test_file_is_required(): void
    {
        $this->post(route('tools.conversor-fiscal-xml.calculate'))
            ->assertSessionHasErrors('xml_file');
    }
}
