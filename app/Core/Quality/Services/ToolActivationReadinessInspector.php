<?php

declare(strict_types=1);

namespace App\Core\Quality\Services;

use App\Core\Quality\Data\ToolActivationReadinessReport;
use App\Core\Tools\Contracts\ToolModule;
use Throwable;

final class ToolActivationReadinessInspector
{
    /** @var array<int, string> */
    private const SYNTHETIC_GOLDEN_MARKERS = [
        'valid-typical-input',
        'valid-boundary-input',
        'invalid-domain-input',
        'fractional-monetary-input',
        'non-applicable-input',
        'rule-version-transition',
        'known-regression-input',
        'calculation-or-document-completed',
        'boundary-handled-without-loss',
        'stable-versioned-result',
    ];

    public function inspect(string $moduleName, ToolModule $module, string $moduleRoot): ToolActivationReadinessReport
    {
        $blockers = [];
        $qualityPath = $moduleRoot.'/QUALITY.md';
        $riskPath = $moduleRoot.'/Quality/RiskProfile.php';
        $goldenPath = $moduleRoot.'/Tests/Fixtures/GoldenCases.php';
        $qualityTestPath = $moduleRoot.'/Tests/Unit/ToolQualityContractTest.php';

        foreach ([
            'QUALITY.md' => $qualityPath,
            'RiskProfile' => $riskPath,
            'GoldenCases' => $goldenPath,
            'ToolQualityContractTest' => $qualityTestPath,
        ] as $label => $path) {
            if (! is_file($path)) {
                $blockers[] = "missing:{$label}";
            }
        }

        $openChecklistItems = 0;
        if (is_file($qualityPath)) {
            $quality = (string) file_get_contents($qualityPath);
            preg_match_all('/^- \[ \] /m', $quality, $matches);
            $openChecklistItems = count($matches[0] ?? []);
            if ($openChecklistItems > 0) {
                $blockers[] = 'quality_checklist_open:'.$openChecklistItems;
            }
        }

        $hasSyntheticGoldenCases = false;
        if (is_file($goldenPath)) {
            $goldenSource = (string) file_get_contents($goldenPath);
            foreach (self::SYNTHETIC_GOLDEN_MARKERS as $marker) {
                if (str_contains($goldenSource, $marker)) {
                    $hasSyntheticGoldenCases = true;
                    break;
                }
            }

            if (preg_match('/reference:\s*(?:self::|GoldenCases::)?PLACEHOLDER_REFERENCE/', $goldenSource) === 1) {
                $blockers[] = 'golden_reference_placeholder';
            }
            if ($hasSyntheticGoldenCases) {
                $blockers[] = 'synthetic_golden_cases';
            }
        }

        $riskClass = 'App\\Tools\\'.$moduleName.'\\Quality\\RiskProfile';
        $goldenClass = 'App\\Tools\\'.$moduleName.'\\Tests\\Fixtures\\GoldenCases';
        if (class_exists($riskClass) && class_exists($goldenClass)) {
            try {
                $profile = $riskClass::define();
                $suite = $goldenClass::suite();
                $requirements = (new ToolRiskClassifier)->classify($profile);
                (new GoldenCaseSuiteValidator)->validate($suite, $requirements);

                if ($profile->toolSlug !== $module->manifest()->slug) {
                    $blockers[] = 'risk_profile_slug_mismatch';
                }
                if ($suite->toolSlug !== $module->manifest()->slug) {
                    $blockers[] = 'golden_suite_slug_mismatch';
                }
            } catch (Throwable) {
                $blockers[] = 'quality_contract_invalid';
            }
        }

        $blockers = array_values(array_unique($blockers));
        sort($blockers);

        return new ToolActivationReadinessReport(
            module: $moduleName,
            slug: $module->manifest()->slug,
            blockers: $blockers,
            openChecklistItems: $openChecklistItems,
            hasSyntheticGoldenCases: $hasSyntheticGoldenCases,
        );
    }
}
