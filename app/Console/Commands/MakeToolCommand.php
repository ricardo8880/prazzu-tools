<?php

namespace App\Console\Commands;

use App\Core\Quality\Enums\ExternalIntegrationDependency;
use App\Core\Quality\Enums\NormativeDependency;
use App\Core\Quality\Enums\PersistenceMode;
use App\Core\Quality\Enums\PersonalDataExposure;
use App\Core\Quality\Enums\ProcessingMode;
use App\Core\Quality\Enums\ResultRisk;
use App\Core\Quality\Enums\ToolNature;
use App\Core\Quality\Enums\UpdateFrequency;
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
        {--nature=calculation : Natureza da ferramenta}
        {--normative=none : Dependência normativa}
        {--personal-data=none : Exposição a dados pessoais}
        {--integration=none : Dependência de integração externa}
        {--persistence=temporary : Forma de persistência}
        {--processing=synchronous : Modo de processamento}
        {--result-risk=informational : Risco do resultado}
        {--update-frequency=rare : Frequência esperada de atualização}
        {--exports= : Formatos de exportação separados por vírgula}
        {--history : Habilita histórico e persistência versionada}
        {--retention-days=365 : Retenção do histórico em dias}
        {--schema-version=1 : Versão inteira do schema persistido}
        {--minimum-readable-schema-version=1 : Menor versão de schema ainda legível}
        {--share : Habilita compartilhamento temporário de resultados}
        {--share-expires=60 : Expiração do compartilhamento em minutos}
        {--share-auth : Exige autenticação para abrir compartilhamentos}
        {--sensitive-mode=none : none, redact ou encrypted}
        {--sensitive-fields= : Campos sensíveis separados por vírgula}
        {--publishes= : Contratos de integração publicados, separados por vírgula}
        {--accepts= : Contratos de integração aceitos, separados por vírgula}
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
        $nature = trim((string) $this->option('nature'));
        $normative = trim((string) $this->option('normative'));
        $personalData = trim((string) $this->option('personal-data'));
        $integration = trim((string) $this->option('integration'));
        $persistence = trim((string) $this->option('persistence'));
        $processing = trim((string) $this->option('processing'));
        $resultRisk = trim((string) $this->option('result-risk'));
        $updateFrequency = trim((string) $this->option('update-frequency'));
        $exports = $this->parseExports((string) $this->option('exports'));
        $history = (bool) $this->option('history');
        $retentionDays = (int) $this->option('retention-days');
        $schemaVersion = (int) $this->option('schema-version');
        $minimumReadableSchemaVersion = (int) $this->option('minimum-readable-schema-version');
        $sharing = (bool) $this->option('share');
        $shareExpires = (int) $this->option('share-expires');
        $shareAuth = (bool) $this->option('share-auth');
        $sensitiveMode = strtolower(trim((string) $this->option('sensitive-mode')));
        $sensitiveFields = $this->parseFieldList((string) $this->option('sensitive-fields'));
        $publishes = $this->parseIntegrationContracts((string) $this->option('publishes'));
        $accepts = $this->parseIntegrationContracts((string) $this->option('accepts'));

        try {
            $categoryEnum = ToolCategory::from($category);
            $statusEnum = ToolStatus::from($status);
            $natureEnum = ToolNature::from($nature);
            $normativeEnum = NormativeDependency::from($normative);
            $personalDataEnum = PersonalDataExposure::from($personalData);
            $integrationEnum = ExternalIntegrationDependency::from($integration);
            $persistenceEnum = PersistenceMode::from($persistence);
            $processingEnum = ProcessingMode::from($processing);
            $resultRiskEnum = ResultRisk::from($resultRisk);
            $updateFrequencyEnum = UpdateFrequency::from($updateFrequency);
        } catch (\ValueError $exception) {
            throw new RuntimeException('Uma das opções informadas não é reconhecida.', previous: $exception);
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
            'nature_case' => $natureEnum->name,
            'normative_case' => $normativeEnum->name,
            'personal_data_case' => $personalDataEnum->name,
            'integration_case' => $integrationEnum->name,
            'persistence_case' => $persistenceEnum->name,
            'processing_case' => $processingEnum->name,
            'result_risk_case' => $resultRiskEnum->name,
            'update_frequency_case' => $updateFrequencyEnum->name,
            'exports_php' => $this->exportsPhp($exports),
            'exports_markdown' => $exports === [] ? 'Nenhum.' : implode(', ', $exports),
            'normative_value' => $normativeEnum->value,
            'publishes_php' => $this->stringListPhp($publishes),
            'accepts_php' => $this->stringListPhp($accepts),
            'publishes_markdown' => $publishes === [] ? 'Nenhum.' : implode(', ', $publishes),
            'accepts_markdown' => $accepts === [] ? 'Nenhum.' : implode(', ', $accepts),
            'supports_history_php' => $history ? 'true' : 'false',
            'stores_sensitive_data_php' => $sensitiveMode === 'none' ? 'false' : 'true',
            'capabilities_php' => $this->capabilitiesPhp($history, $exports, $sharing, $sensitiveMode, $publishes, $accepts),
            'persistence_php' => $history ? "new ToolPersistencePolicy(enabled: true, schemaVersion: {$schemaVersion}, retentionDays: {$retentionDays}, minimumReadableSchemaVersion: {$minimumReadableSchemaVersion})" : 'ToolPersistencePolicy::disabled()',
            'export_policy_php' => $exports === [] ? 'ToolExportPolicy::disabled()' : "new ToolExportPolicy(enabled: true, formats: {$this->exportsPhp($exports)})",
            'sharing_policy_php' => $sharing ? "new ToolSharingPolicy(enabled: true, expiresAfterMinutes: {$shareExpires}, requiresAuthentication: ".($shareAuth ? 'true' : 'false').")" : 'ToolSharingPolicy::disabled()',
            'sensitive_policy_php' => $sensitiveMode === 'none' ? 'ToolSensitiveDataPolicy::none()' : "new ToolSensitiveDataPolicy(SensitiveDataMode::".$this->sensitiveModeCase($sensitiveMode).", {$this->stringListPhp($sensitiveFields)})",
            'history_contract_import' => $history ? "use App\\Core\\Tools\\History\\Contracts\\HasHistoryPolicy;\n" : '',
            'history_policy_import' => $history ? "use App\\Core\\Tools\\History\\Data\\ToolHistoryPolicy;\n" : '',
            'history_interface' => $history ? 'HasHistoryPolicy, ' : '',
            'history_policy_method' => $history ? "    public function historyPolicy(): ToolHistoryPolicy\n    {\n        return new ToolHistoryPolicy(enabled: true, retentionDays: {$retentionDays}, inputFields: ['value'], resultFields: ['result'], sensitiveFields: {$this->stringListPhp($sensitiveFields)});\n    }\n\n" : '',
            'history_markdown' => $history ? "Ativo, com retenção de {$retentionDays} dias e schema {$schemaVersion}." : 'Desabilitado por padrão.',
            'sharing_markdown' => $sharing ? "Ativo, com expiração de {$shareExpires} minutos." : 'Desabilitado por padrão.',
            'sensitive_markdown' => $sensitiveMode === 'none' ? 'Nenhum campo sensível declarado.' : $sensitiveMode.' — '.implode(', ', $sensitiveFields),
        ];
    }

    /** @param array<string, string> $context */
    private function validateOptions(array $context): void
    {
        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $context['slug'])) {
            throw new RuntimeException('O slug deve conter apenas letras minúsculas, números e hífens.');
        }

        if ((int) $this->option('schema-version') < 1
            || (int) $this->option('minimum-readable-schema-version') < 1
            || (int) $this->option('minimum-readable-schema-version') > (int) $this->option('schema-version')) {
            throw new RuntimeException('As versões de schema informadas são incompatíveis.');
        }

        if ((bool) $this->option('history') && (int) $this->option('retention-days') < 1) {
            throw new RuntimeException('Histórico ativo exige retenção mínima de um dia.');
        }

        if ((bool) $this->option('share') && ((int) $this->option('share-expires') < 5 || (int) $this->option('share-expires') > 10080)) {
            throw new RuntimeException('O compartilhamento deve expirar entre 5 minutos e 7 dias.');
        }

        $sensitiveMode = strtolower(trim((string) $this->option('sensitive-mode')));
        $sensitiveFields = $this->parseFieldList((string) $this->option('sensitive-fields'));
        if (! in_array($sensitiveMode, ['none', 'redact', 'encrypted'], true)) {
            throw new RuntimeException('O modo de dados sensíveis deve ser none, redact ou encrypted.');
        }
        if (($sensitiveMode === 'none') !== ($sensitiveFields === [])) {
            throw new RuntimeException('Campos sensíveis devem ser declarados somente com modo redact ou encrypted.');
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
            'CalculateAction.stub' => "app/Tools/{$context['class']}/Application/Actions/CalculateTool.php",
            'Controller.stub' => "app/Tools/{$context['class']}/Presentation/Controllers/ToolController.php",
            'Request.stub' => "app/Tools/{$context['class']}/Presentation/Requests/ExecuteToolRequest.php",
            'web.stub' => "app/Tools/{$context['class']}/Routes/web.php",
            'view.stub' => "app/Tools/{$context['class']}/Resources/views/index.blade.php",
            'UnitTest.stub' => "app/Tools/{$context['class']}/Tests/Unit/ToolManifestTest.php",
            'FeatureTest.stub' => "app/Tools/{$context['class']}/Tests/Feature/ToolPageTest.php",
            'README.stub' => "app/Tools/{$context['class']}/README.md",
            'RiskProfile.stub' => "app/Tools/{$context['class']}/Quality/RiskProfile.php",
            'GoldenCases.stub' => "app/Tools/{$context['class']}/Tests/Fixtures/GoldenCases.php",
            'QualityContractTest.stub' => "app/Tools/{$context['class']}/Tests/Unit/ToolQualityContractTest.php",
            'QUALITY.stub' => "app/Tools/{$context['class']}/QUALITY.md",
            'IntegrationContractTest.stub' => "app/Tools/{$context['class']}/Tests/Unit/ToolIntegrationContractTest.php",
            'CalculationInput.stub' => "app/Tools/{$context['class']}/Application/Data/CalculationInput.php",
            'Calculator.stub' => "app/Tools/{$context['class']}/Domain/Services/Calculator.php",
            'CalculationTest.stub' => "app/Tools/{$context['class']}/Tests/Unit/CalculatorTest.php",
            'ArchitectureTest.stub' => "app/Tools/{$context['class']}/Tests/Architecture/ModuleArchitectureTest.php",
            'CatalogTest.stub' => "app/Tools/{$context['class']}/Tests/Architecture/CatalogRegistrationTest.php",
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
            '{{ nature_case }}' => $context['nature_case'],
            '{{ normative_case }}' => $context['normative_case'],
            '{{ personal_data_case }}' => $context['personal_data_case'],
            '{{ integration_case }}' => $context['integration_case'],
            '{{ persistence_case }}' => $context['persistence_case'],
            '{{ processing_case }}' => $context['processing_case'],
            '{{ result_risk_case }}' => $context['result_risk_case'],
            '{{ update_frequency_case }}' => $context['update_frequency_case'],
            '{{ exports_php }}' => $context['exports_php'],
            '{{ exports_markdown }}' => $context['exports_markdown'],
            '{{ normative_value }}' => $context['normative_value'],
            '{{ publishes_php }}' => $context['publishes_php'],
            '{{ accepts_php }}' => $context['accepts_php'],
            '{{ publishes_markdown }}' => $context['publishes_markdown'],
            '{{ accepts_markdown }}' => $context['accepts_markdown'],
            '{{ supports_history_php }}' => $context['supports_history_php'],
            '{{ stores_sensitive_data_php }}' => $context['stores_sensitive_data_php'],
            '{{ capabilities_php }}' => $context['capabilities_php'],
            '{{ persistence_php }}' => $context['persistence_php'],
            '{{ export_policy_php }}' => $context['export_policy_php'],
            '{{ sharing_policy_php }}' => $context['sharing_policy_php'],
            '{{ sensitive_policy_php }}' => $context['sensitive_policy_php'],
            '{{ history_contract_import }}' => $context['history_contract_import'],
            '{{ history_policy_import }}' => $context['history_policy_import'],
            '{{ history_interface }}' => $context['history_interface'],
            '{{ history_policy_method }}' => $context['history_policy_method'],
            '{{ history_markdown }}' => $context['history_markdown'],
            '{{ sharing_markdown }}' => $context['sharing_markdown'],
            '{{ sensitive_markdown }}' => $context['sensitive_markdown'],
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

    /** @return list<string> */
    private function parseExports(string $value): array
    {
        if (trim($value) === '') {
            return [];
        }

        $exports = array_values(array_filter(array_map(
            static fn (string $format): string => strtolower(trim($format)),
            explode(',', $value),
        )));

        foreach ($exports as $format) {
            if (! preg_match('/^[a-z0-9]+$/', $format)) {
                throw new RuntimeException('Os formatos de exportação devem usar identificadores simples em letras minúsculas.');
            }
        }

        if (count($exports) !== count(array_unique($exports))) {
            throw new RuntimeException('Os formatos de exportação não podem se repetir.');
        }

        return $exports;
    }

    /** @param list<string> $exports */
    private function exportsPhp(array $exports): string
    {
        if ($exports === []) {
            return '[]';
        }

        return "['".implode("', '", $exports)."']";
    }


    /** @return list<string> */
    private function parseIntegrationContracts(string $value): array
    {
        if (trim($value) === '') {
            return [];
        }

        $contracts = array_values(array_filter(array_map(
            static fn (string $contract): string => strtolower(trim($contract)),
            explode(',', $value),
        )));

        foreach ($contracts as $contract) {
            if (! preg_match('/^[a-z][a-z0-9]*(?:-[a-z0-9]+)*:v[1-9][0-9]*$/', $contract)) {
                throw new RuntimeException('Os contratos de integração devem usar o formato nome-do-contrato:v1.');
            }
        }

        if (count($contracts) !== count(array_unique($contracts))) {
            throw new RuntimeException('Os contratos de integração não podem se repetir.');
        }

        return $contracts;
    }

    /** @param list<string> $values */
    private function stringListPhp(array $values): string
    {
        if ($values === []) {
            return '[]';
        }

        return "['".implode("', '", $values)."']";
    }

    /** @return list<string> */
    private function parseFieldList(string $value): array
    {
        if (trim($value) === '') {
            return [];
        }

        $fields = array_values(array_filter(array_map('trim', explode(',', $value))));
        foreach ($fields as $field) {
            if (! preg_match('/^[A-Za-z0-9_.-]+$/', $field)) {
                throw new RuntimeException('Campos devem usar apenas letras, números, ponto, hífen ou sublinhado.');
            }
        }
        if (count($fields) !== count(array_unique($fields))) {
            throw new RuntimeException('Campos não podem se repetir.');
        }

        return $fields;
    }

    /** @param list<string> $exports @param list<string> $publishes @param list<string> $accepts */
    private function capabilitiesPhp(bool $history, array $exports, bool $sharing, string $sensitiveMode, array $publishes, array $accepts): string
    {
        $capabilities = [];
        if ($history) {
            $capabilities[] = 'ToolCapability::History';
            $capabilities[] = 'ToolCapability::VersionedPersistence';
        }
        if ($exports !== []) $capabilities[] = 'ToolCapability::Export';
        if ($sharing) $capabilities[] = 'ToolCapability::Sharing';
        if ($sensitiveMode !== 'none') $capabilities[] = 'ToolCapability::SensitiveData';
        if ($publishes !== []) $capabilities[] = 'ToolCapability::PublishesIntegrations';
        if ($accepts !== []) $capabilities[] = 'ToolCapability::AcceptsIntegrations';

        return $capabilities === [] ? '[]' : "[\n                ".implode(",\n                ", $capabilities).",\n            ]";
    }

    private function sensitiveModeCase(string $mode): string
    {
        return match ($mode) {
            'redact' => 'Redact',
            'encrypted' => 'Encrypted',
            default => 'None',
        };
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
