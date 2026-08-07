import { defineConfig, devices } from '@playwright/test';

const baseURL = process.env.E2E_BASE_URL ?? 'http://127.0.0.1:8010';
const healthURL = process.env.E2E_HEALTH_URL ?? `${baseURL.replace(/\/$/, '')}/up`;
const artifacts = 'storage/app/e2e/artifacts';

export default defineConfig({
    testDir: './tests/Browser/playwright',
    globalSetup: './tests/Browser/playwright/global-setup.ts',
    fullyParallel: false,
    forbidOnly: Boolean(process.env.CI),
    retries: 0,
    workers: 1,
    timeout: 45_000,
    expect: { timeout: 5_000 },
    outputDir: `${artifacts}/results`,
    use: {
        baseURL,
        locale: 'pt-BR',
        timezoneId: 'America/Sao_Paulo',
        trace: 'retain-on-failure',
        screenshot: 'only-on-failure',
        video: 'retain-on-failure',
        acceptDownloads: true,
        actionTimeout: 10_000,
        navigationTimeout: 20_000,
    },
    webServer: {
        command: 'php artisan serve --env=e2e --host=127.0.0.1 --port=8010',
        url: healthURL,
        reuseExistingServer: !process.env.CI,
        timeout: 120_000,
        stdout: 'ignore',
        stderr: 'pipe',
    },
    projects: [
        {
            name: 'chromium-desktop',
            testMatch: [/tool-actions\.spec\.ts/, /tool-downloads\.spec\.ts/],
            use: { ...devices['Desktop Chrome'] },
        },
    ],
});
