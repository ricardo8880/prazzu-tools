import { defineConfig, devices } from '@playwright/test';

const baseURL = process.env.E2E_BASE_URL ?? 'http://127.0.0.1:8010';
const artifacts = 'storage/app/e2e/artifacts';

export default defineConfig({
    testDir: './tests/Browser/playwright',
    globalSetup: './tests/Browser/playwright/global-setup.ts',
    fullyParallel: true,
    forbidOnly: Boolean(process.env.CI),
    retries: process.env.CI ? 2 : 0,
    workers: process.env.CI ? 2 : undefined,
    timeout: 30_000,
    expect: { timeout: 5_000 },
    outputDir: `${artifacts}/results`,
    reporter: [
        ['list'],
        ['html', { outputFolder: `${artifacts}/report`, open: 'never' }],
        ['json', { outputFile: `${artifacts}/results.json` }],
    ],
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
        url: baseURL,
        reuseExistingServer: !process.env.CI,
        timeout: 120_000,
        stdout: 'pipe',
        stderr: 'pipe',
    },
    projects: [
        {
            name: 'chromium-desktop',
            use: { ...devices['Desktop Chrome'] },
        },
    ],
});
