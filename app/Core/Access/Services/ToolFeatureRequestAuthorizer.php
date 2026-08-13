<?php

declare(strict_types=1);

namespace App\Core\Access\Services;

use App\Core\Access\Contracts\ToolFeatureAccessGate;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Enums\ToolFeatureTier;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final readonly class ToolFeatureRequestAuthorizer
{
    public function __construct(private ToolFeatureAccessGate $gate) {}

    public function allows(ToolModule $module, string $featureKey, Request $request): bool
    {
        return $this->gate->decide($module->manifest(), $featureKey, $request->user())->allowed;
    }

    public function plusEnabled(ToolModule $module, Request $request): bool
    {
        foreach ($module->manifest()->featuresFor(ToolFeatureTier::Plus) as $feature) {
            if ($this->gate->decide($module->manifest(), $feature->key, $request->user())->allowed) {
                return true;
            }
        }

        return false;
    }

    public function requireIf(bool $condition, ToolModule $module, string $featureKey, Request $request): void
    {
        if ($condition) {
            $this->require($module, $featureKey, $request);
        }
    }

    public function require(ToolModule $module, string $featureKey, Request $request): void
    {
        $decision = $this->gate->decide($module->manifest(), $featureKey, $request->user());
        if ($decision->allowed) {
            return;
        }

        $message = match ($decision->reason) {
            'feature.authentication_required' => 'Entre na sua conta e assine o Prazzu Plus para usar este recurso.',
            'feature.plus_required' => 'Este recurso faz parte do Prazzu Plus.',
            'feature.disabled' => 'Este recurso está temporariamente indisponível.',
            default => 'Este recurso não está disponível no momento.',
        };

        throw ValidationException::withMessages(['plus' => $message]);
    }
}
