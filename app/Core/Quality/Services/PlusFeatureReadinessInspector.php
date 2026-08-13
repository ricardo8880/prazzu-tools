<?php

declare(strict_types=1);

namespace App\Core\Quality\Services;

use App\Core\Quality\Data\ArchitectureViolation;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Enums\ToolFeatureTier;
use Illuminate\Filesystem\Filesystem;
use ReflectionClass;

final readonly class PlusFeatureReadinessInspector
{
    public function __construct(private Filesystem $files) {}

    /** @param iterable<ToolModule> $modules
     *  @return list<ArchitectureViolation>
     */
    public function inspectGovernance(iterable $modules): array
    {
        $declared = [];

        foreach ($modules as $module) {
            $manifest = $module->manifest();
            foreach ($manifest->featuresFor(ToolFeatureTier::Plus) as $feature) {
                $declared[$manifest->slug.':'.$feature->key] = true;
            }
        }

        $legacy = array_values((array) config('plus_feature_governance.legacy_debt', []));
        $functional = array_values((array) config('plus_feature_governance.functional_contracts', []));
        $violations = [];
        $configFile = config_path('plus_feature_governance.php');

        $declaredKeys = array_keys($declared);
        sort($declaredKeys);
        $declaredChecksum = hash('sha256', implode("\n", $declaredKeys));
        if (! hash_equals((string) config('plus_feature_governance.declared_contracts_checksum', ''), $declaredChecksum)) {
            $violations[] = $this->violation($configFile, 'tools.plus.declared-contracts-checksum', 'O conjunto de contratos Plus declarados mudou sem atualização explícita do snapshot.');
        }

        if (count($legacy) !== count(array_unique($legacy))) {
            $violations[] = $this->violation($configFile, 'tools.plus.legacy-debt-duplicate', 'A dívida legada Plus contém entradas duplicadas.');
        }

        foreach ($legacy as $contractKey) {
            if (! isset($declared[$contractKey])) {
                $violations[] = $this->violation($configFile, 'tools.plus.legacy-debt-stale', "A dívida legada Plus contém a chave inexistente [{$contractKey}].");
            }
        }

        $legacySnapshot = $legacy;
        sort($legacySnapshot);
        $legacyChecksum = hash('sha256', implode("\n", $legacySnapshot));
        if (! hash_equals((string) config('plus_feature_governance.legacy_debt_checksum', ''), $legacyChecksum)) {
            $violations[] = $this->violation($configFile, 'tools.plus.legacy-debt-checksum', 'A composição da dívida legada Plus mudou sem atualização explícita do snapshot.');
        }

        $ceiling = (int) config('plus_feature_governance.legacy_debt_ceiling', count($legacy));
        if (count($legacy) > $ceiling) {
            $violations[] = $this->violation($configFile, 'tools.plus.legacy-debt-growth', 'A dívida legada Plus cresceu para ['.count($legacy)."] itens; o teto consolidado é [{$ceiling}].");
        }

        $strictKeys = array_values(array_diff(array_keys($declared), $legacy));
        sort($strictKeys);
        $strictContracts = count($strictKeys);
        $floor = (int) config('plus_feature_governance.strict_contract_floor', 0);
        if ($strictContracts < $floor) {
            $violations[] = $this->violation($configFile, 'tools.plus.strict-contract-regression', "A quantidade de contratos Plus estritos caiu para [{$strictContracts}]; o piso consolidado é [{$floor}].");
        }

        $snapshot = array_values((array) config('plus_feature_governance.strict_contracts', []));
        sort($snapshot);
        if ($strictKeys !== $snapshot) {
            $missing = array_values(array_diff($snapshot, $strictKeys));
            $unexpected = array_values(array_diff($strictKeys, $snapshot));
            $details = [];
            if ($missing !== []) {
                $details[] = 'ausentes: '.implode(', ', $missing);
            }
            if ($unexpected !== []) {
                $details[] = 'inesperados: '.implode(', ', $unexpected);
            }
            $violations[] = $this->violation(
                $configFile,
                'tools.plus.strict-contract-snapshot',
                'O snapshot exato dos contratos Plus saneados mudou'.($details !== [] ? ' ('.implode('; ', $details).')' : '').'. Atualize-o somente em lote explícito.',
            );
        }

        if (count($functional) !== count(array_unique($functional))) {
            $violations[] = $this->violation($configFile, 'tools.plus.functional-contract-duplicate', 'A certificação funcional Plus contém contratos duplicados.');
        }

        $functionalSnapshot = $functional;
        sort($functionalSnapshot);
        $functionalChecksum = hash('sha256', implode("\n", $functionalSnapshot));
        if (! hash_equals((string) config('plus_feature_governance.functional_contracts_checksum', ''), $functionalChecksum)) {
            $violations[] = $this->violation($configFile, 'tools.plus.functional-contracts-checksum', 'O conjunto de contratos Plus certificados funcionalmente mudou sem atualização explícita do snapshot.');
        }

        foreach ($functional as $contractKey) {
            if (! isset($declared[$contractKey])) {
                $violations[] = $this->violation($configFile, 'tools.plus.functional-contract-stale', "A certificação funcional contém a chave inexistente [{$contractKey}].");
            }
            if (in_array($contractKey, $legacy, true)) {
                $violations[] = $this->violation($configFile, 'tools.plus.functional-contract-legacy', "O contrato funcional [{$contractKey}] ainda está na dívida estrutural legada.");
            }
        }

        $functionalDebt = array_values(array_diff(array_keys($declared), $functional));
        $functionalDebtCeiling = (int) config('plus_feature_governance.functional_debt_ceiling', count($functionalDebt));
        if (count($functionalDebt) > $functionalDebtCeiling) {
            $violations[] = $this->violation($configFile, 'tools.plus.functional-debt-growth', 'A dívida funcional Plus cresceu para ['.count($functionalDebt)."] contratos; o teto é [{$functionalDebtCeiling}].");
        }

        $functionalFloor = (int) config('plus_feature_governance.functional_contract_floor', 0);
        if (count($functional) < $functionalFloor) {
            $violations[] = $this->violation($configFile, 'tools.plus.functional-contract-regression', 'A quantidade de contratos Plus certificados funcionalmente caiu para ['.count($functional)."] itens; o piso é [{$functionalFloor}].");
        }

        return $violations;
    }

    /** @return list<ArchitectureViolation> */
    public function inspect(ToolModule $module): array
    {
        $manifest = $module->manifest();
        $legacyDebt = array_fill_keys((array) config('plus_feature_governance.legacy_debt', []), true);
        $functionalContracts = array_fill_keys((array) config('plus_feature_governance.functional_contracts', []), true);
        $genericKeys = array_fill_keys((array) config('plus_feature_governance.generic_keys', []), true);
        $reflection = new ReflectionClass($module);
        $moduleFile = $reflection->getFileName();

        if ($moduleFile === false) {
            return [];
        }

        $moduleRoot = dirname($moduleFile);
        $violations = [];

        foreach ($manifest->featuresFor(ToolFeatureTier::Plus) as $feature) {
            $contractKey = $manifest->slug.':'.$feature->key;

            if (isset($legacyDebt[$contractKey])) {
                continue;
            }

            if (isset($genericKeys[$feature->key])) {
                $violations[] = $this->violation($moduleFile, 'tools.plus.generic-feature', "O recurso Plus [{$contractKey}] usa uma chave genérica; declare um benefício concreto e verificável.");
            }

            $hasFunctionalEvidence = $this->hasFunctionalTestEvidence($moduleRoot, $manifest->slug, $feature->key);

            if (! $this->hasImplementationEvidence($moduleRoot, $feature->key) && ! $hasFunctionalEvidence) {
                $violations[] = $this->violation($moduleFile, 'tools.plus.implementation', "O recurso Plus [{$contractKey}] não possui evidência de implementação fora do manifesto.");
            }

            if (! $this->hasGateEvidence($moduleRoot, $manifest->slug, $feature->key)) {
                $violations[] = $this->violation($moduleFile, 'tools.plus.gate', "O recurso Plus [{$contractKey}] não está ligado ao middleware tool.feature ou ao ToolFeatureRequestAuthorizer central.");
            }

            if (! $this->hasAccessTestEvidence($moduleRoot, $manifest->slug, $feature->key)) {
                $violations[] = $this->violation($moduleFile, 'tools.plus.tests', "O recurso Plus [{$contractKey}] precisa de teste explícito de acesso ou comportamento.");
            }

            if (isset($functionalContracts[$contractKey]) && ! $hasFunctionalEvidence) {
                $violations[] = $this->violation($moduleFile, 'tools.plus.functional-test', "O recurso Plus [{$contractKey}] foi certificado sem teste comportamental marcado com CoversPlusFeature.");
            }
        }

        return $violations;
    }

    private function hasImplementationEvidence(string $moduleRoot, string $featureKey): bool
    {
        foreach (['Application', 'Domain', 'Presentation', 'Resources', 'Infrastructure'] as $directory) {
            $path = $moduleRoot.DIRECTORY_SEPARATOR.$directory;
            if (! is_dir($path)) {
                continue;
            }

            foreach ($this->sourceFiles($path) as $file) {
                if (str_contains($this->files->get($file), $featureKey)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function hasGateEvidence(string $moduleRoot, string $slug, string $featureKey): bool
    {
        $routeFile = $moduleRoot.'/Routes/web.php';
        if (is_file($routeFile) && str_contains($this->files->get($routeFile), "tool.feature:{$slug},{$featureKey}")) {
            return true;
        }

        $presentation = $moduleRoot.'/Presentation';
        if (! is_dir($presentation)) {
            return false;
        }

        foreach ($this->sourceFiles($presentation) as $file) {
            $source = $this->files->get($file);
            if (str_contains($source, 'ToolFeatureRequestAuthorizer') && preg_match('/[\'\"]'.preg_quote($featureKey, '/').'[\'\"]/', $source) === 1) {
                return true;
            }
        }

        return false;
    }

    private function hasAccessTestEvidence(string $moduleRoot, string $slug, string $featureKey): bool
    {
        $tests = $moduleRoot.'/Tests';
        if (! is_dir($tests)) {
            return false;
        }

        $source = '';
        foreach ($this->sourceFiles($tests) as $file) {
            $source .= "\n".$this->files->get($file);
        }

        $hasPlanMatrix = str_contains($source, $featureKey)
            && str_contains($source, 'SubscriptionPlan::Free')
            && str_contains($source, 'SubscriptionPlan::Plus');

        return $hasPlanMatrix || $this->containsFunctionalMarker($source, $slug, $featureKey);
    }

    private function hasFunctionalTestEvidence(string $moduleRoot, string $slug, string $featureKey): bool
    {
        $tests = $moduleRoot.'/Tests';
        if (! is_dir($tests)) {
            return false;
        }

        foreach ($this->sourceFiles($tests) as $file) {
            $source = $this->files->get($file);
            if ($this->containsFunctionalMarker($source, $slug, $featureKey)) {
                return true;
            }
        }

        return false;
    }

    private function containsFunctionalMarker(string $source, string $slug, string $featureKey): bool
    {
        if (! str_contains($source, 'CoversPlusFeature')) {
            return false;
        }

        $pattern = '/#\[CoversPlusFeature\(\s*(?:toolSlug:\s*)?[\'\"]'.preg_quote($slug, '/').'[\'\"]\s*,\s*(?:featureKey:\s*)?[\'\"]'.preg_quote($featureKey, '/').'[\'\"]\s*\)\]/';

        return preg_match($pattern, $source) === 1;
    }

    /** @return list<string> */
    private function sourceFiles(string $directory): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }
            if (in_array($file->getExtension(), ['php', 'md'], true)) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    private function violation(string $file, string $rule, string $message): ArchitectureViolation
    {
        return new ArchitectureViolation($rule, str_replace(base_path().DIRECTORY_SEPARATOR, '', $file), 1, $message);
    }
}
