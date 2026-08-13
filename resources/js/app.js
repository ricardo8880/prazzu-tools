/**
 * JavaScript global mínimo da plataforma.
 *
 * Regras específicas permanecem em app/Tools/<Modulo>/Resources/js/index.js.
 * O Vite descobre essas entradas e cada script deve atuar apenas dentro do
 * elemento [data-tool="<slug>"].
 */
document.documentElement.classList.add('js-enabled');

const THEME_STORAGE_KEY = 'prazzu-theme';
const VALID_THEMES = new Set(['light', 'dark']);
const themeButtons = document.querySelectorAll('[data-theme-value]');

function storedTheme() {
    try {
        const theme = localStorage.getItem(THEME_STORAGE_KEY);
        return VALID_THEMES.has(theme) ? theme : null;
    } catch {
        return null;
    }
}

function preferredTheme() {
    return window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
}

function resolvedTheme() {
    return storedTheme() ?? preferredTheme();
}

function applyTheme(theme) {
    const value = VALID_THEMES.has(theme) ? theme : preferredTheme();

    document.documentElement.setAttribute('data-bs-theme', value);
    document.documentElement.style.colorScheme = value;
    document.body?.setAttribute('data-theme', value);

    themeButtons.forEach((button) => {
        const isActive = button.dataset.themeValue === value;
        button.classList.toggle('is-active', isActive);
        button.setAttribute('aria-pressed', String(isActive));
    });
}

applyTheme(resolvedTheme());

themeButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const theme = button.dataset.themeValue;
        if (!VALID_THEMES.has(theme)) return;

        try { localStorage.setItem(THEME_STORAGE_KEY, theme); } catch {}
        applyTheme(theme);
    });
});

// Mantém o tema consistente ao voltar pelo cache do navegador ou ao alterá-lo em outra aba.
window.addEventListener('pageshow', () => applyTheme(resolvedTheme()));
window.addEventListener('storage', (event) => {
    if (event.key === THEME_STORAGE_KEY) {
        applyTheme(resolvedTheme());
    }
});

import { initializeToolJourneyAnalytics } from './analytics/tool-journey.js';
import { initializeAudienceContext, initializeSessionHeartbeat, initializeToolPresence } from './analytics/platform-context.js';

initializeAudienceContext();
initializeSessionHeartbeat();
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

// CTA de continuidade pós-resultado: aparece somente depois que a ferramenta entregou valor.
// Para visitantes, a conta é apresentada apenas como persistência opcional; para usuários
// autenticados, o CTA prioriza histórico quando a ferramenta realmente oferece essa capacidade.
// A frequência continua limitada a uma exibição por ferramenta durante a sessão.
const RESULT_CONTINUITY_CTA_SEEN_PREFIX = 'prazzu-result-continuity-cta-seen:';

function hasMeaningfulToolResult(toolPage) {
    return Boolean(toolPage.querySelector(
        '[data-analytics-result], [data-result-export-actions]'
    ));
}

function revealResultContinuityCta(toolPage) {
    if (!(toolPage instanceof HTMLElement) || !hasMeaningfulToolResult(toolPage)) return;

    const cta = toolPage.querySelector('[data-result-continuity-cta]');
    if (!(cta instanceof HTMLElement) || cta.classList.contains('is-visible')) return;

    const toolSlug = cta.dataset.resultContinuityTool || toolPage.dataset.tool || 'tool';
    const storageKey = `${RESULT_CONTINUITY_CTA_SEEN_PREFIX}${toolSlug}`;

    try {
        if (sessionStorage.getItem(storageKey) === '1') return;
        sessionStorage.setItem(storageKey, '1');
    } catch {}

    window.setTimeout(() => {
        cta.hidden = false;
        window.requestAnimationFrame(() => cta.classList.add('is-visible'));
    }, 520);
}

function initializeResultContinuityCta() {
    document.querySelectorAll('[data-tool]').forEach((toolPage) => revealResultContinuityCta(toolPage));

    const observer = new MutationObserver((mutations) => {
        const changedToolPages = new Set();

        for (const mutation of mutations) {
            const target = mutation.target instanceof Element ? mutation.target : mutation.target.parentElement;
            const toolPage = target?.closest?.('[data-tool]');
            if (toolPage) changedToolPages.add(toolPage);

            mutation.addedNodes.forEach((node) => {
                if (!(node instanceof Element)) return;
                const addedToolPage = node.matches('[data-tool]') ? node : node.closest('[data-tool]');
                if (addedToolPage) changedToolPages.add(addedToolPage);
            });
        }

        changedToolPages.forEach((toolPage) => revealResultContinuityCta(toolPage));
    });

    observer.observe(document.body, { childList: true, subtree: true });
}

initializeResultContinuityCta();

// Continuidade temporária para visitantes. Guardamos somente slugs durante a
// sessão do navegador; valores digitados e resultados nunca entram aqui.
const RECENT_TOOLS_STORAGE_PREFIX = 'prazzu-recent-tools-session:v2:';
const LEGACY_RECENT_TOOLS_STORAGE_KEY = 'prazzu-recent-tools-session:v1';
const RECENT_TOOLS_LIMIT = 8;

function activeVerticalSlug() {
    const vertical = document.querySelector('meta[name="vertical"]')?.content?.trim();
    return vertical && /^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(vertical) ? vertical : 'global';
}

function recentToolsStorageKey() {
    return `${RECENT_TOOLS_STORAGE_PREFIX}${activeVerticalSlug()}`;
}

function readRecentToolSlugs() {
    try {
        const currentValue = sessionStorage.getItem(recentToolsStorageKey());
        const raw = currentValue ?? sessionStorage.getItem(LEGACY_RECENT_TOOLS_STORAGE_KEY) ?? '[]';
        const parsed = JSON.parse(raw);
        if (!Array.isArray(parsed)) return [];

        return parsed
            .filter((slug) => typeof slug === 'string' && /^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(slug))
            .slice(0, RECENT_TOOLS_LIMIT);
    } catch {
        return [];
    }
}

function writeRecentToolSlugs(slugs) {
    try {
        sessionStorage.setItem(recentToolsStorageKey(), JSON.stringify(slugs.slice(0, RECENT_TOOLS_LIMIT)));
    } catch {}
}

function rememberCurrentTool() {
    const toolPage = document.querySelector('[data-tool]');
    const slug = toolPage?.dataset.tool;
    if (!slug || !/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(slug)) return;

    const recent = readRecentToolSlugs().filter((candidate) => candidate !== slug);
    writeRecentToolSlugs([slug, ...recent]);
}

function recentToolCard(tool) {
    const column = document.createElement('div');
    column.className = 'col-12 col-sm-6 col-xl-3';

    const article = document.createElement('article');
    article.className = 'prazzu-tool-card prazzu-tool-card--continuity h-100';

    const link = document.createElement('a');
    link.className = 'prazzu-tool-card__link text-decoration-none';
    try {
        const destination = new URL(tool.url, window.location.origin);
        destination.searchParams.set('source', 'home_recent_session');
        link.href = destination.toString();
    } catch {
        link.href = tool.url;
    }
    link.setAttribute('aria-label', `Abrir novamente ${tool.name}`);

    const iconTile = document.createElement('span');
    iconTile.className = `prazzu-icon-tile prazzu-icon-tile--${tool.tone || 'purple'} mb-3`;
    const icon = document.createElement('i');
    icon.className = `bi ${tool.icon}`;
    icon.setAttribute('aria-hidden', 'true');
    iconTile.append(icon);

    const title = document.createElement('h3');
    title.className = 'prazzu-tool-card__title';
    title.textContent = tool.name;

    const description = document.createElement('p');
    description.className = 'prazzu-tool-card__description';
    const toolDescription = typeof tool.description === 'string' ? tool.description : '';
    description.textContent = toolDescription.length > 105 ? `${toolDescription.slice(0, 102).trimEnd()}...` : toolDescription;

    const badge = document.createElement('span');
    badge.className = 'prazzu-badge prazzu-badge--green';
    badge.textContent = 'Usada recentemente';

    article.append(link, iconTile, title, description, badge);
    column.append(article);

    return column;
}

function renderRecentToolsOnHome() {
    const section = document.querySelector('[data-home-recent-tools]');
    const list = section?.querySelector('[data-home-recent-tools-list]');
    const catalogScript = document.querySelector('[data-home-recent-tools-catalog]');
    if (!(section instanceof HTMLElement) || !(list instanceof HTMLElement) || !(catalogScript instanceof HTMLScriptElement)) return;

    let catalog = [];
    try {
        catalog = JSON.parse(catalogScript.textContent || '[]');
    } catch {
        return;
    }
    if (!Array.isArray(catalog)) return;

    const bySlug = new Map(catalog
        .filter((tool) => tool && typeof tool.slug === 'string' && typeof tool.url === 'string')
        .map((tool) => [tool.slug, tool]));
    const validSlugs = readRecentToolSlugs().filter((slug) => bySlug.has(slug)).slice(0, 4);

    if (validSlugs.length === 0) return;

    writeRecentToolSlugs(readRecentToolSlugs().filter((slug) => bySlug.has(slug)));
    validSlugs.forEach((slug) => list.append(recentToolCard(bySlug.get(slug))));
    section.hidden = false;
}

rememberCurrentTool();
renderRecentToolsOnHome();

// Exemplos de preenchimento devem orientar sem virar dados enviados ao cálculo.
// Atua somente dentro de páginas de ferramentas e apenas quando a view não definiu
// um placeholder mais específico.
function initializeToolInputPlaceholders() {
    const toolRoot = document.querySelector('[data-tool]');
    if (!(toolRoot instanceof HTMLElement)) return;

    const exampleFor = (control) => {
        const name = (control.getAttribute('name') || '').toLowerCase();
        const type = (control.getAttribute('type') || 'text').toLowerCase();
        const inputMode = (control.getAttribute('inputmode') || '').toLowerCase();

        if (['date', 'month', 'file', 'hidden', 'checkbox', 'radio'].includes(type)) return null;
        if (/(rate|percent|percentage|aliquota|alíquota|mva|selic|markup|margin|margem)/.test(name)) return 'Ex.: 5';
        if (/(hours|hour|horas|days|day|dias|months|month|meses|dependents|employees|partners|installments|years|anos|headcount|admissions|terminations)/.test(name)) return 'Ex.: 10';
        if (/(document|cnpj|cpf)/.test(name)) return 'Ex.: 00.000.000/0000-00';
        if (/(municipality|city|cidade)/.test(name)) return 'Ex.: Campinas/SP';
        if (/(company|empresa|firm)/.test(name)) return 'Ex.: Empresa Exemplo Ltda.';
        if (/(employee_name|partner_label|contact_name|representative|payer_name|payee_name)/.test(name)) return 'Ex.: Maria Silva';
        if (/(scenario.*name|scenario_label)/.test(name)) return 'Ex.: Cenário conservador';
        if (/(salary|revenue|amount|cost|fee|gross|value|balance|expense|benefit|freight|insurance|price|sales|goal|payroll|profit|income|deduction|credit|tax)/.test(name) || inputMode === 'decimal') return 'Ex.: 5.000,00';
        if (type === 'number') return 'Ex.: 10';

        return null;
    };

    toolRoot.querySelectorAll('input.form-control:not([placeholder]), textarea.form-control:not([placeholder])').forEach((control) => {
        if (!(control instanceof HTMLInputElement || control instanceof HTMLTextAreaElement)) return;
        const example = exampleFor(control);
        if (example) control.setAttribute('placeholder', example);
    });
}

initializeToolInputPlaceholders();
