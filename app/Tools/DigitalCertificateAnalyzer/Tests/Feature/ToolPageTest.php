<?php

declare(strict_types=1);

namespace App\Tools\DigitalCertificateAnalyzer\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    private function upload(): UploadedFile
    {
        $contents = file_get_contents(dirname(__DIR__).'/Fixtures/certificate-e2e.p12');
        self::assertIsString($contents);
        return UploadedFile::fake()->createWithContent('certificado-e2e.p12', $contents);
    }

    public function test_tool_page_is_available_and_explains_no_persistence(): void
    {
        $this->get(route('tools.analisador-certificado-digital-a1.index'))
            ->assertOk()
            ->assertSee('Analisador de Certificado Digital A1')
            ->assertSee('não salva a chave privada');
    }

    public function test_it_analyzes_a_valid_certificate_and_never_echoes_password(): void
    {
        $response = $this->post(route('tools.analisador-certificado-digital-a1.calculate'), ['certificate_file' => $this->upload(), 'password' => 'prazzu-e2e-2026']);
        $response->assertOk()->assertSee('Prazzu E2E LTDA')->assertSee('11.222.333/0001-81')->assertDontSee('prazzu-e2e-2026');
    }

    public function test_wrong_password_returns_validation_error(): void
    {
        $this->from(route('tools.analisador-certificado-digital-a1.index'))
            ->post(route('tools.analisador-certificado-digital-a1.calculate'), ['certificate_file' => $this->upload(), 'password' => 'errada'])
            ->assertSessionHasErrors('certificate_file');
    }

    #[CoversPlusFeature('analisador-certificado-digital-a1', 'technical_report')]
    public function test_plus_technical_report_is_a_real_pdf_and_does_not_include_password(): void
    {
        $response = $this->post(route('tools.analisador-certificado-digital-a1.export'), ['certificate_file' => $this->upload(), 'password' => 'prazzu-e2e-2026']);
        $response->assertOk()->assertHeader('content-type', 'application/pdf');
        self::assertStringNotContainsString('prazzu-e2e-2026', $response->getContent());
    }
}
