/**
 * JavaScript global mínimo da plataforma.
 *
 * Regras específicas devem ficar em resources/js/tools/<slug>.js e cada script
 * deve atuar apenas dentro do elemento [data-tool="<slug>"].
 */
document.documentElement.classList.add('js-enabled');

const themeButtons = document.querySelectorAll('[data-theme-value]');
const savedTheme = localStorage.getItem('prazzu-theme');
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
        localStorage.setItem('prazzu-theme', theme);
        applyTheme(theme);
    });
});
