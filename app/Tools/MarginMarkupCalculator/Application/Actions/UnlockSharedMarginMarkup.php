<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use App\Tools\MarginMarkupCalculator\Infrastructure\Models\MarginMarkupShare;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final readonly class UnlockSharedMarginMarkup
{
    public function __construct(
        private RequireAvailableMarginMarkupShare $availableShare,
    ) {}

    public function execute(string $token, string $accessCode): MarginMarkupShare
    {
        $share = $this->availableShare->execute($token);

        if (
            ! $share->isProtected()
            || Hash::check($accessCode, (string) $share->access_code_hash)
        ) {
            return $share;
        }

        throw ValidationException::withMessages([
            'access_code' => 'Código de acesso inválido.',
        ]);
    }
}
