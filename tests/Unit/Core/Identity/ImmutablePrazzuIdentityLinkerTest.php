<?php

namespace Tests\Unit\Core\Identity;

use App\Core\Identity\Services\ImmutablePrazzuIdentityLinker;
use App\Models\User;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ImmutablePrazzuIdentityLinkerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_links_a_local_user_to_an_external_prazzu_account(): void
    {
        $user = User::factory()->create();

        $linked = (new ImmutablePrazzuIdentityLinker)->link($user, 'prazzu-account-001');

        $this->assertSame('prazzu-account-001', $linked->prazzu_account_id);
    }

    public function test_it_does_not_replace_an_existing_link_automatically(): void
    {
        $user = User::factory()->linkedToPrazzu('prazzu-account-001')->create();

        $this->expectException(DomainException::class);

        (new ImmutablePrazzuIdentityLinker)->link($user, 'prazzu-account-002');
    }
}
