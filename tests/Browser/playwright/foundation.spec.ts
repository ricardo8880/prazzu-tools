import { expect, test } from '@playwright/test';
import { attachBrowserDiagnostics, collectBrowserDiagnostics } from './helpers/diagnostics';

test.describe('Fundação do navegador E2E', () => {
    test('abre a home em um navegador real', async ({ page }, testInfo) => {
        const diagnostics = collectBrowserDiagnostics(page);
        const response = await page.goto('/');

        expect(response, 'A navegação deve produzir uma resposta HTTP.').not.toBeNull();
        expect(response?.status()).toBeLessThan(400);
        await expect(page).toHaveTitle(/Prazzu/i);
        await expect(page.locator('body')).not.toBeEmpty();
        await attachBrowserDiagnostics(testInfo, diagnostics);
        expect(diagnostics.filter(item => item.type === 'page-error')).toEqual([]);
    });

    test('abre a ferramenta piloto sem executar regra de domínio', async ({ page }, testInfo) => {
        const diagnostics = collectBrowserDiagnostics(page);
        const response = await page.goto('/ferramentas/custo-funcionario-clt');

        expect(response).not.toBeNull();
        expect(response?.status()).toBeLessThan(400);
        await expect(page.getByTestId('tool-page-custo-funcionario-clt')).toBeVisible();
        await expect(page.getByTestId('tool-form-panel').first()).toBeVisible();
        await attachBrowserDiagnostics(testInfo, diagnostics);
        expect(diagnostics.filter(item => item.type === 'page-error')).toEqual([]);
    });
});
