<?php

declare(strict_types=1);

namespace App\Tools\DigitalCertificateAnalyzer\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: substitua por fonte oficial, cálculo revisado ou caso aprovado.';

    public static function suite(): GoldenCaseSuite
    {
        return new GoldenCaseSuite('analisador-certificado-digital-a1', [
            new GoldenCase('valid-pkcs12', 'PKCS#12 autoassinado de teste abre com a senha correta', GoldenCaseKind::Typical, ['fixture' => 'certificate-e2e.p12', 'password' => '[segredo-de-teste]'], ['status' => 'valid', 'document_type' => 'CNPJ'], 'Fixture criptográfica fictícia gerada pelo projeto com OpenSSL; identidade e senha são exclusivas de teste.'),
            new GoldenCase('near-expiry-boundary', 'A faixa de aviso começa em 30 dias restantes', GoldenCaseKind::Boundary, ['days_remaining' => 30], ['status' => 'expiring_soon'], 'Regra de produto do Prazzu Tools v1.0: alerta operacional a 30 dias do fim do período X.509.'),
            new GoldenCase('wrong-password', 'Senha incorreta não expõe detalhes internos do OpenSSL', GoldenCaseKind::InvalidInput, ['fixture' => 'certificate-e2e.p12', 'password' => '[incorreta]'], ['validation_error' => true], 'Contrato de segurança do módulo: falha segura e mensagem controlada.'),
        ]);
    }
}
