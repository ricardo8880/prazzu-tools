<?php

namespace App\Core\Tools\Infrastructure\Services;

use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Infrastructure\Contracts\ToolResultSharingGuard;
use DomainException;

final class ManifestToolResultSharingGuard implements ToolResultSharingGuard
{
    public function authorize(ToolModule $module, bool $authenticated, bool $containsSensitivePayload): void
    {
        $policy = $module->manifest()->sharing;

        if ($policy === null || ! $policy->enabled) {
            throw new DomainException('O compartilhamento não está habilitado para esta ferramenta.');
        }

        if ($policy->requiresAuthentication && ! $authenticated) {
            throw new DomainException('O compartilhamento desta ferramenta exige autenticação.');
        }

        if ($containsSensitivePayload && ! $policy->allowSensitivePayload) {
            throw new DomainException('O manifesto não permite compartilhar dados sensíveis.');
        }
    }

    public function expirationMinutes(ToolModule $module): int
    {
        $policy = $module->manifest()->sharing;

        if ($policy === null || ! $policy->enabled) {
            throw new DomainException('O compartilhamento não está habilitado para esta ferramenta.');
        }

        return $policy->expiresAfterMinutes;
    }
}
