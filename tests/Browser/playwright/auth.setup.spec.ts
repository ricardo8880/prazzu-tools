import { mkdirSync } from 'node:fs';
import { dirname } from 'node:path';
import { expect, test as setup } from '@playwright/test';
import { AccessProfileName, authStatePath, loadAccessManifest } from './helpers/access-profiles';

const profiles: AccessProfileName[] = ['free', 'plus', 'administrator'];

// O servidor E2E local é deliberadamente simples. Serializar os perfis evita
// que três submits de autenticação disputem a mesma instância durante o setup.
setup.describe.configure({ mode: 'serial' });

for (const profileName of profiles) {
    setup(`autentica e reutiliza a sessão ${profileName}`, async ({ page }) => {
        const profile = loadAccessManifest().profiles[profileName];
        const statePath = authStatePath(profileName);
        mkdirSync(dirname(statePath), { recursive: true });

        await page.goto('/entrar', { waitUntil: 'domcontentloaded' });

        const loginForm = page.locator('form').filter({ has: page.locator('#email') }).first();
        await expect(loginForm, 'Formulário de login não encontrado.').toBeVisible();

        await loginForm.locator('#email').fill(profile.email);
        await loginForm.locator('#password').fill(profile.password);

        const loginButton = loginForm.getByRole('button', { name: /^entrar$/i });
        await expect(loginButton, 'Botão de login não encontrado.').toBeVisible();

        // Sincronize o submit com a resposta real do endpoint. Isso evita validar
        // uma URL vazia quando a execução é encerrada enquanto a navegação ainda ocorre.
        const [loginResponse] = await Promise.all([
            page.waitForResponse(response => (
                response.request().method() === 'POST'
                && new URL(response.url()).pathname === '/entrar'
            )),
            loginButton.click(),
        ]);

        expect(
            loginResponse.status(),
            `O login do perfil ${profileName} retornou HTTP ${loginResponse.status()}.`,
        ).toBeLessThan(400);

        await page.waitForURL(url => url.pathname !== '/entrar', {
            timeout: 10_000,
            waitUntil: 'domcontentloaded',
        });

        await expect(page, `O perfil ${profileName} permaneceu na página de login.`)
            .not.toHaveURL(/\/entrar(?:\?|$)/);

        await page.context().storageState({ path: statePath });
    });
}
