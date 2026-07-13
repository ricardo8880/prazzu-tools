<?php

namespace App\Core\Tools\Support;

use App\Core\Tools\Contracts\HasApiRoutes;
use App\Core\Tools\Contracts\HasMigrations;
use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\History\Contracts\HasHistoryPolicy;
use InvalidArgumentException;
use ReflectionClass;

final class ToolModuleValidator
{
    public function validate(ToolModule $module): void
    {
        $manifest = $module->manifest();
        $expectedRoutePrefix = "tools.{$manifest->slug}.";

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
            $this->validateFile($module->webRoutesPath(), $moduleRoot, 'rotas web', $manifest->slug);
        }

        if ($module instanceof HasApiRoutes) {
            $this->validateFile($module->apiRoutesPath(), $moduleRoot, 'rotas de API', $manifest->slug);
        }

        if ($module instanceof HasViews) {
            $this->validateDirectory($module->viewsPath(), $moduleRoot, 'views', $manifest->slug);

            if ($module->viewsNamespace() !== "tools-{$manifest->slug}") {
                throw new InvalidArgumentException(
                    "O namespace de views de [{$manifest->slug}] deve ser [tools-{$manifest->slug}].",
                );
            }
        }

        if ($module instanceof HasMigrations) {
            $this->validateDirectory($module->migrationsPath(), $moduleRoot, 'migrations', $manifest->slug);
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

        if ($resolvedPath === false || $resolvedRoot === false || ! str_starts_with($resolvedPath, $resolvedRoot.DIRECTORY_SEPARATOR)) {
            throw new InvalidArgumentException("O recurso de {$type} de [{$slug}] deve permanecer dentro do próprio módulo.");
        }
    }
}
