/**
 * JavaScript global mínimo da plataforma.
 *
 * Regras específicas permanecem em app/Tools/<Modulo>/Resources/js/index.js.
 * O Vite descobre essas entradas e cada script deve atuar apenas dentro do
 * elemento [data-tool="<slug>"].
 */
document.documentElement.classList.add('js-enabled');

const themeButtons = document.querySelectorAll('[data-theme-value]');
const savedTheme = (() => {
    try { return localStorage.getItem('prazzu-theme'); } catch { return null; }
})();
const preferredTheme = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';

function applyTheme(theme) {
    const value = theme === 'light' ? 'light' : 'dark';

    document.documentElement.setAttribute('data-bs-theme', value);
    document.body?.setAttribute('data-theme', value);

    themeButtons.forEach((button) => {
        const isActive = button.dataset.themeValue === value;
        button.classList.toggle('is-active', isActive);
        button.setAttribute('aria-pressed', String(isActive));
    });
}

applyTheme(savedTheme ?? preferredTheme);

themeButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const theme = button.dataset.themeValue;
        try { localStorage.setItem('prazzu-theme', theme); } catch {}
        applyTheme(theme);
    });
});

import { initializeToolJourneyAnalytics } from './analytics/tool-journey.js';
import { initializeAudienceContext, initializeToolPresence } from './analytics/platform-context.js';

initializeAudienceContext();
initializeToolPresence();
initializeToolJourneyAnalytics();

// Browser actions must be bound from an external script because the production
// Content-Security-Policy intentionally blocks inline event handlers.
document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-browser-action]');
    if (!(trigger instanceof HTMLElement)) return;

    const action = trigger.dataset.browserAction;
    if (action === 'back') {
        history.back();
    }
});

document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.dataset.confirm) return;

    if (!window.confirm(form.dataset.confirm)) {
        event.preventDefault();
    }
});

document.addEventListener('change', (event) => {
    const control = event.target.closest('[data-submit-on-change]');
    if (!(control instanceof HTMLElement) || !(control.form instanceof HTMLFormElement)) return;

    control.form.requestSubmit();
});


const TOOL_GENERATION_KEY = 'prazzu-tool-generation-pending';

function showFriendlyNotice(message, tone = 'success') {
    const existing = document.querySelector('[data-prazzu-global-notice]');
    existing?.remove();

    const notice = document.createElement('div');
    notice.dataset.prazzuGlobalNotice = 'true';
    notice.className = `alert alert-${tone} alert-dismissible shadow position-fixed top-0 start-50 translate-middle-x mt-3`;
    notice.style.zIndex = '1080';
    notice.style.width = 'min(92vw, 680px)';
    notice.setAttribute('role', tone === 'danger' ? 'alert' : 'status');
    notice.setAttribute('aria-live', tone === 'danger' ? 'assertive' : 'polite');

    const text = document.createElement('span');
    text.textContent = message;
    notice.append(text);

    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'btn-close ms-3';
    close.setAttribute('aria-label', 'Fechar');
    close.addEventListener('click', () => notice.remove());
    notice.append(close);

    document.body.append(notice);
    window.setTimeout(() => notice.remove(), tone === 'danger' ? 9000 : 6000);
}

function isFileDownloadForm(form, submitter = null) {
    if (form.hasAttribute('data-file-download') || form.closest('[data-result-export-actions]')) return true;

    const action = (submitter?.getAttribute('formaction') || form.action || '').toLowerCase();
    const label = (submitter?.textContent || '').toLowerCase();

    return /(?:export|exportar|download|baixar)/.test(action)
        && /(?:pdf|excel|xlsx|csv|word|docx|baixar|exportar)/.test(`${action} ${label}`);
}

function filenameFromResponse(response, fallback = 'arquivo') {
    const disposition = response.headers.get('content-disposition') || '';
    const utf8 = disposition.match(/filename\*=UTF-8''([^;]+)/i);
    const regular = disposition.match(/filename="?([^";]+)"?/i);
    const encoded = utf8?.[1] || regular?.[1];

    if (!encoded) return fallback;

    try { return decodeURIComponent(encoded); } catch { return encoded; }
}

async function errorMessageFromResponse(response) {
    const contentType = response.headers.get('content-type') || '';

    if (contentType.includes('application/json')) {
        const payload = await response.json().catch(() => null);
        return payload?.message || Object.values(payload?.errors || {}).flat()[0] || null;
    }

    const text = await response.text().catch(() => '');
    const documentMatch = text.match(/<title>(.*?)<\/title>/i);
    return documentMatch?.[1]?.replace(/\s+/g, ' ').trim() || null;
}

async function downloadFormResponse(form, submitter) {
    const button = submitter instanceof HTMLButtonElement || submitter instanceof HTMLInputElement ? submitter : null;
    const originalLabel = button?.innerHTML;

    if (button) {
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        button.textContent = 'Preparando arquivo...';
    }

    try {
        const method = (form.getAttribute('method') || 'POST').toUpperCase();
        const formData = new FormData(form);
        const target = new URL(submitter?.getAttribute('formaction') || form.action, window.location.href);
        if (method === 'GET') {
            for (const [key, value] of formData.entries()) target.searchParams.append(key, String(value));
        }

        const response = await fetch(target, {
            method,
            body: method === 'GET' || method === 'HEAD' ? undefined : formData,
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json, application/pdf, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/octet-stream',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (response.redirected) {
            const detail = await errorMessageFromResponse(response);
            throw new Error(detail || `A exportação foi redirecionada para ${response.url}. Verifique a sessão, o CSRF e os dados enviados.`);
        }

        if (!response.ok) {
            const detail = await errorMessageFromResponse(response);
            throw new Error(detail || 'Não foi possível gerar o arquivo. Revise os dados e tente novamente.');
        }

        const contentType = response.headers.get('content-type') || '';
        if (contentType.includes('text/html')) {
            const detail = await errorMessageFromResponse(response.clone());
            throw new Error(detail || `A exportação retornou HTML (${response.status}) em vez de um arquivo.`);
        }

        const blob = await response.blob();
        if (!blob.size) throw new Error('O arquivo foi gerado vazio. Tente novamente.');

        const fallbackExtension = /excel|xlsx/i.test(`${form.action} ${button?.textContent || ''}`) ? 'xlsx' : 'pdf';
        const filename = filenameFromResponse(response, `resultado.${fallbackExtension}`);
        const url = URL.createObjectURL(blob);
        const anchor = document.createElement('a');
        anchor.href = url;
        anchor.download = filename;
        document.body.append(anchor);
        anchor.click();
        anchor.remove();
        URL.revokeObjectURL(url);

        showFriendlyNotice('Arquivo pronto! O download foi iniciado sem sair desta página.');
    } catch (error) {
        showFriendlyNotice(error instanceof Error ? error.message : 'Não foi possível baixar o arquivo.', 'danger');
    } finally {
        if (button) {
            button.disabled = false;
            button.removeAttribute('aria-busy');
            button.innerHTML = originalLabel;
        }
    }
}

// Intercepta exportações para que erros não substituam a tela da ferramenta.
document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;

    const submitter = event.submitter instanceof HTMLElement ? event.submitter : null;
    if (isFileDownloadForm(form, submitter)) {
        event.preventDefault();
        void downloadFormResponse(form, submitter);
        return;
    }

    const toolPage = form.closest('[data-tool]');
    if (toolPage && (form.getAttribute('method') || 'get').toLowerCase() === 'post') {
        try { sessionStorage.setItem(TOOL_GENERATION_KEY, toolPage.dataset.tool || 'tool'); } catch {}
    }
});

// Após o POST de cálculo/geração, confirma que o resultado está pronto para exportar.
const currentTool = document.querySelector('[data-tool]');
const hasGeneratedResult = document.querySelector('[data-analytics-result="main"], [data-result-export-actions]');
if (currentTool && hasGeneratedResult) {
    try {
        const pendingTool = sessionStorage.getItem(TOOL_GENERATION_KEY);
        if (pendingTool === currentTool.dataset.tool) {
            sessionStorage.removeItem(TOOL_GENERATION_KEY);
            showFriendlyNotice('Tudo certo! O resultado foi gerado e os arquivos já podem ser baixados.');
        }
    } catch {}
}
