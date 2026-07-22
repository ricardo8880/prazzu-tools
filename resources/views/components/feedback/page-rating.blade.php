<div class="prazzu-page-feedback" data-page-feedback>
    <section
        id="prazzu-page-feedback-panel"
        class="prazzu-page-feedback__panel"
        data-feedback-panel
        aria-labelledby="prazzu-page-feedback-title"
        hidden
    >
        <div class="prazzu-page-feedback__header">
            <div>
                <strong id="prazzu-page-feedback-title">Como foi sua experiência?</strong>
                <p>Avalie esta página. Leva poucos segundos.</p>
            </div>
            <button type="button" class="prazzu-page-feedback__close" data-feedback-close aria-label="Fechar avaliação">
                <span aria-hidden="true">×</span>
            </button>
        </div>

        <form data-feedback-form action="{{ route('feedback.page.store') }}" method="post" novalidate>
            @csrf

            <fieldset class="prazzu-page-feedback__rating-group">
                <legend class="visually-hidden">Nota de 1 a 5 estrelas</legend>
                <input type="hidden" name="rating" value="" data-feedback-rating>

                <div class="prazzu-page-feedback__stars" role="radiogroup" aria-label="Nota de 1 a 5 estrelas">
                    @foreach (range(1, 5) as $rating)
                        <button
                            type="button"
                            class="prazzu-page-feedback__star"
                            data-feedback-star
                            data-rating="{{ $rating }}"
                            role="radio"
                            aria-checked="false"
                            aria-label="{{ $rating }} {{ $rating === 1 ? 'estrela' : 'estrelas' }}"
                            title="{{ $rating }} {{ $rating === 1 ? 'estrela' : 'estrelas' }}"
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path d="M12 2.75l2.86 5.8 6.4.93-4.63 4.51 1.09 6.37L12 17.35l-5.72 3.01 1.09-6.37-4.63-4.51 6.4-.93L12 2.75z" />
                            </svg>
                        </button>
                    @endforeach
                </div>
            </fieldset>

            <div class="prazzu-page-feedback__selected" data-feedback-selected aria-live="polite">Selecione uma nota</div>

            <label class="prazzu-page-feedback__label" for="page-feedback-comment">
                Quer contar mais? <span>(opcional)</span>
            </label>
            <textarea
                class="prazzu-page-feedback__textarea"
                id="page-feedback-comment"
                name="comment"
                rows="3"
                maxlength="2000"
                placeholder="O que funcionou bem ou poderia melhorar?"
            ></textarea>

            <input type="hidden" name="path" value="{{ request()->getPathInfo() }}">
            <input type="hidden" name="url" value="{{ request()->url() }}">
            <input type="hidden" name="page_title" value="{{ trim($__env->yieldContent('title', config('app.name', 'Prazzu Tools'))) }}">

            <div class="prazzu-page-feedback__actions">
                <span class="prazzu-page-feedback__error" data-feedback-error role="alert"></span>
                <button class="prazzu-page-feedback__submit" type="submit" disabled data-feedback-submit>
                    Enviar avaliação
                </button>
            </div>
        </form>

        <div class="prazzu-page-feedback__success" data-feedback-success hidden role="status">
            <span class="prazzu-page-feedback__success-icon" aria-hidden="true">✓</span>
            <span>Obrigado! Sua avaliação foi enviada.</span>
        </div>
    </section>

    <button
        type="button"
        class="prazzu-page-feedback__trigger"
        data-feedback-trigger
        aria-expanded="false"
        aria-controls="prazzu-page-feedback-panel"
    >
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
            <path d="M12 2.75l2.86 5.8 6.4.93-4.63 4.51 1.09 6.37L12 17.35l-5.72 3.01 1.09-6.37-4.63-4.51 6.4-.93L12 2.75z" />
        </svg>
        <span>Avaliar página</span>
    </button>
</div>

@once
    @push('scripts')
        <script>
        (() => {
            const root = document.querySelector('[data-page-feedback]');
            if (!root || root.dataset.initialized === 'true') return;
            root.dataset.initialized = 'true';

            const trigger = root.querySelector('[data-feedback-trigger]');
            const panel = root.querySelector('[data-feedback-panel]');
            const closeButton = root.querySelector('[data-feedback-close]');
            const form = root.querySelector('[data-feedback-form]');
            const submit = root.querySelector('[data-feedback-submit]');
            const selected = root.querySelector('[data-feedback-selected]');
            const error = root.querySelector('[data-feedback-error]');
            const success = root.querySelector('[data-feedback-success]');
            const ratingInput = root.querySelector('[data-feedback-rating]');
            const stars = [...root.querySelectorAll('[data-feedback-star]')];
            const storageKey = `prazzu-page-feedback:${location.pathname}`;
            let currentRating = 0;

            const paintStars = (rating) => {
                stars.forEach((star) => {
                    const active = Number(star.dataset.rating) <= rating;
                    star.classList.toggle('is-active', active);
                });
            };

            const selectRating = (rating) => {
                currentRating = rating;
                ratingInput.value = String(rating);
                selected.textContent = `${rating} ${rating === 1 ? 'estrela' : 'estrelas'}`;
                submit.disabled = false;
                stars.forEach((star) => {
                    star.setAttribute('aria-checked', Number(star.dataset.rating) === rating ? 'true' : 'false');
                });
                paintStars(rating);
            };

            const setOpen = (open) => {
                panel.hidden = !open;
                trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
                root.classList.toggle('is-open', open);

                if (open) {
                    window.requestAnimationFrame(() => (currentRating ? stars[currentRating - 1] : stars[0])?.focus());
                }
            };

            trigger.addEventListener('click', () => setOpen(panel.hidden));
            closeButton.addEventListener('click', () => setOpen(false));

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !panel.hidden) {
                    setOpen(false);
                    trigger.focus();
                }
            });

            document.addEventListener('pointerdown', (event) => {
                if (!panel.hidden && !root.contains(event.target)) setOpen(false);
            });

            stars.forEach((star, index) => {
                star.addEventListener('click', () => selectRating(Number(star.dataset.rating)));
                star.addEventListener('mouseenter', () => paintStars(Number(star.dataset.rating)));
                star.addEventListener('focus', () => paintStars(Number(star.dataset.rating)));
                star.addEventListener('keydown', (event) => {
                    if (!['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
                    event.preventDefault();
                    const direction = event.key === 'ArrowRight' ? 1 : -1;
                    const nextIndex = Math.min(4, Math.max(0, index + direction));
                    stars[nextIndex].focus();
                    selectRating(nextIndex + 1);
                });
            });

            root.querySelector('.prazzu-page-feedback__stars').addEventListener('mouseleave', () => paintStars(currentRating));

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                if (!currentRating) return;

                error.textContent = '';
                submit.disabled = true;
                submit.setAttribute('aria-busy', 'true');
                submit.textContent = 'Enviando...';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
                        },
                        body: new FormData(form),
                    });

                    const payload = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        const validationMessage = payload?.errors
                            ? Object.values(payload.errors).flat()[0]
                            : null;
                        throw new Error(validationMessage || payload?.message || 'Não foi possível enviar sua avaliação. Tente novamente.');
                    }

                    form.hidden = true;
                    success.hidden = false;
                    try { localStorage.setItem(storageKey, new Date().toISOString()); } catch (_) {}
                    trigger.querySelector('span').textContent = 'Página avaliada';
                    window.setTimeout(() => setOpen(false), 2200);
                } catch (exception) {
                    error.textContent = exception instanceof Error ? exception.message : 'Não foi possível enviar sua avaliação.';
                    submit.disabled = false;
                } finally {
                    submit.removeAttribute('aria-busy');
                    submit.textContent = 'Enviar avaliação';
                }
            });

            try {
                if (localStorage.getItem(storageKey)) {
                    form.hidden = true;
                    success.hidden = false;
                    trigger.querySelector('span').textContent = 'Página avaliada';
                }
            } catch (_) {}
        })();
        </script>
    @endpush
@endonce
