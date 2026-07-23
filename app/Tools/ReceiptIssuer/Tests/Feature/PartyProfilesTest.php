<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Tests\Feature;

use App\Models\User;
use App\Tools\ReceiptIssuer\Infrastructure\Models\ReceiptPartyProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PartyProfilesTest extends TestCase
{
    use RefreshDatabase;

    public function test_profiles_require_authentication(): void
    {
        $this->get(route('tools.emissor-de-recibos.profiles.index'))->assertRedirect();
    }

    public function test_authenticated_user_can_save_and_delete_an_owned_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('tools.emissor-de-recibos.profiles.store'), [
            'party_type' => 'payer',
            'label' => 'Cliente mensal',
            'name' => 'Empresa Exemplo Ltda',
            'document_type' => 'cnpj',
            'document' => '11222333000181',
        ])->assertSessionHasNoErrors();

        $profile = ReceiptPartyProfile::query()->where('user_id', $user->id)->firstOrFail();
        self::assertSame('11222333000181', $profile->document);
        self::assertNotSame('11222333000181', (string) $profile->getRawOriginal('document'));

        $this->delete(route('tools.emissor-de-recibos.profiles.destroy', $profile))->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('receipt_party_profiles', ['id' => $profile->id]);
    }

    public function test_user_cannot_use_another_users_profile(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $profile = ReceiptPartyProfile::query()->create([
            'user_id' => $owner->id,
            'party_type' => 'payee',
            'label' => 'Minha empresa',
            'name' => 'Empresa Segura Ltda',
        ]);

        $this->actingAs($other)->post(route('tools.emissor-de-recibos.profiles.use', $profile))->assertNotFound();
    }
}
