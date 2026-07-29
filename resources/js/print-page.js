document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-browser-action]');
    if (!(trigger instanceof HTMLElement)) return;

    if (trigger.dataset.browserAction === 'print') {
        window.print();
    } else if (trigger.dataset.browserAction === 'back') {
        history.back();
    }
});
