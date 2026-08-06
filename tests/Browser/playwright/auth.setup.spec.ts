import { mkdirSync } from 'node:fs';
import { dirname } from 'node:path';
import { expect, test as setup } from '@playwright/test';
import { AccessProfileName, authStatePath, loadAccessManifest } from './helpers/access-profiles';

const profiles: AccessProfileName[] = ['free', 'plus', 'administrator'];

for (const profileName of profiles) {
    setup(`autentica e reutiliza a sessão ${profileName}`, async ({ page }) => {
        const profile = loadAccessManifest().profiles[profileName];
        const statePath = authStatePath(profileName);
        mkdirSync(dirname(statePath), { recursive: true });

        await page.goto('/entrar');
        await page.getByLabel(/e-mail/i).fill(profile.email);
        await page.getByLabel(/senha/i).fill(profile.password);
        await page.getByRole('button', { name: /entrar/i }).click();

        await expect(page).not.toHaveURL(/\/entrar(?:\?|$)/);
        await page.context().storageState({ path: statePath });
    });
}
