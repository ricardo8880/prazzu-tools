import { expect, test } from '@playwright/test';
import { loadToolCatalog } from './helpers/tool-catalog';

const catalog = loadToolCatalog();
const criticalTools = catalog.tools.filter(tool => ['critical', 'high'].includes(tool.risk));

test.describe('Responsividade crítica do catálogo', () => {
    test(`selecionou ${criticalTools.length} ferramentas críticas ou de alto risco`, async () => {
        expect(criticalTools.length).toBeGreaterThan(0);
    });

    for (const tool of criticalTools) {
        test(`${tool.slug} mantém página e formulário utilizáveis`, async ({ page }) => {
            const response = await page.goto(tool.path, { waitUntil: 'domcontentloaded' });
            expect(response?.status()).toBeLessThan(400);
            await expect(page.getByTestId(tool.test_ids.page)).toBeVisible();
            await expect(page.getByTestId(tool.test_ids.form).first()).toBeVisible();
            await expect(page.locator('body')).toHaveCSS('overflow-x', /^(visible|hidden|clip|auto)$/);

            const viewport = page.viewportSize();
            if (viewport) {
                const scrollWidth = await page.locator('body').evaluate(element => element.scrollWidth);
                expect(scrollWidth, `A ferramenta [${tool.slug}] não deve criar estouro horizontal relevante.`)
                    .toBeLessThanOrEqual(viewport.width + 8);
            }
        });
    }
});
