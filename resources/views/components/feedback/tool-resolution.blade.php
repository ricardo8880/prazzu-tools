@props(['slug'])

<section
    {{ $attributes->class(['prazzu-panel p-3 mt-4']) }}
    data-tool-resolution-feedback
    data-tool-resolution-slug="{{ $slug }}"
    hidden
    aria-labelledby="tool-resolution-title"
>
    <div data-tool-resolution-question>
        <span class="prazzu-eyebrow">Sua experiência</span>
        <h2 id="tool-resolution-title" class="h5 mt-1 mb-1">Esta ferramenta resolveu o que você precisava?</h2>
        <p class="small text-body-secondary mb-3">Leva poucos segundos e ajuda a priorizar melhorias que realmente fazem diferença.</p>

        <form action="{{ route('feedback.tool.store') }}" method="post" data-tool-resolution-form novalidate>
            @csrf
            <input type="hidden" name="feedback_kind" value="resolution">
            <input type="hidden" name="tool_slug" value="{{ $slug }}">
            <input type="hidden" name="path" value="{{ request()->getPathInfo() }}">
            <input type="hidden" name="url" value="{{ request()->fullUrl() }}">

            <fieldset>
                <legend class="visually-hidden">A ferramenta resolveu o que você precisava?</legend>
                <div class="d-flex flex-wrap gap-2" data-tool-resolution-options>
                    @foreach (\App\Core\Feedback\Enums\ToolResolution::cases() as $resolution)
                        <label class="btn btn-sm btn-outline-secondary">
                            <input class="btn-check" type="radio" name="resolution" value="{{ $resolution->value }}" autocomplete="off" required>
                            {{ $resolution->label() }}
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <div class="mt-3" data-tool-resolution-reasons hidden>
                <label class="form-label fw-semibold mb-2" for="tool-resolution-reason">O que faltou?</label>
                <select class="form-select form-select-sm" id="tool-resolution-reason" name="reason">
                    <option value="">Selecione o principal motivo</option>
                    @foreach (\App\Core\Feedback\Enums\ToolResolutionReason::cases() as $reason)
                        <option value="{{ $reason->value }}">{{ $reason->label() }}</option>
                    @endforeach
                </select>

                <label class="form-label small fw-semibold mt-3" for="tool-resolution-comment">Quer explicar um pouco mais? <span class="fw-normal text-body-secondary">Opcional</span></label>
                <textarea class="form-control form-control-sm" id="tool-resolution-comment" name="comment" rows="2" maxlength="1000" placeholder="Sem dados sigilosos ou pessoais."></textarea>
            </div>

            <div class="alert alert-danger py-2 px-3 mt-3 mb-0" data-tool-resolution-error role="alert" hidden></div>
            <button class="btn btn-primary btn-sm mt-3" type="submit" data-tool-resolution-submit hidden>Enviar resposta</button>
        </form>
    </div>

    <div class="d-flex align-items-start gap-2" data-tool-resolution-success role="status" tabindex="-1" hidden>
        <i class="bi bi-check-circle-fill text-success" aria-hidden="true"></i>
        <div>
            <strong>Resposta registrada.</strong>
            <p class="small text-body-secondary mb-0" data-tool-resolution-success-message>Obrigado por ajudar a melhorar o Prazzu Tools.</p>
        </div>
    </div>
</section>

@once
    @push('scripts')
        <script nonce="{{ $cspNonce ?? '' }}">
        (() => {
            const roots = [...document.querySelectorAll('[data-tool-resolution-feedback]')];

            roots.forEach((root) => {
                if (root.dataset.initialized === 'true') return;
                root.dataset.initialized = 'true';

                const result = root.closest('[data-tool]')?.querySelector('[data-analytics-result], [data-testid="tool-result"]');
                if (!result) return;

                root.hidden = false;

                const form = root.querySelector('[data-tool-resolution-form]');
                const radios = [...form.querySelectorAll('[name="resolution"]')];
                const reasons = root.querySelector('[data-tool-resolution-reasons]');
                const reason = form.querySelector('[name="reason"]');
                const submit = root.querySelector('[data-tool-resolution-submit]');
                const error = root.querySelector('[data-tool-resolution-error]');
                const question = root.querySelector('[data-tool-resolution-question]');
                const success = root.querySelector('[data-tool-resolution-success]');
                const successMessage = root.querySelector('[data-tool-resolution-success-message]');

                const sync = () => {
                    const selected = radios.find((radio) => radio.checked)?.value;
                    const needsReason = selected === 'partially' || selected === 'no';
                    reasons.hidden = !needsReason;
                    reason.required = needsReason;
                    submit.hidden = !selected;

                    if (!needsReason) {
                        reason.value = '';
                        form.querySelector('[name="comment"]').value = '';
                    }
                };

                radios.forEach((radio) => radio.addEventListener('change', sync));

                form.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    error.hidden = true;
                    error.textContent = '';
                    if (!form.reportValidity()) return;

                    submit.disabled = true;
                    submit.setAttribute('aria-busy', 'true');

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
                            const validationMessage = payload?.errors ? Object.values(payload.errors).flat()[0] : null;
                            throw new Error(validationMessage || payload?.message || 'Não foi possível registrar sua resposta.');
                        }

                        successMessage.textContent = payload?.message || 'Obrigado por ajudar a melhorar o Prazzu Tools.';
                        question.hidden = true;
                        success.hidden = false;
                        success.focus();
                    } catch (exception) {
                        error.textContent = exception instanceof Error ? exception.message : 'Não foi possível registrar sua resposta.';
                        error.hidden = false;
                    } finally {
                        submit.disabled = false;
                        submit.removeAttribute('aria-busy');
                    }
                });
            });
        })();
        </script>
    @endpush
@endonce
