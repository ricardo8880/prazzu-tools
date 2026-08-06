import { defineConfig, devices } from '@playwright/test';

const baseURL = process.env.E2E_BASE_URL ?? 'http://127.0.0.1:8010';
const healthURL = process.env.E2E_HEALTH_URL ?? `${baseURL.replace(/\/$/, '')}/up`;
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
        // Use Laravel's lightweight health endpoint instead of the homepage.
        // The homepage can be slow or return an application error while optional
        // services are still booting, which would make Playwright wait until timeout.
        url: healthURL,
        reuseExistingServer: !process.env.CI,
        timeout: 120_000,
        stdout: 'pipe',
        stderr: 'pipe',
    },
    projects: [
        {
            name: 'auth-setup',
            testMatch: /auth\.setup\.spec\.ts/,
            use: { ...devices['Desktop Chrome'] },
        },
        {
            name: 'chromium-desktop',
            testIgnore: [/auth\.setup\.spec\.ts/, /tool-access\.spec\.ts/, /tool-responsive\.spec\.ts/, /tool-exploratory\.spec\.ts/],
            use: { ...devices['Desktop Chrome'] },
        },
        {
            name: 'firefox-desktop',
            testIgnore: [/auth\.setup\.spec\.ts/, /tool-access\.spec\.ts/, /tool-responsive\.spec\.ts/, /tool-exploratory\.spec\.ts/],
            use: { ...devices['Desktop Firefox'] },
        },
        {
            name: 'webkit-desktop',
            testIgnore: [/auth\.setup\.spec\.ts/, /tool-access\.spec\.ts/, /tool-responsive\.spec\.ts/, /tool-exploratory\.spec\.ts/],
            use: { ...devices['Desktop Safari'] },
        },
        {
            name: 'mobile-chromium',
            testMatch: /tool-responsive\.spec\.ts/,
            use: { ...devices['Pixel 7'] },
        },
        {
            name: 'tablet-webkit',
            testMatch: /tool-responsive\.spec\.ts/,
            use: { ...devices['iPad (gen 7)'] },
        },
        {
            name: 'exploratory-controlled',
            testMatch: /tool-exploratory\.spec\.ts/,
            retries: 0,
            use: { ...devices['Desktop Chrome'] },
        },
        {
            name: 'access-transversal',
            testMatch: /tool-access\.spec\.ts/,
            dependencies: ['auth-setup'],
            use: { ...devices['Desktop Chrome'] },
        },
    ],
});
