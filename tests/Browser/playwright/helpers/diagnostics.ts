import type { Page, TestInfo } from '@playwright/test';

export type BrowserDiagnostic = {
    type: 'console' | 'page-error' | 'request-failed' | 'http-error';
    message: string;
    url?: string;
    status?: number;
};

export function collectBrowserDiagnostics(page: Page): BrowserDiagnostic[] {
    const diagnostics: BrowserDiagnostic[] = [];

    page.on('console', message => {
        if (message.type() === 'error') {
            diagnostics.push({ type: 'console', message: message.text() });
        }
    });

    page.on('pageerror', error => {
        diagnostics.push({ type: 'page-error', message: error.stack ?? error.message });
    });

    page.on('requestfailed', request => {
        diagnostics.push({
            type: 'request-failed',
            message: request.failure()?.errorText ?? 'Falha de rede sem detalhe.',
            url: request.url(),
        });
    });

    page.on('response', response => {
        if (response.status() >= 400) {
            diagnostics.push({
                type: 'http-error',
                message: `${response.status()} ${response.statusText()}`,
                url: response.url(),
                status: response.status(),
            });
        }
    });

    return diagnostics;
}

export async function attachBrowserDiagnostics(
    testInfo: TestInfo,
    diagnostics: BrowserDiagnostic[],
): Promise<void> {
    await testInfo.attach('browser-diagnostics', {
        contentType: 'application/json',
        body: Buffer.from(JSON.stringify(diagnostics, null, 2)),
    });
}
