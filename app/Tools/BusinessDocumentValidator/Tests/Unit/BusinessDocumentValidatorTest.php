<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Tests\Unit;

use App\Tools\BusinessDocumentValidator\Domain\Validators\BusinessDocumentValidator;
use PHPUnit\Framework\TestCase;

final class BusinessDocumentValidatorTest extends TestCase
{
    public function test_it_accepts_basic_ein_format(): void
    {
        $result = (new BusinessDocumentValidator)->validate('12-3456789');
        self::assertTrue($result->valid);
        self::assertSame('12-3456789', $result->formatted);
    }

    public function test_it_rejects_invalid_ein_format(): void
    {
        self::assertFalse((new BusinessDocumentValidator)->validate('123456')->valid);
        self::assertFalse((new BusinessDocumentValidator)->validate('00-0000000')->valid);
    }
}
