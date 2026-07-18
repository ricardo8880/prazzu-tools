<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use App\Core\Tools\History\Models\ToolRun;
use App\Tools\MarginMarkupCalculator\Infrastructure\Models\MarginMarkupShare;
use App\Tools\MarginMarkupCalculator\Infrastructure\Repositories\EloquentMarginMarkupShareRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

final readonly class CreateMarginMarkupShare
{
    public function __construct(
        private RequireOwnedMarginMarkupRun $ownedRun,
        private EloquentMarginMarkupShareRepository $shares,
    ) {}

    public function execute(
        ToolRun $run,
        int $userId,
        int $validityDays,
        ?string $accessCode,
    ): MarginMarkupShare {
        $run = $this->ownedRun->execute($run, $userId);

        if (($run->result_payload['calculation_type'] ?? 'single') !== 'single') {
            throw new HttpException(422, 'O compartilhamento está disponível para cálculos individuais.');
        }

        return $this->shares->createOrReplaceActive(
            toolRunId: (string) $run->getKey(),
            userId: $userId,
            token: (string) Str::uuid(),
            accessCodeHash: $accessCode !== null ? Hash::make($accessCode) : null,
            expiresAt: now()->addDays($validityDays),
        );
    }
}
