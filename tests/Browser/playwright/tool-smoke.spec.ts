import { expect, test } from '@playwright/test';
import {
    attachBrowserDiagnostics,
    blockingBrowserDiagnostics,
    collectBrowserDiagnostics,
} from './helpers/diagnostics';
import { applyE2ECorrelation, attachCorrelatedServerLogs } from './helpers/e2e-correlation';
import { loadToolCatalog } from './helpers/tool-catalog';

const catalog = loadToolCatalog();

test.describe('Smoke universal do catálogo oficial', () => {
    test(`descobriu ${catalog.tool_count} ferramentas oficiais`, async () => {
        expect(catalog.tool_count).toBe(32);
    });

    for (const tool of catalog.tools) {
        test(`${String(tool.id).padStart(2, '0')} · ${tool.slug} · carrega a página e o formulário`, async ({ page }, testInfo) => {
            const diagnostics = collectBrowserDiagnostics(page);
            const correlation = await applyE2ECorrelation(page, `${tool.slug}:page-load`);

            try {
                const response = await page.goto(tool.path, { waitUntil: 'domcontentloaded' });

                expect(response, `A ferramenta [${tool.slug}] deve produzir uma resposta HTTP.`).not.toBeNull();
                expect(response?.status(), `A ferramenta [${tool.slug}] não pode responder com erro.`).toBeLessThan(400);
                await expect(page.getByTestId(tool.test_ids.page)).toBeVisible();
                await expect(page.getByTestId(tool.test_ids.form).first()).toBeVisible();
                await expect(page.locator('body')).not.toBeEmpty();

                const blockingDiagnostics = blockingBrowserDiagnostics(diagnostics);
                expect(
                    blockingDiagnostics,
                    `Falhas técnicas bloqueantes encontradas em [${tool.slug}].`,
                ).toEqual([]);
            } finally {
                // Estes anexos precisam existir mesmo quando uma asserção de status,
                // marcação ou visibilidade falhar. Assim a automação sempre entrega
                // contexto suficiente para corrigir a aplicação posteriormente.
                await testInfo.attach('tool-smoke-summary', {
                    contentType: 'application/json',
                    body: JSON.stringify({
                        id: tool.id,
                        slug: tool.slug,
                        module: tool.module,
                        risk: tool.risk,
                        path: tool.path,
                        final_url: page.url(),
                        title: await page.title().catch(() => ''),
                    }, null, 2),
                });
                await attachBrowserDiagnostics(testInfo, diagnostics);
                await attachCorrelatedServerLogs(testInfo, correlation);
            }
        });
    }
});
