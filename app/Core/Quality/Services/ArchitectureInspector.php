<?php

namespace App\Core\Quality\Services;

use App\Core\Quality\Data\ArchitectureViolation;
use App\Core\Tools\Contracts\HasApiRoutes;
use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\ToolRegistry;
use Illuminate\Filesystem\Filesystem;
use ReflectionClass;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

final class ArchitectureInspector
{
    public function __construct(
        private readonly ToolRegistry $registry,
        private readonly Filesystem $files,
    ) {}

    /** @return list<ArchitectureViolation> */
    public function inspect(): array
    {
        return array_values(array_merge(
            $this->inspectRegisteredModules(),
            $this->inspectModuleDependencies(),
            $this->inspectDomainPurity(),
            $this->inspectControllers(),
            $this->inspectRouteFiles(),
        ));
    }

    /** @return list<ArchitectureViolation> */
    private function inspectRegisteredModules(): array
    {
        $violations = [];
        $routeNames = [];
        $viewNamespaces = [];

        foreach ($this->registry->modules() as $module) {
            $manifest = $module->manifest();
            $reflection = new ReflectionClass($module);
            $moduleFile = $reflection->getFileName();

            if ($moduleFile === false) {
                continue;
            }

            $relativeFile = $this->relative($moduleFile);

            if (isset($routeNames[$manifest->routeName])) {
                $violations[] = new ArchitectureViolation(
                    'tools.unique-route-name',
                    $relativeFile,
                    1,
                    "A rota [{$manifest->routeName}] também é usada por [{$routeNames[$manifest->routeName]}].",
                );
            }

            $routeNames[$manifest->routeName] = $manifest->slug;

            if (! $module instanceof HasWebRoutes && ! $module instanceof HasApiRoutes) {
                $violations[] = new ArchitectureViolation(
                    'tools.routes-required',
                    $relativeFile,
                    1,
                    "O módulo [{$manifest->slug}] deve expor ao menos rotas web ou API.",
                );
            }

            if ($module instanceof HasViews) {
                $namespace = $module->viewsNamespace();

                if (isset($viewNamespaces[$namespace])) {
                    $violations[] = new ArchitectureViolation(
                        'tools.unique-view-namespace',
                        $relativeFile,
                        1,
                        "O namespace de views [{$namespace}] também é usado por [{$viewNamespaces[$namespace]}].",
                    );
                }

                $viewNamespaces[$namespace] = $manifest->slug;
            }
        }

        return $violations;
    }

    /** @return list<ArchitectureViolation> */
    private function inspectModuleDependencies(): array
    {
        $violations = [];

        foreach ($this->phpFiles(app_path('Tools')) as $file) {
            $relative = $this->relative($file);
            $currentModule = $this->moduleNameFromPath($file);

            if ($currentModule === null) {
                continue;
            }

            foreach ($this->lines($file) as $lineNumber => $line) {
                if (! preg_match_all('/App\\\\Tools\\\\([A-Za-z0-9_]+)/', $line, $matches)) {
                    continue;
                }

                foreach ($matches[1] as $dependency) {
                    if ($dependency === $currentModule) {
                        continue;
                    }

                    $violations[] = new ArchitectureViolation(
                        'tools.no-cross-module-dependency',
                        $relative,
                        $lineNumber,
                        "O módulo [{$currentModule}] não pode depender diretamente de [{$dependency}]. Extraia um contrato estável para o Core.",
                    );
                }
            }
        }

        return $violations;
    }

    /** @return list<ArchitectureViolation> */
    private function inspectDomainPurity(): array
    {
        $violations = [];

        foreach ($this->phpFiles(app_path('Tools')) as $file) {
            if (! str_contains(str_replace('\\', '/', $file), '/Domain/')) {
                continue;
            }

            $rules = [
                'domain.no-framework' => '/Illuminate\\\\|Laravel\\\\/',
                'domain.no-current-time' => '/\bnow\s*\(|\btoday\s*\(|Carbon::now\s*\(/',
                'domain.no-float-money' => '/\bfloat\b|\(float\)|floatval\s*\(/',
                'domain.no-environment' => '/\benv\s*\(|\bconfig\s*\(/',
            ];

            foreach ($this->lines($file) as $lineNumber => $line) {
                foreach ($rules as $rule => $pattern) {
                    if (preg_match($pattern, $line) !== 1) {
                        continue;
                    }

                    $violations[] = new ArchitectureViolation(
                        $rule,
                        $this->relative($file),
                        $lineNumber,
                        'O domínio deve permanecer determinístico e independente do framework.',
                    );
                }
            }
        }

        return $violations;
    }

    /** @return list<ArchitectureViolation> */
    private function inspectControllers(): array
    {
        $violations = [];

        foreach ($this->phpFiles(app_path('Tools')) as $file) {
            $normalized = str_replace('\\', '/', $file);

            if (! str_contains($normalized, '/Presentation/Controllers/')) {
                continue;
            }

            $patterns = [
                'controllers.no-direct-http-client' => '/Http::|new\s+Client\s*\(/',
                'controllers.no-database-query' => '/DB::|->query\s*\(|::where\s*\(|::create\s*\(/',
            ];

            foreach ($this->lines($file) as $lineNumber => $line) {
                foreach ($patterns as $rule => $pattern) {
                    if (preg_match($pattern, $line) !== 1) {
                        continue;
                    }

                    $violations[] = new ArchitectureViolation(
                        $rule,
                        $this->relative($file),
                        $lineNumber,
                        'Controllers devem apenas coordenar entrada e saída; use Application ou Infrastructure.',
                    );
                }
            }
        }

        return $violations;
    }

    /** @return list<ArchitectureViolation> */
    private function inspectRouteFiles(): array
    {
        $violations = [];

        foreach ($this->phpFiles(app_path('Tools')) as $file) {
            $normalized = str_replace('\\', '/', $file);

            if (! str_contains($normalized, '/Routes/')) {
                continue;
            }

            foreach ($this->lines($file) as $lineNumber => $line) {
                if (preg_match('/Route::(?:get|post|put|patch|delete|any|match)\s*\([^;]*function\s*\(/', $line) !== 1) {
                    continue;
                }

                $violations[] = new ArchitectureViolation(
                    'routes.no-closures',
                    $this->relative($file),
                    $lineNumber,
                    'Rotas de ferramentas devem apontar para controllers para serem compatíveis com route:cache.',
                );
            }
        }

        return $violations;
    }

    /** @return list<string> */
    private function phpFiles(string $directory): array
    {
        if (! is_dir($directory)) {
            return [];
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        $files = new RegexIterator($iterator, '/\.php$/i');
        $paths = [];

        foreach ($files as $file) {
            if ($file->isFile()) {
                $paths[] = $file->getPathname();
            }
        }

        sort($paths);

        return $paths;
    }

    /** @return array<int, string> */
    private function lines(string $file): array
    {
        $contents = $this->files->get($file);
        $lines = preg_split('/\R/', $contents) ?: [];
        $indexed = [];

        foreach ($lines as $index => $line) {
            $indexed[$index + 1] = $line;
        }

        return $indexed;
    }

    private function moduleNameFromPath(string $file): ?string
    {
        $toolsRoot = str_replace('\\', '/', app_path('Tools')).'/';
        $normalized = str_replace('\\', '/', $file);

        if (! str_starts_with($normalized, $toolsRoot)) {
            return null;
        }

        $remainder = substr($normalized, strlen($toolsRoot));
        $parts = explode('/', $remainder);

        return $parts[0] !== '' && $parts[0] !== 'README.md' ? $parts[0] : null;
    }

    private function relative(string $path): string
    {
        $base = str_replace('\\', '/', base_path()).'/';
        $normalized = str_replace('\\', '/', $path);

        return str_starts_with($normalized, $base) ? substr($normalized, strlen($base)) : $normalized;
    }
}
