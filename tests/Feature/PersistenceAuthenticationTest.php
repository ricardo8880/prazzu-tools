<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PersistenceAuthenticationTest extends TestCase
{
    public function test_guest_is_redirected_only_when_accessing_persistent_history(): void
    {
        $this->get(route('tools.calculadora-de-rescisao.history.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_can_open_tools_without_authentication(): void
    {
        $this->get(route('tools.calculadora-de-rescisao.index'))->assertOk();
        $this->get(route('tools.calculadora-simples-nacional.index'))->assertOk();
    }
}
