<!doctype html>

<html lang="pt-BR" data-bs-theme="dark">
<head>
    @php($verticalSeo = app(\App\Core\Seo\Application\VerticalSeoContext::class)->defaults())
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $verticalSeo['title'])</title>
    <meta
        name="description"
        content="@yield('meta_description', $verticalSeo['description'])"
    >
    @if ($verticalSeo['keywords'] !== [])
        <meta name="keywords" content="{{ implode(', ', $verticalSeo['keywords']) }}">
    @endif
    <meta name="vertical" content="{{ $verticalSeo['vertical'] ?? 'global' }}">
    <meta name="robots" content="@yield('meta_robots', 'index,follow')">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">

    <meta property="og:locale" content="pt_BR">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ config('app.name', 'Prazzu Tools') }}">
    <meta
        property="og:title"
        content="@yield('og_title', trim($__env->yieldContent('title', $verticalSeo['title'])))"
    >
    <meta
        property="og:description"
        content="@yield('og_description', trim($__env->yieldContent('meta_description', $verticalSeo['description'])))"
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
        content="@yield('og_title', trim($__env->yieldContent('title', $verticalSeo['title'])))"
    >
    <meta
        name="twitter:description"
        content="@yield('og_description', trim($__env->yieldContent('meta_description', $verticalSeo['description'])))"
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

    <meta name="analytics-audience-endpoint" content="{{ route('analytics.audience.capture') }}">
    <meta name="analytics-session-heartbeat-endpoint" content="{{ route('analytics.session.heartbeat') }}">
    @if(! empty($analyticsToolSlug))
        <meta name="analytics-tool-endpoint" content="{{ route('analytics.tools.track') }}">
        <meta name="analytics-presence-endpoint" content="{{ route('analytics.tools.presence') }}">
        <meta name="analytics-tool-slug" content="{{ $analyticsToolSlug }}">
    @endif

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
            @if (request()->routeIs('admin.*'))
                <x-admin.navigation />
            @endif
            @if (! request()->routeIs('admin.*') && isset($activeAcquisitionContext) && $activeAcquisitionContext !== null)
                <x-acquisition.context-bar :context="$activeAcquisitionContext" :mode="$activeAcquisitionContextMode" />
            @endif
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
@unless (request()->routeIs('admin.*'))
    <x-feedback.page-rating />
@endunless

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')


</body>
</html>
