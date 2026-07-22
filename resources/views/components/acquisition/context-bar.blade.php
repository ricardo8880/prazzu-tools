@props(['context', 'mode' => \App\Core\Acquisition\Application\Session\AcquisitionContextSession::MODE_CONTEXTUAL])

@php
    $message = filled($context->contextualMessage)
        ? $context->contextualMessage
        : 'Experiência personalizada para: '.$context->name;
    $continueLabel = filled($context->contextualContinueLabel)
        ? $context->contextualContinueLabel
        : 'Ver soluções recomendadas';
    $isFreeMode = $mode === \App\Core\Acquisition\Application\Session\AcquisitionContextSession::MODE_FREE;
@endphp

<section class="prazzu-context-bar" aria-label="Experiência personalizada">
    <div class="prazzu-context-bar__content">
        <div class="prazzu-context-bar__message">
            <span class="prazzu-context-bar__eyebrow">Experiência personalizada</span>
            <strong>{{ $message }}</strong>
        </div>

        <div class="prazzu-context-bar__actions" aria-label="Modo de exploração">
            <form method="post" action="{{ route('acquisition.context.clear') }}">
                @csrf
                <button
                    class="btn btn-sm {{ $isFreeMode ? 'btn-primary' : 'btn-outline-secondary' }}"
                    type="submit"
                    @if ($isFreeMode) aria-current="true" @endif
                >
                    <i class="bi bi-compass" aria-hidden="true"></i>
                    <span>Explorar livremente</span>
                </button>
            </form>

            <form method="post" action="{{ route('acquisition.context.continue') }}">
                @csrf
                <button
                    class="btn btn-sm {{ $isFreeMode ? 'btn-outline-primary' : 'btn-primary' }}"
                    type="submit"
                    @if (! $isFreeMode) aria-current="true" @endif
                >
                    <span>{{ $continueLabel }}</span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </div>
</section>
