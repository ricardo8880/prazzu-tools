<?php

namespace App\Console\Commands;

use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolStatus;
use App\Core\Tools\Support\ToolModuleStructure;
use App\Core\Tools\ToolRegistry;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;

final class MakeToolCommand extends Command
{
    protected $signature = 'make:tool
        {name : Nome da classe da ferramenta, por exemplo CalculadoraRescisao}
        {--slug= : Slug público; por padrão é gerado a partir do nome}
        {--category=outros : Categoria oficial da ferramenta}
        {--status=draft : Estado inicial da ferramenta}
        {--force : Sobrescreve arquivos existentes}';

    protected $description = 'Cria a estrutura padrão de um módulo de ferramenta';

    public function __construct(
        private readonly Filesystem $files,
        private readonly ToolRegistry $registry,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            $context = $this->buildContext();
            $this->validateOptions($context);
            $this->generateFiles($context);
            $this->registerModule($context);
        } catch (RuntimeException $exception) {
            $this->output->writeln('<error>'.$exception->getMessage().'</error>');

            return self::FAILURE;
        }

        $this->output->writeln("<info>Módulo [{$context['class']}] criado com sucesso.</info>");
        $this->line("Diretório: app/Tools/{$context['class']}");
        $this->line('Próximos passos: implemente o domínio, documente as regras e escreva os casos de referência.');

        return self::SUCCESS;
    }

    /** @return array<string, string> */
    private function buildContext(): array
    {
        $class = $this->studly((string) $this->argument('name'));

        if ($class === '' || ! preg_match('/^[A-Z][A-Za-z0-9]*$/', $class)) {
            throw new RuntimeException('O nome deve gerar uma classe PHP válida em StudlyCase.');
        }

        $slug = trim((string) ($this->option('slug') ?: $this->kebab($class)));
        $category = trim((string) $this->option('category'));
        $status = trim((string) $this->option('status'));

        try {
            $categoryEnum = ToolCategory::from($category);
            $statusEnum = ToolStatus::from($status);
        } catch (\ValueError $exception) {
            throw new RuntimeException('Categoria ou status informado não é reconhecido.', previous: $exception);
        }

        return [
            'class' => $class,
            'slug' => $slug,
            'name' => $this->headline($class),
            'category' => $category,
            'category_case' => $categoryEnum->name,
            'status' => $status,
            'status_case' => $statusEnum->name,
            'route_prefix' => "tools.{$slug}",
            'view_namespace' => "tools-{$slug}",
            'module_class' => "App\\Tools\\{$class}\\Tool",
            'config_group' => $this->configGroup($category),
        ];
    }

    /** @param array<string, string> $context */
    private function validateOptions(array $context): void
    {
        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $context['slug'])) {
            throw new RuntimeException('O slug deve conter apenas letras minúsculas, números e hífens.');
        }

        if ($context['status'] !== ToolStatus::Draft->value) {
            throw new RuntimeException('Toda nova ferramenta deve iniciar com o estado [draft].');
        }

        $registeredModule = $this->registry->findModule($context['slug']);

        if ($registeredModule !== null
            && (! $this->option('force') || $registeredModule::class !== $context['module_class'])) {
            throw new RuntimeException("Já existe uma ferramenta registrada com o slug [{$context['slug']}].");
        }

        $target = app_path("Tools/{$context['class']}");

        if ($this->files->isDirectory($target) && ! $this->option('force')) {
            throw new RuntimeException("O módulo [{$context['class']}] já existe. Use --force para sobrescrever.");
        }

        foreach ($this->fileMap($context) as $stub => $destination) {
            $stubPath = base_path("stubs/tool/{$stub}");

            if (! $this->files->isFile($stubPath)) {
                throw new RuntimeException("O stub obrigatório [{$stub}] não existe.");
            }

            if ($this->files->exists(base_path($destination)) && ! $this->option('force')) {
                throw new RuntimeException("O arquivo [{$destination}] já existe.");
            }
        }

        $this->validateRegistrationTarget($context);
    }

    /** @param array<string, string> $context */
    private function generateFiles(array $context): void
    {
        $moduleRoot = app_path("Tools/{$context['class']}");

        foreach (ToolModuleStructure::requiredDirectories() as $directory) {
            $this->files->ensureDirectoryExists($moduleRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $directory));
        }

        foreach ($this->fileMap($context) as $stub => $destination) {
            $destinationPath = base_path($destination);

            $this->files->ensureDirectoryExists(dirname($destinationPath));
            $this->files->put($destinationPath, $this->renderStub($stub, $context));
        }

        foreach (['Domain', 'Infrastructure'] as $emptyLayer) {
            $placeholder = $moduleRoot.DIRECTORY_SEPARATOR.$emptyLayer.DIRECTORY_SEPARATOR.'.gitkeep';

            if (! $this->files->exists($placeholder)) {
                $this->files->put($placeholder, '');
            }
        }
    }

    /**
     * @param  array<string, string>  $context
     * @return array<string, string>
     */
    private function fileMap(array $context): array
    {
        return [
            'Tool.stub' => "app/Tools/{$context['class']}/Tool.php",
            'Action.stub' => "app/Tools/{$context['class']}/Application/Actions/ShowToolPage.php",
            'Controller.stub' => "app/Tools/{$context['class']}/Presentation/Controllers/ToolController.php",
            'Request.stub' => "app/Tools/{$context['class']}/Presentation/Requests/ExecuteToolRequest.php",
            'web.stub' => "app/Tools/{$context['class']}/Routes/web.php",
            'view.stub' => "app/Tools/{$context['class']}/Resources/views/index.blade.php",
            'UnitTest.stub' => "app/Tools/{$context['class']}/Tests/Unit/ToolManifestTest.php",
            'FeatureTest.stub' => "app/Tools/{$context['class']}/Tests/Feature/ToolPageTest.php",
            'README.stub' => "app/Tools/{$context['class']}/README.md",
        ];
    }

    /** @param array<string, string> $context */
    private function validateRegistrationTarget(array $context): void
    {
        $path = config_path('tools/modules.php');
        $contents = $this->files->get($path);
        $marker = "        // <tools:{$context['config_group']}>";
        $registration = "        \\{$context['module_class']}::class,";

        if (! str_contains($contents, $registration) && ! str_contains($contents, $marker)) {
            throw new RuntimeException("Marcador de registro [{$marker}] não encontrado em config/tools/modules.php.");
        }
    }

    /** @param array<string, string> $context */
    private function registerModule(array $context): void
    {
        $path = config_path('tools/modules.php');
        $contents = $this->files->get($path);
        $marker = "        // <tools:{$context['config_group']}>";
        $registration = "        \\{$context['module_class']}::class,";

        if (str_contains($contents, $registration)) {
            return;
        }

        if (! str_contains($contents, $marker)) {
            throw new RuntimeException("Marcador de registro [{$marker}] não encontrado em config/tools/modules.php.");
        }

        $contents = str_replace($marker, $registration."\n".$marker, $contents);
        $this->files->put($path, $contents);
    }

    /** @param array<string, string> $context */
    private function renderStub(string $stub, array $context): string
    {
        $contents = $this->files->get(base_path("stubs/tool/{$stub}"));

        $replacements = [
            '{{ class }}' => $context['class'],
            '{{ slug }}' => $context['slug'],
            '{{ name }}' => $context['name'],
            '{{ category_case }}' => $context['category_case'],
            '{{ status_case }}' => $context['status_case'],
            '{{ route_prefix }}' => $context['route_prefix'],
            '{{ view_namespace }}' => $context['view_namespace'],
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $contents);
    }

    private function studly(string $value): string
    {
        $value = preg_replace('/(?<!^)([A-Z])/', ' $1', trim($value)) ?? $value;
        $value = preg_replace('/[^A-Za-z0-9]+/', ' ', $value) ?? '';
        $words = preg_split('/\s+/', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return implode('', array_map(static fn (string $word): string => ucfirst(strtolower($word)), $words));
    }

    private function kebab(string $value): string
    {
        $value = preg_replace('/(?<!^)[A-Z]/', '-$0', $value) ?? $value;

        return strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $value) ?? '', '-'));
    }

    private function headline(string $value): string
    {
        $value = preg_replace('/(?<!^)[A-Z]/', ' $0', $value) ?? $value;

        return ucwords(strtolower(trim($value)));
    }

    private function configGroup(string $category): string
    {
        return match (ToolCategory::from($category)) {
            ToolCategory::Fiscal => 'fiscal',
            ToolCategory::Labor => 'labor',
            ToolCategory::Corporate => 'corporate',
            ToolCategory::Documents => 'documents',
            default => 'general',
        };
    }
}
