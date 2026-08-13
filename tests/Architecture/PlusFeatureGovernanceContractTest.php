<?php

declare(strict_types=1);

namespace Tests\Architecture;

use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class PlusFeatureGovernanceContractTest extends TestCase
{
    public function test_plus_catalog_snapshot_and_legacy_debt_are_consistent(): void
    {
        $declared = [];
        $genericKeys = array_fill_keys((array) config('plus_feature_governance.generic_keys', []), true);

        foreach (app(ToolRegistry::class)->manifests(false) as $manifest) {
            foreach ($manifest->featuresFor(ToolFeatureTier::Plus) as $feature) {
                $contractKey = $manifest->slug.':'.$feature->key;
                $this->assertArrayNotHasKey($contractKey, $declared, "Contrato Plus duplicado: {$contractKey}");
                $this->assertArrayNotHasKey($feature->key, $genericKeys, "Feature Plus genérica ainda declarada: {$contractKey}");
                $declared[$contractKey] = true;
            }
        }

        $legacy = array_values((array) config('plus_feature_governance.legacy_debt', []));
        $this->assertSame($legacy, array_values(array_unique($legacy)), 'legacy_debt não pode conter entradas duplicadas.');

        foreach ($legacy as $contractKey) {
            $this->assertArrayHasKey($contractKey, $declared, "Dívida legada obsoleta ou inexistente: {$contractKey}");
        }

        $this->assertCount((int) config('plus_feature_governance.declared_plus_feature_count'), $declared);
        $declaredKeys = array_keys($declared);
        sort($declaredKeys);
        $this->assertSame(
            (string) config('plus_feature_governance.declared_contracts_checksum'),
            hash('sha256', implode("\n", $declaredKeys)),
            'O catálogo Plus mudou sem atualização explícita do checksum.',
        );
        $this->assertLessThanOrEqual((int) config('plus_feature_governance.legacy_debt_ceiling'), count($legacy), 'A dívida legada Plus não pode crescer após o Lote 7.');

        $legacySnapshot = $legacy;
        sort($legacySnapshot);
        $this->assertSame(
            (string) config('plus_feature_governance.legacy_debt_checksum'),
            hash('sha256', implode("\n", $legacySnapshot)),
            'A composição de legacy_debt mudou sem atualização explícita do checksum.',
        );

        $strict = array_values(array_diff(array_keys($declared), $legacy));
        sort($strict);
        $this->assertGreaterThanOrEqual((int) config('plus_feature_governance.strict_contract_floor'), count($strict), 'Features já saneadas não podem voltar para a dívida legada.');

        $snapshot = array_values((array) config('plus_feature_governance.strict_contracts', []));
        sort($snapshot);
        $this->assertSame($snapshot, $strict, 'O conjunto exato de contratos Plus saneados só pode mudar em lote explícito.');

        $functional = array_values((array) config('plus_feature_governance.functional_contracts', []));
        $this->assertSame($functional, array_values(array_unique($functional)), 'functional_contracts não pode conter duplicatas.');
        $functionalSnapshot = $functional;
        sort($functionalSnapshot);
        $this->assertSame(
            (string) config('plus_feature_governance.functional_contracts_checksum'),
            hash('sha256', implode("\n", $functionalSnapshot)),
            'A composição da certificação funcional mudou sem atualização explícita do checksum.',
        );
        foreach ($functional as $contractKey) {
            $this->assertArrayHasKey($contractKey, $declared, "Contrato funcional Plus inexistente: {$contractKey}");
            $this->assertNotContains($contractKey, $legacy, "Contrato funcional Plus ainda legado: {$contractKey}");
        }

        $functionalDebt = array_values(array_diff(array_keys($declared), $functional));
        $this->assertLessThanOrEqual(
            (int) config('plus_feature_governance.functional_debt_ceiling'),
            count($functionalDebt),
            'Uma nova feature Plus não pode aumentar a dívida funcional.',
        );
        $this->assertGreaterThanOrEqual(
            (int) config('plus_feature_governance.functional_contract_floor'),
            count($functional),
            'Contratos Plus certificados funcionalmente não podem regredir.',
        );
        $this->assertSame($strict, $functionalSnapshot, 'A auditoria final exige que todo contrato estrutural estrito também esteja certificado funcionalmente.');
    }
}
