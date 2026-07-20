<?php

namespace App\Core\Tools\Support;

use App\Core\Tools\Contracts\HasApiRoutes;
use App\Core\Tools\Contracts\HasMigrations;
use App\Core\Tools\Contracts\HasServiceProviders;
use App\Core\Tools\Contracts\HasToolIntegrations;
use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Enums\ToolCapability;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\History\Contracts\HasHistoryPolicy;
use App\Core\Tools\Infrastructure\Enums\SensitiveDataMode;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use ReflectionClass;

final class ToolModuleValidator
{
    public function validate(ToolModule $module): void
    {
        $manifest = $module->manifest();
        $expectedRoutePrefix = "tools.{$manifest->slug}.";

        if ($manifest->featuresFor(ToolFeatureTier::Essential) === []) {
            throw new InvalidArgumentException(
                "A ferramenta [{$manifest->slug}] deve declarar ao menos um recurso Essencial completo.",
            );
        }

        if ($manifest->featuresFor(ToolFeatureTier::Plus) === []) {
            throw new InvalidArgumentException(
                "A ferramenta [{$manifest->slug}] deve declarar ao menos um recurso Prazzu Plus avançado.",
            );
        }

        if (! str_starts_with($manifest->routeName, $expectedRoutePrefix)) {
            throw new InvalidArgumentException(
                "A rota principal de [{$manifest->slug}] deve começar com [{$expectedRoutePrefix}].",
            );
        }

        $reflection = new ReflectionClass($module);
        $moduleFile = $reflection->getFileName();

        if ($moduleFile === false) {
            throw new InvalidArgumentException("Não foi possível localizar o arquivo do módulo [{$manifest->slug}].");
        }

        $moduleRoot = dirname($moduleFile);
        $missingPaths = str_starts_with($reflection->getName(), 'App\\Tools\\')
            ? ToolModuleStructure::missingPaths($moduleRoot)
            : [];

        if ($missingPaths !== []) {
            throw new InvalidArgumentException(sprintf(
                'O módulo [%s] não possui a estrutura obrigatória: %s.',
                $manifest->slug,
                implode(', ', $missingPaths),
            ));
        }

        if ($manifest->supportsHistory !== $manifest->hasCapability(ToolCapability::History)) {
            throw new InvalidArgumentException(
                "A capacidade de histórico de [{$manifest->slug}] não corresponde ao manifesto.",
            );
        }

        if ($manifest->storesSensitiveData !== $manifest->hasCapability(ToolCapability::SensitiveData)) {
            throw new InvalidArgumentException(
                "A capacidade de dados sensíveis de [{$manifest->slug}] não corresponde ao manifesto.",
            );
        }

        if ($manifest->persistence !== null) {
            if ($manifest->persistence->enabled !== $manifest->hasCapability(ToolCapability::VersionedPersistence)) {
                throw new InvalidArgumentException("A persistência versionada de [{$manifest->slug}] não corresponde às capacidades declaradas.");
            }

            if ($manifest->persistence->enabled !== $manifest->supportsHistory) {
                throw new InvalidArgumentException("A persistência versionada de [{$manifest->slug}] deve acompanhar o suporte a histórico.");
            }
        }

        if ($manifest->export !== null && $manifest->export->enabled !== $manifest->hasCapability(ToolCapability::Export)) {
            throw new InvalidArgumentException("A exportação de [{$manifest->slug}] não corresponde às capacidades declaradas.");
        }

        if ($manifest->sharing !== null && $manifest->sharing->enabled !== $manifest->hasCapability(ToolCapability::Sharing)) {
            throw new InvalidArgumentException("O compartilhamento de [{$manifest->slug}] não corresponde às capacidades declaradas.");
        }

        if ($manifest->sensitiveData !== null) {
            $hasSensitivePolicy = $manifest->sensitiveData->mode !== SensitiveDataMode::None;

            if ($manifest->storesSensitiveData !== $hasSensitivePolicy) {
                throw new InvalidArgumentException("A política de dados sensíveis de [{$manifest->slug}] não corresponde ao manifesto.");
            }
        }

        if (($manifest->sharing?->allowSensitivePayload ?? false) && ($manifest->sensitiveData?->mode ?? SensitiveDataMode::None) === SensitiveDataMode::None) {
            throw new InvalidArgumentException("O compartilhamento de [{$manifest->slug}] permite payload sensível sem declarar uma política de dados sensíveis.");
        }

        if ($module instanceof HasToolIntegrations) {
            $integrations = $module->integrations();

            if (($integrations->publishes !== []) !== $manifest->hasCapability(ToolCapability::PublishesIntegrations)) {
                throw new InvalidArgumentException(
                    "A capacidade de publicação de integrações de [{$manifest->slug}] não corresponde ao manifesto.",
                );
            }

            if (($integrations->accepts !== []) !== $manifest->hasCapability(ToolCapability::AcceptsIntegrations)) {
                throw new InvalidArgumentException(
                    "A capacidade de consumo de integrações de [{$manifest->slug}] não corresponde ao manifesto.",
                );
            }
        }

        if ($manifest->supportsHistory !== ($module instanceof HasHistoryPolicy)) {
            throw new InvalidArgumentException(
                "A declaração de histórico de [{$manifest->slug}] não corresponde às capacidades do módulo.",
            );
        }

        if ($module instanceof HasHistoryPolicy) {
            $policy = $module->historyPolicy();

            if (! $policy->enabled) {
                throw new InvalidArgumentException("O módulo [{$manifest->slug}] declara histórico, mas sua política está desabilitada.");
            }

            if ($manifest->storesSensitiveData !== ($policy->sensitiveFields !== [])) {
                throw new InvalidArgumentException(
                    "A declaração de dados sensíveis de [{$manifest->slug}] não corresponde à política de histórico.",
                );
            }
        }

        if ($module instanceof HasWebRoutes) {
            $this->validateFile($module->webRoutesPath(), $moduleRoot.'/Routes', 'rotas web', $manifest->slug);
        }

        if ($module instanceof HasApiRoutes) {
            $this->validateFile($module->apiRoutesPath(), $moduleRoot.'/Routes', 'rotas de API', $manifest->slug);
        }

        if ($module instanceof HasViews) {
            $this->validateDirectory($module->viewsPath(), $moduleRoot.'/Resources/views', 'views', $manifest->slug);

            if ($module->viewsNamespace() !== "tools-{$manifest->slug}") {
                throw new InvalidArgumentException(
                    "O namespace de views de [{$manifest->slug}] deve ser [tools-{$manifest->slug}].",
                );
            }
        }

        if ($module instanceof HasMigrations) {
            $this->validateDirectory($module->migrationsPath(), $moduleRoot.'/Infrastructure/Database/Migrations', 'migrations', $manifest->slug);
        }

        if ($module instanceof HasServiceProviders) {
            foreach ($module->serviceProviders() as $providerClass) {
                if (! is_a($providerClass, ServiceProvider::class, true)) {
                    throw new InvalidArgumentException(
                        "O provider [{$providerClass}] de [{$manifest->slug}] deve estender ".ServiceProvider::class.'.',
                    );
                }

                $providerFile = (new ReflectionClass($providerClass))->getFileName();

                if ($providerFile === false) {
                    throw new InvalidArgumentException("Não foi possível localizar o provider [{$providerClass}].");
                }

                $this->validateFile($providerFile, $moduleRoot.'/Infrastructure/Providers', 'provider de serviço', $manifest->slug);
            }
        }
    }

    private function validateFile(string $path, string $moduleRoot, string $type, string $slug): void
    {
        if (! is_file($path)) {
            throw new InvalidArgumentException("O arquivo de {$type} de [{$slug}] não existe: {$path}");
        }

        $this->validateInsideModule($path, $moduleRoot, $type, $slug);
    }

    private function validateDirectory(string $path, string $moduleRoot, string $type, string $slug): void
    {
        if (! is_dir($path)) {
            throw new InvalidArgumentException("O diretório de {$type} de [{$slug}] não existe: {$path}");
        }

        $this->validateInsideModule($path, $moduleRoot, $type, $slug);
    }

    private function validateInsideModule(string $path, string $moduleRoot, string $type, string $slug): void
    {
        $resolvedPath = realpath($path);
        $resolvedRoot = realpath($moduleRoot);

        if ($resolvedPath === false || $resolvedRoot === false || ($resolvedPath !== $resolvedRoot && ! str_starts_with($resolvedPath, $resolvedRoot.DIRECTORY_SEPARATOR))) {
            throw new InvalidArgumentException("O recurso de {$type} de [{$slug}] deve permanecer em sua camada obrigatória.");
        }
    }
}
