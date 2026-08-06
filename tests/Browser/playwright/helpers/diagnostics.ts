import type { Page, TestInfo } from '@playwright/test';

export type DiagnosticSeverity = 'blocking' | 'warning' | 'info';
export type DiagnosticOrigin = 'application' | 'external' | 'unknown';

export type BrowserDiagnostic = {
    type: 'console' | 'page-error' | 'request-failed' | 'http-error';
    severity: DiagnosticSeverity;
    origin: DiagnosticOrigin;
    message: string;
    url?: string;
    status?: number;
};

const defaultBaseURL = process.env.E2E_BASE_URL ?? 'http://127.0.0.1:8010';

function classifyOrigin(url: string | undefined, baseURL: string): DiagnosticOrigin {
    if (!url) {
        return 'unknown';
    }

    try {
        return new URL(url).origin === new URL(baseURL).origin
            ? 'application'
            : 'external';
    } catch {
        return 'unknown';
    }
}

function isExpectedAbort(message: string): boolean {
    const normalized = message.toLowerCase();

    return normalized.includes('err_aborted')
        || normalized.includes('ns_binding_aborted')
        || normalized.includes('cancelled')
        || normalized.includes('canceled');
}

function isTelemetryURL(url: string, baseURL: string): boolean {
    try {
        const target = new URL(url, baseURL);
        const application = new URL(baseURL);

        return target.origin === application.origin
            && (target.pathname === '/analytics' || target.pathname.startsWith('/analytics/'));
    } catch {
        return false;
    }
}

function classifyRequestFailure(
    url: string,
    message: string,
    baseURL: string,
): Pick<BrowserDiagnostic, 'origin' | 'severity'> {
    const origin = classifyOrigin(url, baseURL);
    const expectedAbort = isExpectedAbort(message);

    // Cancelamentos de recursos externos são comuns durante navegação, cache,
    // deduplicação e encerramento do contexto. Devem ser registrados sem reprovar.
    if (origin === 'external') {
        return { origin, severity: expectedAbort ? 'info' : 'warning' };
    }

    // As chamadas de analytics são telemetria assíncrona e podem ser canceladas
    // quando o cenário navega, envia o formulário ou encerra a página. Somente o
    // cancelamento esperado é não bloqueante; outros erros continuam bloqueantes.
    if (origin === 'application' && expectedAbort && isTelemetryURL(url, baseURL)) {
        return { origin, severity: 'info' };
    }

    return { origin, severity: 'blocking' };
}

export function collectBrowserDiagnostics(
    page: Page,
    baseURL: string = defaultBaseURL,
): BrowserDiagnostic[] {
    const diagnostics: BrowserDiagnostic[] = [];

    page.on('console', message => {
        if (message.type() === 'error') {
            diagnostics.push({
                type: 'console',
                severity: 'warning',
                origin: 'unknown',
                message: message.text(),
            });
        }
    });

    page.on('pageerror', error => {
        diagnostics.push({
            type: 'page-error',
            severity: 'blocking',
            origin: 'application',
            message: error.stack ?? error.message,
        });
    });

    page.on('requestfailed', request => {
        const url = request.url();
        const message = request.failure()?.errorText ?? 'Falha de rede sem detalhe.';
        const classification = classifyRequestFailure(url, message, baseURL);

        diagnostics.push({
            type: 'request-failed',
            ...classification,
            message,
            url,
        });
    });

    page.on('response', response => {
        if (response.status() >= 400) {
            const url = response.url();
            const origin = classifyOrigin(url, baseURL);

            diagnostics.push({
                type: 'http-error',
                severity: origin === 'application' && response.status() >= 500
                    ? 'blocking'
                    : 'warning',
                origin,
                message: `${response.status()} ${response.statusText()}`,
                url,
                status: response.status(),
            });
        }
    });

    return diagnostics;
}

export function blockingBrowserDiagnostics(
    diagnostics: BrowserDiagnostic[],
): BrowserDiagnostic[] {
    return diagnostics.filter(item => item.severity === 'blocking');
}

export async function attachBrowserDiagnostics(
    testInfo: TestInfo,
    diagnostics: BrowserDiagnostic[],
): Promise<void> {
    const summary = diagnostics.reduce<Record<DiagnosticSeverity, number>>(
        (counts, diagnostic) => {
            counts[diagnostic.severity] += 1;
            return counts;
        },
        { blocking: 0, warning: 0, info: 0 },
    );

    await testInfo.attach('browser-diagnostics-summary', {
        contentType: 'application/json',
        body: Buffer.from(JSON.stringify(summary, null, 2)),
    });

    await testInfo.attach('browser-diagnostics', {
        contentType: 'application/json',
        body: Buffer.from(JSON.stringify(diagnostics, null, 2)),
    });
}
