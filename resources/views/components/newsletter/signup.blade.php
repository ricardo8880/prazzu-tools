@props([
    'idPrefix' => 'newsletter',
])

@php
    $titleId = $idPrefix.'-title';
    $emailId = $idPrefix.'-email';
@endphp

<section {{ $attributes->class(['prazzu-newsletter-signup']) }} aria-labelledby="{{ $titleId }}">
    <h2 id="{{ $titleId }}" class="h6 mb-2">Stay on top of what actually changes</h2>
    <p class="small text-body-secondary mb-3">
        Get new tools and alerts when important rules, tables, or content are updated.
    </p>

    <form action="{{ route('newsletter.store') }}" method="post" class="d-grid gap-2">
        @csrf
        <label for="{{ $emailId }}" class="visually-hidden">Your email</label>
        <input
            id="{{ $emailId }}"
            class="form-control prazzu-form-control @error('email') is-invalid @enderror"
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="Your email"
            required
            autocomplete="email"
            inputmode="email"
        >
        @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <button class="btn btn-primary prazzu-btn-primary" type="submit">Send me updates</button>
        <small class="text-body-secondary">Focused on launches and updates that may affect how you use the tools.</small>
    </form>
</section>
