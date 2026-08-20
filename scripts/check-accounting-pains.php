<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$inventory = require $root.'/config/product_tools.php';
$modules = require $root.'/config/tools/modules.php';
$plus = require $root.'/config/plus_feature_governance.php';

$errors = [];
$official = $inventory['official'] ?? [];
$bySlug = [];

foreach ($official as $tool) {
    $slug = (string) ($tool['slug'] ?? '');
    if ($slug === '') {
        $errors[] = 'Ferramenta oficial sem slug.';
        continue;
    }
    if (isset($bySlug[$slug])) {
        $errors[] = "Slug oficial duplicado: {$slug}.";
    }
    $bySlug[$slug] = $tool;
}

$expectedCount = (int) ($inventory['expected_module_count'] ?? 0);
if (count($official) !== $expectedCount) {
    $errors[] = sprintf('Inventário divergente: expected_module_count=%d, official=%d.', $expectedCount, count($official));
}

$accountingCount = count(array_filter($official, static fn (array $tool): bool => ($tool['vertical'] ?? null) === 'contabilidade'));
$rhCount = count(array_filter($official, static fn (array $tool): bool => ($tool['vertical'] ?? null) === 'rh'));
if ($accountingCount !== 49 || $rhCount !== 1) {
    $errors[] = "Distribuição por vertical inesperada: contabilidade={$accountingCount}, rh={$rhCount}.";
}

$releaseOrders = array_map(static fn (array $tool): int => (int) ($tool['release_order'] ?? 0), $official);
sort($releaseOrders);
if ($releaseOrders !== range(1, 50)) {
    $errors[] = 'release_order deve formar exatamente a sequência 1..50.';
}

$pains = [
    'Nota Fiscal' => ['calculadora-retencoes-nota-fiscal', 'conversor-fiscal-xml'],
    'CFOP' => ['consultor-validador-cfop'],
    'Certificado Digital' => ['analisador-certificado-digital-a1'],
    'Simples Nacional' => ['calculadora-simples-nacional'],
    'Lucro Presumido' => ['calculadora-irpj-csll-lucro-presumido'],
    'Lucro Real' => ['calculadora-lucro-real'],
    'Fator R' => ['simulador-fator-r'],
    'Reforma Tributária' => ['simulador-reforma-tributaria-consumo'],
    'PIS/Cofins' => ['calculadora-pis-cofins'],
    'SEFAZ' => ['validador-fiscal-sefaz'],
    'ICMS' => ['calculadora-icms-proprio', 'calculadora-icms-st'],
    'ECAD' => ['simulador-ecad-direitos-autorais'],
    'DIFAL' => ['calculadora-difal-icms'],
];

foreach ($pains as $pain => $slugs) {
    foreach ($slugs as $slug) {
        if (! isset($bySlug[$slug])) {
            $errors[] = "Dor [{$pain}] sem ferramenta oficial esperada [{$slug}].";
            continue;
        }

        $tool = $bySlug[$slug];
        if (($tool['vertical'] ?? null) !== 'contabilidade') {
            $errors[] = "Ferramenta [{$slug}] da dor [{$pain}] vazou da vertical contabilidade.";
        }

        $module = (string) ($tool['module'] ?? '');
        foreach ([
            "app/Tools/{$module}/Tool.php",
            "app/Tools/{$module}/Routes/web.php",
            "app/Tools/{$module}/Resources/views/index.blade.php",
        ] as $relative) {
            if (! is_file($root.'/'.$relative)) {
                $errors[] = "Artefato obrigatório ausente para [{$slug}]: {$relative}.";
            }
        }

        $pageDocs = [
            "app/Tools/{$module}/pages/index.blade.md",
            "docs/pages/app/Tools/{$module}/pages/index.blade.md",
        ];
        if (! array_filter($pageDocs, static fn (string $relative): bool => is_file($root.'/'.$relative))) {
            $errors[] = "Documentação da página ausente para [{$slug}].";
        }
    }
}

$registeredModuleClasses = [];
$collectModuleClasses = static function (array $items) use (&$registeredModuleClasses, &$collectModuleClasses): void {
    foreach ($items as $item) {
        if (is_array($item)) {
            $collectModuleClasses($item);
            continue;
        }
        if (is_string($item)) {
            $registeredModuleClasses[] = ltrim($item, '\\');
        }
    }
};
$collectModuleClasses(is_array($modules) ? $modules : []);
foreach ($pains as $slugs) {
    foreach ($slugs as $slug) {
        if (! isset($bySlug[$slug])) {
            continue;
        }
        $expectedClass = 'App\\Tools\\'.$bySlug[$slug]['module'].'\\Tool';
        if (! in_array($expectedClass, $registeredModuleClasses, true)) {
            $errors[] = "Módulo não registrado em config/tools/modules.php: {$expectedClass}.";
        }
    }
}

if ((int) ($plus['catalog_tool_count'] ?? 0) !== 50) {
    $errors[] = 'Governança Plus deve declarar catalog_tool_count=50.';
}
$declaredPlusFeatureCount = (int) ($plus['declared_plus_feature_count'] ?? 0);
if ($declaredPlusFeatureCount <= 0) {
    $errors[] = 'Governança Plus deve declarar uma contagem positiva de contratos Plus.';
}
if ((array) ($plus['legacy_debt'] ?? []) !== []) {
    $errors[] = 'Governança Plus voltou a possuir dívida legada.';
}
if (count((array) ($plus['strict_contracts'] ?? [])) !== $declaredPlusFeatureCount) {
    $errors[] = 'Governança Plus deve manter contratos estritos iguais à contagem declarada.';
}
if (count((array) ($plus['functional_contracts'] ?? [])) !== $declaredPlusFeatureCount) {
    $errors[] = 'Governança Plus deve manter contratos funcionais iguais à contagem declarada.';
}

foreach ([
    'app/Core/Tax/Fiscal/CfopCatalog.php',
    'app/Core/Tax/Normative/ActualProfitIncomeTaxRule.php',
] as $sharedRule) {
    if (! is_file($root.'/'.$sharedRule)) {
        $errors[] = "Reutilização de Core consolidada ausente: {$sharedRule}.";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "[Dores contábeis] Auditoria final falhou:\n\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo sprintf(
    "[Dores contábeis] OK: %d dores agrupadas, %d ferramentas oficiais, %d em Contabilidade, %d em RH, Plus %d/%d e dívida zero.\n",
    count($pains),
    count($official),
    $accountingCount,
    $rhCount,
    count((array) ($plus['strict_contracts'] ?? [])),
    count((array) ($plus['functional_contracts'] ?? [])),
);
