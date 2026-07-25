<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Imports;

use App\Core\Imports\Data\TabularDataset;
use App\Core\Imports\Infrastructure\CacheImportDatasetStore;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Encryption\Encrypter;
use PHPUnit\Framework\TestCase;

final class CacheImportDatasetStoreTest extends TestCase
{
    public function test_import_preview_is_encrypted_and_scoped_to_the_owner(): void
    {
        $cache = new Repository(new ArrayStore);
        $store = new CacheImportDatasetStore(
            $cache,
            new Encrypter(random_bytes(32), 'AES-256-CBC'),
        );
        $dataset = new TabularDataset(
            ['Nome', 'Salário'],
            [['Nome' => 'Ana Confidencial', 'Salário' => '5000,00']],
            'funcionarios.csv',
            'csv',
        );

        $token = $store->put($dataset, 'user:10');
        $raw = $cache->get('imports:'.hash('sha256', 'user:10|'.$token));

        self::assertIsString($raw);
        self::assertStringNotContainsString('Ana Confidencial', $raw);
        self::assertNull($store->get($token, 'user:11'));
        self::assertSame('Ana Confidencial', $store->get($token, 'user:10')?->rows[0]['Nome']);
    }
}
