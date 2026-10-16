<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

final class BatchGenerationTest extends TestCase
{
    public function test_batch_page_is_public(): void
    {
        $this->get(route('tools.receipt-generator.batch.index'))->assertOk()->assertSee('Batch receipt generation');
    }

    #[CoversPlusFeature('receipt-generator', 'batch_generation')]
    public function test_valid_csv_generates_printable_receipts(): void
    {
        $csv = "number;payer_name;payee_name;amount;description;issued_at;city\nR-1;Test Client;Test Provider;100.00;Professional services;2026-07-23;New York\n";
        $file = UploadedFile::fake()->createWithContent('receipts.csv', $csv);

        $this->post(route('tools.receipt-generator.batch.issue'), ['file' => $file])
            ->assertOk()->assertSee('Batch receipts')->assertSee('R-1')->assertSee('Test Client');
    }
}
