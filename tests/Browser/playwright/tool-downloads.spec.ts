import { expect, test } from '@playwright/test';
import { attachBrowserDiagnostics, collectBrowserDiagnostics } from './helpers/diagnostics';
import { executeAndValidateDownload } from './helpers/download-validator';
import { applyE2ECorrelation, attachCorrelatedServerLogs } from './helpers/e2e-correlation';
import { executeScenario, loadToolScenarios } from './helpers/tool-scenarios';

const scenarios = loadToolScenarios().scenarios.filter(scenario => scenario.downloads.length > 0);

test.describe('Validação profunda de downloads', () => {
    test(`carregou ${scenarios.length} cenários com downloads`, async () => {
        expect(scenarios.length).toBeGreaterThanOrEqual(1);
    });

    for (const scenario of scenarios) {
        test(`${scenario.tool_slug} · ${scenario.id} · downloads`, async ({ page }, testInfo) => {
            const diagnostics = collectBrowserDiagnostics(page);
            const correlation = await applyE2ECorrelation(page, `${scenario.tool_slug}:${scenario.id}:downloads`);

            try {
                const response = await page.goto(`/ferramentas/${scenario.tool_slug}`, { waitUntil: 'domcontentloaded' });
                expect(response?.status()).toBeLessThan(400);
                await executeScenario(page, scenario);

                const validations = [];
                for (const download of scenario.downloads) {
                    validations.push(await executeAndValidateDownload(page, download, testInfo));
                }

                await testInfo.attach('download-validation-summary', {
                    contentType: 'application/json',
                    body: Buffer.from(JSON.stringify({ scenario: scenario.id, correlation, validations }, null, 2)),
                });

                const blocking = diagnostics.filter(item => item.type === 'page-error' || item.type === 'request-failed' || (item.type === 'http-error' && (item.status ?? 0) >= 500));
                expect(blocking, `Falhas técnicas durante downloads de [${scenario.tool_slug}:${scenario.id}].`).toEqual([]);
            } finally {
                await attachBrowserDiagnostics(testInfo, diagnostics);
                await attachCorrelatedServerLogs(testInfo, correlation);
            }
        });
    }
});
