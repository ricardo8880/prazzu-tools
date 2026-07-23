<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

final class BatchGenerationTest extends TestCase
{
    public function test_batch_page_is_public(): void
    {
        $this->get(route('tools.emissor-de-recibos.batch.index'))->assertOk()->assertSee('Geração de recibos em lote');
    }

    public function test_valid_csv_generates_printable_receipts(): void
    {
        $csv = "number;payer_name;payee_name;amount;description;issued_at;city\nR-1;Cliente Teste;Prestador Teste;100,00;Serviço prestado;2026-07-23;São Paulo\n";
        $file = UploadedFile::fake()->createWithContent('recibos.csv', $csv);

        $this->post(route('tools.emissor-de-recibos.batch.issue'), ['file' => $file])
            ->assertOk()->assertSee('Recibos em lote')->assertSee('R-1')->assertSee('Cliente Teste');
    }
}
