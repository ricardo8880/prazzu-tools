<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Infrastructure\Providers;

use App\Tools\BusinessDocumentValidator\Domain\Contracts\CompanyRegistryProvider;
use App\Tools\BusinessDocumentValidator\Domain\Validators\StateRegistrations\MinasGeraisStateRegistrationValidator;
use App\Tools\BusinessDocumentValidator\Domain\Validators\StateRegistrations\ParanaStateRegistrationValidator;
use App\Tools\BusinessDocumentValidator\Domain\Validators\StateRegistrations\PernambucoStateRegistrationValidator;
use App\Tools\BusinessDocumentValidator\Domain\Validators\StateRegistrations\SaoPauloStateRegistrationValidator;
use App\Tools\BusinessDocumentValidator\Domain\Validators\StateRegistrations\SingleDigitMod11StateRegistrationValidator;
use App\Tools\BusinessDocumentValidator\Domain\Validators\StateRegistrationValidatorRegistry;
use Illuminate\Support\ServiceProvider;

final class BusinessDocumentValidatorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CompanyRegistryProvider::class, BrasilApiCompanyRegistryProvider::class);

        $this->app->singleton(StateRegistrationValidatorRegistry::class, static fn (): StateRegistrationValidatorRegistry => new StateRegistrationValidatorRegistry([
            new SingleDigitMod11StateRegistrationValidator('CE', 'Ceará', 9, [9, 8, 7, 6, 5, 4, 3, 2]),
            new SingleDigitMod11StateRegistrationValidator('ES', 'Espírito Santo', 9, [9, 8, 7, 6, 5, 4, 3, 2]),
            new SingleDigitMod11StateRegistrationValidator('MA', 'Maranhão', 9, [9, 8, 7, 6, 5, 4, 3, 2], '12'),
            new MinasGeraisStateRegistrationValidator,
            new SingleDigitMod11StateRegistrationValidator('PA', 'Pará', 9, [9, 8, 7, 6, 5, 4, 3, 2], '03'),
            new SingleDigitMod11StateRegistrationValidator('PB', 'Paraíba', 9, [9, 8, 7, 6, 5, 4, 3, 2]),
            new PernambucoStateRegistrationValidator,
            new ParanaStateRegistrationValidator,
            new SingleDigitMod11StateRegistrationValidator('RJ', 'Rio de Janeiro', 8, [2, 7, 6, 5, 4, 3, 2]),
            new SingleDigitMod11StateRegistrationValidator('RS', 'Rio Grande do Sul', 10, [2, 9, 8, 7, 6, 5, 4, 3, 2]),
            new SingleDigitMod11StateRegistrationValidator('SC', 'Santa Catarina', 9, [9, 8, 7, 6, 5, 4, 3, 2]),
            new SingleDigitMod11StateRegistrationValidator('SE', 'Sergipe', 9, [9, 8, 7, 6, 5, 4, 3, 2]),
            new SaoPauloStateRegistrationValidator,
        ]));
    }
}
