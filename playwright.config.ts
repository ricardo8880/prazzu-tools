import { defineConfig, devices } from '@playwright/test';

const baseURL = process.env.E2E_BASE_URL ?? 'http://127.0.0.1:8010';
const readinessURL = process.env.E2E_READINESS_URL ?? `${baseURL.replace(/\/$/, '')}/robots.txt`;
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
        command: 'php artisan serve --host=127.0.0.1 --port=8010 --tries=1 --no-reload',
        env: { APP_ENV: 'e2e' },
        // Use a static public asset for process readiness. Laravel's /up route
        // renders a framework health view and cached routes may embed machine-specific
        // absolute paths, which makes that endpoint unsuitable for Playwright startup.
        url: readinessURL,
        reuseExistingServer: !process.env.CI,
        timeout: 120_000,
        stdout: 'pipe',
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
