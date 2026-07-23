<?php

namespace Tests\Unit\Core\Tools\Api;

use App\Core\Tools\Api\Exceptions\UnsupportedToolApiResult;
use App\Core\Tools\Api\Services\ToolApiResultNormalizer;
use PHPUnit\Framework\TestCase;

final class ToolApiResultNormalizerTest extends TestCase
{
    public function test_it_accepts_arrays_and_null(): void
    {
        $normalizer = new ToolApiResultNormalizer;

        $this->assertSame(['total' => 10], $normalizer->normalize(['total' => 10]));
        $this->assertSame([], $normalizer->normalize(null));
    }

    public function test_it_rejects_scalar_results(): void
    {
        $this->expectException(UnsupportedToolApiResult::class);

        (new ToolApiResultNormalizer)->normalize('invalid');
    }
}
