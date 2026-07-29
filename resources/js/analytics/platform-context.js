function readMeta(name) {
    return document.querySelector(`meta[name="${name}"]`)?.content || null;
}

function storageGet(key) {
    try { return sessionStorage.getItem(key); } catch { return null; }
}

function storageSet(key, value) {
    try { sessionStorage.setItem(key, value); } catch {}
}

function safeUuid() {
    if (globalThis.crypto?.randomUUID) return globalThis.crypto.randomUUID();

    const bytes = new Uint8Array(16);
    if (globalThis.crypto?.getRandomValues) {
        globalThis.crypto.getRandomValues(bytes);
    } else {
        for (let index = 0; index < bytes.length; index += 1) {
            bytes[index] = Math.floor(Math.random() * 256);
        }
    }

    bytes[6] = (bytes[6] & 0x0f) | 0x40;
    bytes[8] = (bytes[8] & 0x3f) | 0x80;
    const hex = [...bytes].map((byte) => byte.toString(16).padStart(2, '0')).join('');

    return `${hex.slice(0, 8)}-${hex.slice(8, 12)}-${hex.slice(12, 16)}-${hex.slice(16, 20)}-${hex.slice(20)}`;
}

export function initializeAudienceContext() {
    const endpoint = readMeta('analytics-audience-endpoint');
    if (!endpoint || storageGet('prazzu-audience-context') === '1') return;

    const csrf = readMeta('csrf-token');
    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone || null;
    const screenResolution = `${window.screen.width}x${window.screen.height}`;
    const language = navigator.language || null;

    fetch(endpoint, {
        method: 'POST',
        credentials: 'same-origin',
        keepalive: true,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf || '',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ timezone, screen_resolution: screenResolution, language }),
    }).then((response) => {
        if (response.ok) storageSet('prazzu-audience-context', '1');
    }).catch(() => {});
}

export function initializeToolPresence() {
    const endpoint = readMeta('analytics-tool-endpoint');
    const presenceEndpoint = readMeta('analytics-presence-endpoint');
    const tool = readMeta('analytics-tool-slug');
    if (!endpoint || !presenceEndpoint || !tool) return;

    const csrf = readMeta('csrf-token');
    let activeStartedAt = document.hidden ? null : Date.now();
    let activeMilliseconds = 0;
    const presenceId = safeUuid();

    const send = (event, properties = {}) => fetch(endpoint, {
        method: 'POST',
        credentials: 'same-origin',
        keepalive: true,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf || '',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ tool, event, ...properties }),
    }).catch(() => {});

    const sendPresence = (action, beacon = false) => {
        const body = JSON.stringify({ _token: csrf, presence_id: presenceId, tool, action });
        if (beacon && typeof navigator.sendBeacon === 'function') {
            const payload = new Blob([body], { type: 'application/json' });
            if (navigator.sendBeacon(presenceEndpoint, payload)) return;
        }

        fetch(presenceEndpoint, {
            method: 'POST',
            credentials: 'same-origin',
            keepalive: true,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf || '',
                'Accept': 'application/json',
            },
            body,
        }).catch(() => {});
    };

    sendPresence('heartbeat');
    const presenceTimer = window.setInterval(() => {
        if (!document.hidden) sendPresence('heartbeat');
    }, 10000);

    document.addEventListener('visibilitychange', () => {
        const now = Date.now();
        if (document.hidden) {
            if (activeStartedAt !== null) activeMilliseconds += now - activeStartedAt;
            activeStartedAt = null;
            return;
        }

        activeStartedAt = now;
        sendPresence('heartbeat');
    });

    window.addEventListener('pagehide', () => {
        window.clearInterval(presenceTimer);
        sendPresence('leave', true);
        if (activeStartedAt !== null) activeMilliseconds += Date.now() - activeStartedAt;
        activeStartedAt = null;
        const seconds = Math.min(86400, Math.max(0, Math.round(activeMilliseconds / 1000)));
        if (seconds >= 3) send('tool.time.spent', { metadata: { time_spent_seconds: seconds } });
    }, { once: true });
}
