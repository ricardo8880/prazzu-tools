<?php

namespace App\Core\Quality\Services;

use App\Core\Quality\Data\ArchitectureViolation;
use App\Core\Tools\Contracts\HasApiRoutes;
use App\Core\Tools\Contracts\HasViews;
use App\Core\Tools\Contracts\HasWebRoutes;
use App\Core\Tools\Support\ToolModuleStructure;
use App\Core\Tools\ToolRegistry;
use Illuminate\Filesystem\Filesystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
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
            $this->inspectModuleStructure(),
            $this->inspectMigrationNames(),
            $this->inspectNamespaces(),
            $this->inspectModuleDocumentation(),
            $this->inspectModuleDependencies(),
            $this->inspectDomainPurity(),
            $this->inspectFinancialPrimitivesOutsideDomain(),
            $this->inspectCoreImplementationLeaks(),
            $this->inspectToolPrintImplementations(),
            $this->inspectSharedToolComponents(),
            $this->inspectControllers(),
            $this->inspectRouteFiles(),
        ));
    }

    /** @return list<ArchitectureViolation> */
    private function inspectModuleStructure(): array
    {
        $violations = [];

        foreach ($this->moduleDirectories() as $moduleRoot) {
            foreach (ToolModuleStructure::missingPaths($moduleRoot) as $missingPath) {
                $violations[] = new ArchitectureViolation(
                    'tools.required-structure',
                    $this->relative($moduleRoot),
                    1,
                    "O módulo deve conter [{$missingPath}] conforme o README raiz.",
                );
            }
        }

        return $violations;
    }

    /** @return list<ArchitectureViolation> */
    private function inspectNamespaces(): array
    {
        $violations = [];
        $classes = [];

        foreach ($this->phpFiles(app_path('Tools')) as $file) {
            if (! $this->isAutoloadedPhpFile($file)) {
                continue;
            }

            $contents = $this->files->get($file);

            if (preg_match('/^namespace\s+([^;]+);/m', $contents, $namespaceMatch) !== 1) {
                $violations[] = new ArchitectureViolation(
                    'tools.namespace-required',
                    $this->relative($file),
                    1,
                    'Todo arquivo PHP de ferramenta deve declarar seu namespace PSR-4.',
                );

                continue;
            }

            $expectedNamespace = $this->expectedNamespace($file);
            $namespace = trim($namespaceMatch[1]);

            if ($namespace !== $expectedNamespace) {
                $violations[] = new ArchitectureViolation(
                    'tools.namespace-path',
                    $this->relative($file),
                    $this->lineNumberAtOffset($contents, (int) strpos($contents, $namespaceMatch[0])),
                    "O namespace [{$namespace}] deve acompanhar o caminho PSR-4 [{$expectedNamespace}].",
                );
            }

            if (preg_match('/\b(?:final\s+|abstract\s+|readonly\s+)*(?:class|interface|trait|enum)\s+([A-Za-z_][A-Za-z0-9_]*)/', $contents, $classMatch) !== 1) {
                continue;
            }

            $fqcn = $namespace.'\\'.$classMatch[1];
            $classKey = strtolower($fqcn);

            if (isset($classes[$classKey])) {
                $violations[] = new ArchitectureViolation(
                    'tools.unique-class',
                    $this->relative($file),
                    1,
                    "A classe [{$fqcn}] também foi declarada em [{$classes[$classKey]}].",
                );
            }

            $classes[$classKey] = $this->relative($file);
        }

        return $violations;
    }

    /** @return list<ArchitectureViolation> */
    private function inspectMigrationNames(): array
    {
        $violations = [];
        $filesByName = [];
        $roots = [database_path('migrations')];

        foreach ($this->moduleDirectories() as $moduleRoot) {
            $roots[] = $moduleRoot.DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Database'.DIRECTORY_SEPARATOR.'Migrations';
        }

        foreach ($roots as $root) {
            foreach (glob($root.DIRECTORY_SEPARATOR.'*.php') ?: [] as $file) {
                $name = strtolower(basename($file));

                if (isset($filesByName[$name])) {
                    $violations[] = new ArchitectureViolation(
                        'migrations.unique-name',
                        $this->relative($file),
                        1,
                        "A migration [{$name}] também existe em [{$filesByName[$name]}].",
                    );
                }

                $filesByName[$name] = $this->relative($file);
            }
        }

        return $violations;
    }

    /** @return list<ArchitectureViolation> */
    private function inspectModuleDocumentation(): array
    {
        $violations = [];
        $requiredSections = [
            'Descrição',
            'Funcionalidades',
            'Experiência Essencial',
            'Prazzu Plus',
            'Regras',
            'Dependências',
            'Histórico de versões',
        ];

        foreach ($this->moduleDirectories() as $moduleRoot) {
            $readme = $moduleRoot.DIRECTORY_SEPARATOR.'README.md';

            if (! is_file($readme)) {
                continue;
            }

            $contents = $this->files->get($readme);

            foreach ($requiredSections as $section) {
                if (preg_match('/^#{2,3}\s+'.preg_quote($section, '/').'/miu', $contents) === 1) {
                    continue;
                }

                $violations[] = new ArchitectureViolation(
                    'tools.readme-required-section',
                    $this->relative($readme),
                    1,
                    "O README da ferramenta deve documentar a seção [{$section}].",
                );
            }
        }

        return $violations;
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
    private function inspectFinancialPrimitivesOutsideDomain(): array
    {
        $violations = [];

        foreach ($this->phpFiles(app_path('Tools')) as $file) {
            $normalized = str_replace('\\', '/', $file);

            if (! str_contains($normalized, '/Application/') && ! str_contains($normalized, '/Domain/')) {
                continue;
            }

            foreach ($this->lines($file) as $lineNumber => $line) {
                if (preg_match('/\bfloat\b|\(float\)|floatval\s*\(/', $line) !== 1) {
                    continue;
                }

                $violations[] = new ArchitectureViolation(
                    'tools.no-financial-float',
                    $this->relative($file),
                    $lineNumber,
                    'Application e Domain de ferramentas não podem usar float; utilize Money, Percentage ou outro objeto financeiro do Core.',
                );
            }
        }

        return $violations;
    }

    /** @return list<ArchitectureViolation> */
    private function inspectCoreImplementationLeaks(): array
    {
        $violations = [];

        foreach ($this->phpFiles(app_path('Tools')) as $file) {
            $normalized = str_replace('\\', '/', $file);

            if (str_contains($normalized, '/Infrastructure/') || str_contains($normalized, '/Tests/')) {
                continue;
            }

            foreach ($this->lines($file) as $lineNumber => $line) {
                if (! str_contains($line, 'App\\Core\\') || ! str_contains($line, '\\Models\\')) {
                    continue;
                }

                $violations[] = new ArchitectureViolation(
                    'tools.no-core-model-dependency',
                    $this->relative($file),
                    $lineNumber,
                    'Ferramentas devem depender de contratos e DTOs do Core, nunca de models internos do Core.',
                );
            }
        }

        return $violations;
    }

    /** @return list<ArchitectureViolation> */
    private function inspectToolPrintImplementations(): array
    {
        $violations = [];

        foreach ($this->phpFiles(app_path('Tools')) as $file) {
            if (! str_ends_with(str_replace('\\', '/', $file), '.blade.php')) {
                continue;
            }

            foreach ($this->lines($file) as $lineNumber => $line) {
                if (preg_match('/window\.print\s*\(/', $line) !== 1) {
                    continue;
                }

                $violations[] = new ArchitectureViolation(
                    'tools.no-direct-browser-print',
                    $this->relative($file),
                    $lineNumber,
                    'Views de ferramentas devem reutilizar o componente compartilhado de impressão do Core.',
                );
            }
        }

        return $violations;
    }

    /** @return list<ArchitectureViolation> */
    private function inspectSharedToolComponents(): array
    {
        $violations = [];
        $required = [
            'intro.blade.php',
            'form-panel.blade.php',
            'validation-summary.blade.php',
            'result-panel.blade.php',
            'history-actions.blade.php',
            'export-button.blade.php',
            'print-button.blade.php',
        ];
        $root = resource_path('views/components/tools');

        foreach ($required as $component) {
            $path = $root.DIRECTORY_SEPARATOR.$component;

            if (is_file($path)) {
                continue;
            }

            $violations[] = new ArchitectureViolation(
                'tools.shared-component-required',
                $this->relative($path),
                1,
                "O componente compartilhado [{$component}] é obrigatório para manter a experiência consistente entre ferramentas.",
            );
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
                'controllers.no-database-query' => '/\bDB::|[A-Za-z_][A-Za-z0-9_]*::(?:query|where|create|find|findOrFail|updateOrCreate)\s*\(/',
                'controllers.no-export-implementation' => '/streamDownload\s*\(|\bfputcsv\s*\(|php:\/\/output/',
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

            $contents = $this->files->get($file);
            preg_match_all(
                '/Route::(?:get|post|put|patch|delete|any|match)\s*\([^;]*?(?:static\s+)?(?:function\s*\(|fn\s*\()/s',
                $contents,
                $matches,
                PREG_OFFSET_CAPTURE,
            );

            foreach ($matches[0] ?? [] as [$match, $offset]) {
                $violations[] = new ArchitectureViolation(
                    'routes.no-closures',
                    $this->relative($file),
                    $this->lineNumberAtOffset($contents, (int) $offset),
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

    /** @return list<string> */
    private function moduleDirectories(): array
    {
        $root = app_path('Tools');

        if (! is_dir($root)) {
            return [];
        }

        $directories = array_values(array_filter(
            glob($root.DIRECTORY_SEPARATOR.'*') ?: [],
            static fn (string $path): bool => is_dir($path),
        ));

        sort($directories);

        return $directories;
    }

    private function expectedNamespace(string $file): string
    {
        $appRoot = str_replace('\\', '/', app_path()).'/';
        $directory = str_replace('\\', '/', dirname($file));
        $relativeDirectory = str_starts_with($directory.'/', $appRoot)
            ? substr($directory, strlen($appRoot))
            : $directory;

        return 'App\\'.str_replace('/', '\\', trim($relativeDirectory, '/'));
    }

    private function isAutoloadedPhpFile(string $file): bool
    {
        $normalized = str_replace('\\', '/', $file);

        return ! str_ends_with($normalized, '.blade.php')
            && ! str_contains($normalized, '/Routes/')
            && ! str_contains($normalized, '/Infrastructure/Database/Migrations/');
    }

    private function lineNumberAtOffset(string $contents, int $offset): int
    {
        return substr_count(substr($contents, 0, max(0, $offset)), "\n") + 1;
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
