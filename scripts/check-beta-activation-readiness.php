<?php

declare(strict_types=1);

use App\Core\Quality\Services\ToolActivationReadinessInspector;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Enums\ToolStatus;

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';
$inventory = require $root.'/config/product_tools.php';
$inspector = new ToolActivationReadinessInspector;
$reports = [];

foreach (($inventory['official'] ?? []) as $tool) {
    $moduleName = (string) ($tool['module'] ?? '');
    $class = 'App\\Tools\\'.$moduleName.'\\Tool';
    if (! class_exists($class)) {
        continue;
    }
    $module = new $class;
    if (! $module instanceof ToolModule || $module->manifest()->status !== ToolStatus::Beta) {
        continue;
    }
    $reports[] = $inspector->inspect($moduleName, $module, $root.'/app/Tools/'.$moduleName);
}

usort($reports, static fn ($a, $b): int => [$a->module] <=> [$b->module]);
$ready = array_values(array_filter($reports, static fn ($report): bool => $report->isReady()));
$synthetic = array_values(array_filter($reports, static fn ($report): bool => $report->hasSyntheticGoldenCases));
$missingArtifacts = array_values(array_filter($reports, static function ($report): bool {
    foreach ($report->blockers as $blocker) {
        if (str_starts_with($blocker, 'missing:')) {
            return true;
        }
    }

    return false;
}));
$openChecklist = array_values(array_filter($reports, static fn ($report): bool => $report->openChecklistItems > 0));

foreach ($reports as $report) {
    printf(
        "%s\t%s\topen=%d\tsynthetic=%s\t%s\n",
        $report->module,
        $report->slug,
        $report->openChecklistItems,
        $report->hasSyntheticGoldenCases ? 'yes' : 'no',
        $report->blockers === [] ? 'READY' : implode(',', $report->blockers),
    );
}

printf(
    "[Beta Activation] %d beta; %d prontas estruturalmente; %d com checklist aberto; %d com artefatos ausentes; %d com golden cases sintéticos.\n",
    count($reports),
    count($ready),
    count($openChecklist),
    count($missingArtifacts),
    count($synthetic),
);
