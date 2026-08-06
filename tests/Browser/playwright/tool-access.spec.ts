import { expect, test } from '@playwright/test';
import { authStatePath, loadAccessManifest } from './helpers/access-profiles';

const access = loadAccessManifest();

test.describe('Lote 9 - acesso, autenticação e fluxos transversais', () => {
    test('visitante é redirecionado ao tentar acessar conta, histórico e administração', async ({ browser }) => {
        const page = await browser.newPage();

        await page.goto(access.protected_paths.account);
        await expect(page).toHaveURL(/\/entrar/);

        await page.goto(access.protected_paths.history);
        await expect(page).toHaveURL(/\/entrar/);

        const adminResponse = await page.goto(access.protected_paths.administrator);
        expect(adminResponse?.status()).toBe(403);
        await page.close();
    });

    test('usuário gratuito mantém sessão e não acessa administração', async ({ browser }) => {
        const context = await browser.newContext({ storageState: authStatePath('free') });
        const page = await context.newPage();

        await page.goto(access.protected_paths.account);
        await expect(page).toHaveURL(/\/minha-conta/);
        await expect(page.locator('body')).toContainText(access.profiles.free.email);

        const adminResponse = await page.goto(access.protected_paths.administrator);
        expect(adminResponse?.status()).toBe(403);
        await context.close();
    });

    test('usuário Plus mantém sessão e acessa fluxos autenticados', async ({ browser }) => {
        const context = await browser.newContext({ storageState: authStatePath('plus') });
        const page = await context.newPage();

        await page.goto(access.protected_paths.account);
        await expect(page).toHaveURL(/\/minha-conta/);
        await expect(page.locator('body')).toContainText(access.profiles.plus.email);

        await page.goto(access.protected_paths.history);
        await expect(page).not.toHaveURL(/\/entrar/);
        await context.close();
    });

    test('administrador acessa a área interna e preserva autenticação', async ({ browser }) => {
        const context = await browser.newContext({ storageState: authStatePath('administrator') });
        const page = await context.newPage();

        const response = await page.goto(access.protected_paths.administrator);
        expect(response?.status()).toBeLessThan(400);
        await expect(page).not.toHaveURL(/\/entrar/);

        await page.goto(access.protected_paths.account);
        await expect(page.locator('body')).toContainText(access.profiles.administrator.email);
        await context.close();
    });

    test('POST sem token CSRF é rejeitado', async ({ request }) => {
        const response = await request.post('/newsletter', {
            form: { email: 'csrf-lote-9@prazzu.test' },
        });

        expect(response.status()).toBe(419);
    });
});
