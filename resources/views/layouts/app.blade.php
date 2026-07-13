<!doctype html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Prazzu Tools'))</title>
    <meta name="description" content="@yield('meta_description', 'Ferramentas contábeis práticas, confiáveis e acessíveis.')">

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="prazzu-app">
    <a class="visually-hidden-focusable prazzu-skip-link" href="#main-content">Pular para o conteúdo</a>

    <div class="prazzu-shell">
        <x-layout.header />

        <div class="prazzu-shell__body">
            <x-layout.left-sidebar />

            <main id="main-content" class="prazzu-main" tabindex="-1">
                @if (session('status'))
                    <div class="alert alert-success prazzu-flash-alert alert-dismissible fade show" role="status">
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                        <span>{{ session('status') }}</span>
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
</body>
</html>
