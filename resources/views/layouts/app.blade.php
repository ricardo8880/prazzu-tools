<!doctype html>

<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Prazzu Tools'))</title>
    <meta
        name="description"
        content="@yield('meta_description', 'Ferramentas contábeis práticas, confiáveis e acessíveis.')"
    >
    <meta name="robots" content="@yield('meta_robots', 'index,follow')">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">

    <meta property="og:locale" content="pt_BR">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ config('app.name', 'Prazzu Tools') }}">
    <meta
        property="og:title"
        content="@yield('og_title', trim($__env->yieldContent('title', config('app.name', 'Prazzu Tools'))))"
    >
    <meta
        property="og:description"
        content="@yield('og_description', trim($__env->yieldContent('meta_description', 'Ferramentas contábeis práticas, confiáveis e acessíveis.')))"
    >
    <meta property="og:url" content="@yield('canonical_url', url()->current())">

    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif

    @hasSection('og_image')
        <meta name="twitter:card" content="summary_large_image">
    @else
        <meta name="twitter:card" content="summary">
    @endif

    <meta
        name="twitter:title"
        content="@yield('og_title', trim($__env->yieldContent('title', config('app.name', 'Prazzu Tools'))))"
    >
    <meta
        name="twitter:description"
        content="@yield('og_description', trim($__env->yieldContent('meta_description', 'Ferramentas contábeis práticas, confiáveis e acessíveis.')))"
    >

    @hasSection('og_image')
        <meta name="twitter:image" content="@yield('og_image')">
    @endif

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')


</head>
<body class="prazzu-app">
<a
    class="visually-hidden-focusable prazzu-skip-link"
    href="#main-content"
>
    Pular para o conteúdo
</a>


<div class="prazzu-shell">
    <x-layout.header />

    <div class="prazzu-shell__body">
        <x-layout.left-sidebar />

        <main id="main-content" class="prazzu-main" tabindex="-1">
            @if (session('status'))
                <div
                    class="alert alert-success prazzu-flash-alert alert-dismissible fade show"
                    role="status"
                >
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <span>{{ session('status') }}</span>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Fechar"
                    ></button>
                </div>
            @endif

            @if (session('access_warning'))
                <div class="alert alert-warning prazzu-flash-alert alert-dismissible fade show" role="alert">
                    <i class="bi bi-lock-fill" aria-hidden="true"></i>
                    <span>{{ session('access_warning') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <x-layout.right-sidebar />
    </div>

    <x-layout.footer />
</div>

<x-layout.mobile-navigation />

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

<script>
(() => {
    if (sessionStorage.getItem('prazzu-audience-context') === '1') return;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone || null;
    const screenResolution = `${window.screen.width}x${window.screen.height}`;
    const language = navigator.language || null;
    fetch(@json(route('analytics.audience.capture')), {
        method: 'POST',
        credentials: 'same-origin',
        keepalive: true,
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'},
        body: JSON.stringify({timezone, screen_resolution: screenResolution, language})
    }).then((response) => {
        if (response.ok) sessionStorage.setItem('prazzu-audience-context', '1');
    }).catch(() => {});
})();
</script>

@php
    $analyticsRouteName = request()->route()?->getName();
    $analyticsToolSlug = is_string($analyticsRouteName) && str_starts_with($analyticsRouteName, 'tools.')
        ? (explode('.', $analyticsRouteName)[1] ?? null)
        : null;
@endphp
@if($analyticsToolSlug)
<script>
(() => {
    const endpoint = @json(route('analytics.tools.track'));
    const presenceEndpoint = @json(route('analytics.tools.presence'));
    const tool = @json($analyticsToolSlug);
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const startedAt = Date.now();
    const presenceId = crypto.randomUUID();
    const send = (event, properties = {}) => fetch(endpoint, {
        method: 'POST', credentials: 'same-origin', keepalive: true,
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'},
        body: JSON.stringify({tool, event, ...properties})
    }).catch(() => {});

    const sendPresence = (action, beacon = false) => {
        const body = JSON.stringify({_token: csrf, presence_id: presenceId, tool, action});
        if (beacon && navigator.sendBeacon) {
            const payload = new Blob([body], {type: 'application/json'});
            navigator.sendBeacon(presenceEndpoint, payload);
            return;
        }
        fetch(presenceEndpoint, {
            method: 'POST', credentials: 'same-origin', keepalive: true,
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'},
            body
        }).catch(() => {});
    };

    sendPresence('heartbeat');
    const presenceTimer = window.setInterval(() => {
        if (!document.hidden) sendPresence('heartbeat');
    }, 10000);

    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) sendPresence('heartbeat');
    });

    document.querySelectorAll('form').forEach((form) => {
        if ((form.method || 'get').toLowerCase() !== 'get') {
            form.addEventListener('submit', () => send('tool.calculation.started'), {once: true});
        }
    });
    window.addEventListener('pagehide', () => {
        window.clearInterval(presenceTimer);
        sendPresence('leave', true);
        const seconds = Math.min(86400, Math.max(0, Math.round((Date.now() - startedAt) / 1000)));
        if (seconds >= 3) send('tool.time.spent', {seconds});
    }, {once: true});
})();
</script>
@endif

</body>
</html>
