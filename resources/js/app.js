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
    if (action === 'print') {
        window.print();
    } else if (action === 'back') {
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
