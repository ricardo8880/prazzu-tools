<?php

namespace App\Core\Identity\Services;

use App\Core\Identity\Contracts\PrazzuIdentityLinker;
use App\Models\User;
use DomainException;

final class ImmutablePrazzuIdentityLinker implements PrazzuIdentityLinker
{
    public function link(User $user, string $prazzuAccountId): User
    {
        $normalizedId = trim($prazzuAccountId);

        if ($normalizedId === '') {
            throw new DomainException('O identificador da conta Prazzu não pode ser vazio.');
        }

        if ($user->prazzu_account_id !== null && $user->prazzu_account_id !== $normalizedId) {
            throw new DomainException('A conta Prazzu vinculada não pode ser substituída automaticamente.');
        }

        if ($user->prazzu_account_id === $normalizedId) {
            return $user;
        }

        $user->forceFill(['prazzu_account_id' => $normalizedId])->save();

        return $user->refresh();
    }
}
