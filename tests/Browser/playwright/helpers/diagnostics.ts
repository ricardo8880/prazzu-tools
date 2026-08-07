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


function isTransientPeerFailure(message: string): boolean {
    const normalized = message.toLowerCase();

    return normalized.includes('failure when receiving data from the peer')
        || normalized.includes('connection reset by peer')
        || normalized.includes('connection was reset')
        || normalized.includes('err_connection_reset');
}

function isBuildAssetURL(url: string, baseURL: string): boolean {
    try {
        const target = new URL(url, baseURL);
        const application = new URL(baseURL);

        return target.origin === application.origin
            && target.pathname.startsWith('/build/assets/');
    } catch {
        return false;
    }
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

    // Navegações, redirecionamentos e downloads cancelam requisições que já
    // deixaram de ser necessárias. Chromium, Firefox e WebKit usam mensagens
    // diferentes para esse mesmo evento. Respostas HTTP e expectativas funcionais
    // continuam detectando falhas reais da aplicação.
    if (expectedAbort) {
        return { origin, severity: 'info' };
    }

    // O servidor de desenvolvimento do PHP pode resetar esporadicamente uma
    // conexão de asset estático sob execução paralela, sobretudo no WebKit.
    // A ausência real do CSS/JS continua sendo detectada pelas expectativas de
    // página/formulário e por respostas HTTP; o reset isolado fica como aviso.
    if (isBuildAssetURL(url, baseURL) && isTransientPeerFailure(message)) {
        return { origin, severity: 'warning' };
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
