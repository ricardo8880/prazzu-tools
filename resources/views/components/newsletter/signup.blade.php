@props([
    'idPrefix' => 'newsletter',
])

@php
    $titleId = $idPrefix.'-title';
    $emailId = $idPrefix.'-email';
@endphp

<section {{ $attributes->class(['prazzu-newsletter-signup']) }} aria-labelledby="{{ $titleId }}">
    <h2 id="{{ $titleId }}" class="h6 mb-2">Fique por dentro do que realmente muda</h2>
    <p class="small text-body-secondary mb-3">
        Receba novas ferramentas e avisos quando regras, tabelas ou conteúdos importantes forem atualizados.
    </p>

    <form action="{{ route('newsletter.store') }}" method="post" class="d-grid gap-2">
        @csrf
        <label for="{{ $emailId }}" class="visually-hidden">Seu melhor e-mail</label>
        <input
            id="{{ $emailId }}"
            class="form-control prazzu-form-control @error('email') is-invalid @enderror"
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="Seu melhor e-mail"
            required
            autocomplete="email"
            inputmode="email"
        >
        @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <button class="btn btn-primary prazzu-btn-primary" type="submit">Quero receber atualizações</button>
        <small class="text-body-secondary">Foco em lançamentos e atualizações que possam mudar seu uso das ferramentas.</small>
    </form>
</section>
