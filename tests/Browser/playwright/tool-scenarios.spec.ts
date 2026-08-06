import { expect, test } from '@playwright/test';
import { attachBrowserDiagnostics, collectBrowserDiagnostics } from './helpers/diagnostics';
import { applyE2ECorrelation, attachCorrelatedServerLogs } from './helpers/e2e-correlation';
import { executeScenario, loadToolScenarios } from './helpers/tool-scenarios';

const manifest = loadToolScenarios();

test.describe('Motor declarativo de cenários', () => {
    test(`carregou cobertura mínima das ${manifest.tool_count} ferramentas`, async () => {
        expect(manifest.scenario_count).toBe(64);
        expect(manifest.tool_count).toBe(32);
        expect(manifest.coverage.valid_tools).toBe(32);
        expect(manifest.coverage.invalid_tools).toBe(32);
    });

    for (const scenario of manifest.scenarios) {
        test(`${scenario.tool_slug} · ${scenario.id}`, async ({ page }, testInfo) => {
            const diagnostics = collectBrowserDiagnostics(page);
            const correlation = await applyE2ECorrelation(page, `${scenario.tool_slug}:${scenario.id}`);

            try {
                const response = await page.goto(`/ferramentas/${scenario.tool_slug}`, { waitUntil: 'domcontentloaded' });
                expect(response?.status()).toBeLessThan(400);

                await executeScenario(page, scenario);
                await testInfo.attach('scenario-definition', {
                    contentType: 'application/json',
                    body: Buffer.from(JSON.stringify({ ...scenario, correlation }, null, 2)),
                });
                const blocking = diagnostics.filter(item => item.type === 'page-error' || item.type === 'request-failed' || (item.type === 'http-error' && (item.status ?? 0) >= 500));
                expect(blocking, `Falhas técnicas durante [${scenario.tool_slug}:${scenario.id}].`).toEqual([]);
            } finally {
                await attachBrowserDiagnostics(testInfo, diagnostics);
                await attachCorrelatedServerLogs(testInfo, correlation);
            }
        });
    }
});
