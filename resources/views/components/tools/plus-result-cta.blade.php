@props([
    'slug',
    'historyUrl' => null,
])

@php
    $headingId = $slug.'-result-continuity-cta-title';
    $isAuthenticated = auth()->check();
    $hasHistory = $isAuthenticated && is_string($historyUrl) && $historyUrl !== '';
@endphp

<aside
    class="prazzu-result-continuity-cta mt-4"
    data-result-continuity-cta
    data-result-continuity-tool="{{ $slug }}"
    aria-labelledby="{{ $headingId }}"
    hidden
>
    <div class="prazzu-result-continuity-cta__icon" aria-hidden="true">
        <i class="bi {{ $hasHistory ? 'bi-clock-history' : ($isAuthenticated ? 'bi-compass' : 'bi-bookmark-heart') }}"></i>
    </div>

    <div class="prazzu-result-continuity-cta__content min-w-0">
        @guest
            <span class="prazzu-result-continuity-cta__eyebrow">Continuidade opcional</span>
            <h2 class="h5 mb-1" id="{{ $headingId }}">Quer voltar aos seus próximos cálculos com mais facilidade?</h2>
            <p class="text-body-secondary mb-0">
                Crie uma conta grátis para usar histórico, favoritos e recuperação nas ferramentas que oferecem persistência.
                <strong>Os cálculos continuam disponíveis sem cadastro.</strong>
            </p>
        @elseif ($hasHistory)
            <span class="prazzu-result-continuity-cta__eyebrow">Retome quando precisar</span>
            <h2 class="h5 mb-1" id="{{ $headingId }}">Seu histórico ajuda você a continuar este trabalho depois.</h2>
            <p class="text-body-secondary mb-0">
                Revise resultados anteriores e repita cálculos sem depender de anotações soltas ou refazer todo o caminho.
            </p>
        @else
            <span class="prazzu-result-continuity-cta__eyebrow">Continue sua análise</span>
            <h2 class="h5 mb-1" id="{{ $headingId }}">Resultado pronto. Há outras ferramentas para o próximo passo.</h2>
            <p class="text-body-secondary mb-0">
                Explore o catálogo quando surgir outra necessidade. O Prazzu continua focado em resolver tarefas pontuais sem transformar seu trabalho em um sistema de gestão.
            </p>
        @endguest
    </div>

    <div class="prazzu-result-continuity-cta__actions flex-shrink-0">
        @guest
            <a class="btn prazzu-result-continuity-cta__action" href="{{ route('register', ['source' => 'result_continuity', 'tool' => $slug]) }}">
                Criar conta grátis
                <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
            </a>
            <a class="prazzu-result-continuity-cta__secondary" href="{{ route('login', ['source' => 'result_continuity', 'tool' => $slug]) }}">Já tenho conta</a>
        @elseif ($hasHistory)
            <a class="btn prazzu-result-continuity-cta__action" href="{{ $historyUrl }}">
                Abrir histórico
                <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
            </a>
        @else
            <a class="btn prazzu-result-continuity-cta__action" href="{{ route('tools.index') }}">
                Ver ferramentas
                <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
            </a>
        @endguest
    </div>
</aside>
