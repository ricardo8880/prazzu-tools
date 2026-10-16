<?php

namespace Tests\Feature\Api;

use App\Core\Tools\Api\Services\ToolApiActionRegistry;
use Tests\TestCase;

final class ToolApiActionsRegistrationTest extends TestCase
{
    public function test_all_registered_tools_publish_a_primary_api_action(): void
    {
        $actions = app(ToolApiActionRegistry::class)->all();

        $this->assertCount(10, $actions);
        $slugs = array_keys($actions);
        sort($slugs);

        $this->assertSame([
            'accounting-fee-calculator',
            'final-pay-severance-calculator',
            'pto-calculator',
            'margin-markup-calculator',
            'us-tax-id-validator',
        ], $slugs);
    }
}
