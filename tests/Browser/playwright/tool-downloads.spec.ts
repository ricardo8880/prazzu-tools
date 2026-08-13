import { expect, test } from '@playwright/test';
import { attachBrowserDiagnostics, blockingBrowserDiagnostics, collectBrowserDiagnostics } from './helpers/diagnostics';
import { executeAndValidateDownload } from './helpers/download-validator';
import { applyE2ECorrelation, attachCorrelatedServerLogs } from './helpers/e2e-correlation';
import { executeScenario, loadToolScenarios, toolPublicPath } from './helpers/tool-scenarios';

const requestedToolSlug = process.env.E2E_TOOL_SLUG?.trim() || null;
const downloadScenarios = loadToolScenarios().scenarios.filter(scenario => scenario.downloads.length > 0);
const scenarios = requestedToolSlug
    ? downloadScenarios.filter(scenario => scenario.tool_slug === requestedToolSlug)
    : downloadScenarios;

test.describe('Validação profunda de downloads', () => {
    test(requestedToolSlug
        ? `carregou downloads declarados para ${requestedToolSlug}`
        : `carregou ${scenarios.length} cenários com downloads`, async () => {
        if (requestedToolSlug) {
            test.skip(scenarios.length === 0, `[E2E TOOL] ${requestedToolSlug} não declara downloads para validação profunda.`);
            expect(scenarios.length).toBeGreaterThanOrEqual(1);
            return;
        }
        expect(scenarios.length).toBeGreaterThanOrEqual(1);
    });

    for (const scenario of scenarios) {
        test(`${scenario.tool_slug} · ${scenario.id} · downloads`, async ({ page }, testInfo) => {
            const diagnostics = collectBrowserDiagnostics(page);
            const correlation = await applyE2ECorrelation(page, `${scenario.tool_slug}:${scenario.id}:downloads`);

            try {
                const response = await page.goto(toolPublicPath(scenario), { waitUntil: 'domcontentloaded' });
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

                const blocking = blockingBrowserDiagnostics(diagnostics);
                expect(blocking, `Falhas técnicas durante downloads de [${scenario.tool_slug}:${scenario.id}].`).toEqual([]);
            } finally {
                await attachBrowserDiagnostics(testInfo, diagnostics);
                await attachCorrelatedServerLogs(testInfo, correlation);
            }
        });
    }
});
