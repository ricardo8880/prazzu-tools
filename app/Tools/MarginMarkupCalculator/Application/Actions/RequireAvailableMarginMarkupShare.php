<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use App\Tools\MarginMarkupCalculator\Infrastructure\Models\MarginMarkupShare;
use App\Tools\MarginMarkupCalculator\Infrastructure\Repositories\EloquentMarginMarkupShareRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

final readonly class RequireAvailableMarginMarkupShare
{
    public function __construct(private EloquentMarginMarkupShareRepository $shares) {}

    public function execute(string $token): MarginMarkupShare
    {
        $share = $this->shares->findByTokenOrFail($token);

        if (! $share->isAvailable()) {
            throw new HttpException(410, 'Este link expirou ou foi revogado.');
        }

        return $share;
    }
}
