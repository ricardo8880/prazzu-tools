import { expect, test } from '@playwright/test';
import { attachBrowserDiagnostics, collectBrowserDiagnostics } from './helpers/diagnostics';
import { loadToolCatalog } from './helpers/tool-catalog';

const catalog = loadToolCatalog();

test.describe('Smoke universal do catálogo oficial', () => {
    test(`descobriu ${catalog.tool_count} ferramentas oficiais`, async () => {
        expect(catalog.tool_count).toBe(32);
    });

    for (const tool of catalog.tools) {
        test(`${String(tool.id).padStart(2, '0')} · ${tool.slug} · carrega a página e o formulário`, async ({ page }, testInfo) => {
            const diagnostics = collectBrowserDiagnostics(page);
            const response = await page.goto(tool.path, { waitUntil: 'domcontentloaded' });

            expect(response, `A ferramenta [${tool.slug}] deve produzir uma resposta HTTP.`).not.toBeNull();
            expect(response?.status(), `A ferramenta [${tool.slug}] não pode responder com erro.`).toBeLessThan(400);
            await expect(page.getByTestId(tool.test_ids.page)).toBeVisible();
            await expect(page.getByTestId(tool.test_ids.form).first()).toBeVisible();
            await expect(page.locator('body')).not.toBeEmpty();

            await testInfo.attach('tool-smoke-summary', {
                contentType: 'application/json',
                body: Buffer.from(JSON.stringify({
                    id: tool.id,
                    slug: tool.slug,
                    module: tool.module,
                    risk: tool.risk,
                    path: tool.path,
                    status: response?.status(),
                    title: await page.title(),
                }, null, 2)),
            });
            await attachBrowserDiagnostics(testInfo, diagnostics);

            const blockingDiagnostics = diagnostics.filter(item =>
                item.type === 'page-error'
                || item.type === 'request-failed'
                || (item.type === 'http-error' && (item.status ?? 0) >= 500),
            );
            expect(blockingDiagnostics, `Falhas técnicas encontradas em [${tool.slug}].`).toEqual([]);
        });
    }
});
