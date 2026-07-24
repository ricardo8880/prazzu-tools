@props(['tool'])

<section class="prazzu-panel p-3 mt-3" aria-labelledby="tool-feedback-sidebar-title">
    <div class="d-flex align-items-start gap-2 mb-2">
        <span class="prazzu-icon-tile flex-shrink-0" aria-hidden="true">
            <i class="bi bi-chat-square-heart"></i>
        </span>
        <div>
            <h2 id="tool-feedback-sidebar-title" class="h6 mb-1">Ajude a melhorar esta ferramenta</h2>
            <p class="small text-body-secondary mb-0">Encontrou um problema ou sentiu falta de algo? Conte para a gente.</p>
        </div>
    </div>

    <button
        class="btn btn-outline-primary btn-sm w-100 mt-2"
        type="button"
        data-bs-toggle="modal"
        data-bs-target="#tool-feedback-modal"
    >
        <i class="bi bi-chat-left-text me-1" aria-hidden="true"></i>
        Enviar feedback
    </button>
</section>

<div
    class="modal fade"
    id="tool-feedback-modal"
    tabindex="-1"
    aria-labelledby="tool-feedback-modal-title"
    aria-hidden="true"
    data-tool-feedback
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title fs-5" id="tool-feedback-modal-title">Ajude a melhorar esta ferramenta</h2>
                    <p class="small text-body-secondary mb-0 mt-1">Seu feedback sobre {{ $tool->name }} será analisado pela equipe.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <form action="{{ route('feedback.tool.store') }}" method="post" data-tool-feedback-form novalidate>
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="tool_slug" value="{{ $tool->slug }}">
                    <input type="hidden" name="path" value="{{ request()->getPathInfo() }}">
                    <input type="hidden" name="url" value="{{ request()->fullUrl() }}">
                    <input type="hidden" name="route_name" value="{{ request()->route()?->getName() }}">
                    <input type="hidden" name="page_title" value="{{ trim($__env->yieldContent('title', $tool->name)) }}">

                    <fieldset>
                        <legend class="form-label fw-semibold mb-2">O que você gostaria de nos contar?</legend>
                        <div class="d-grid gap-2" data-tool-feedback-types>
                            @foreach (\App\Core\Feedback\Enums\ToolFeedbackType::cases() as $type)
                                <label class="border rounded-3 p-2 d-flex gap-2 align-items-start">
                                    <input
                                        class="form-check-input mt-1"
                                        type="radio"
                                        name="type"
                                        value="{{ $type->value }}"
                                        data-tool-feedback-type
                                        required
                                    >
                                    <span>{{ $type->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div class="mt-3" data-tool-feedback-attempted hidden>
                        <label class="form-label fw-semibold" for="tool-feedback-attempted-action">O que você estava tentando fazer?</label>
                        <textarea
                            class="form-control"
                            id="tool-feedback-attempted-action"
                            name="attempted_action"
                            rows="2"
                            maxlength="2000"
                            placeholder="Descreva brevemente a ação que estava realizando."
                        ></textarea>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold" for="tool-feedback-message">Conte mais detalhes</label>
                        <textarea
                            class="form-control"
                            id="tool-feedback-message"
                            name="message"
                            rows="5"
                            maxlength="5000"
                            placeholder="Explique o problema, o que está faltando ou sua sugestão."
                            required
                        ></textarea>
                        <div class="form-text">Não inclua senhas, dados bancários ou outras informações sigilosas.</div>
                    </div>

                    <div class="alert alert-danger py-2 px-3 mt-3 mb-0" data-tool-feedback-error role="alert" hidden></div>
                    <div class="alert alert-success py-2 px-3 mt-3 mb-0" data-tool-feedback-success role="status" hidden>
                        Obrigado! Seu feedback foi enviado para análise.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" data-tool-feedback-submit>Enviar feedback</button>
                </div>
            </form>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
        (() => {
            const root = document.querySelector('[data-tool-feedback]');
            if (!root || root.dataset.initialized === 'true') return;
            root.dataset.initialized = 'true';

            // O modal nasce junto ao CTA da sidebar, mas é movido para o body para
            // não ser recortado pelo container sticky com overflow da coluna lateral.
            document.body.appendChild(root);

            const form = root.querySelector('[data-tool-feedback-form]');
            const attempted = root.querySelector('[data-tool-feedback-attempted]');
            const attemptedInput = attempted?.querySelector('textarea');
            const typeInputs = [...root.querySelectorAll('[data-tool-feedback-type]')];
            const submit = root.querySelector('[data-tool-feedback-submit]');
            const error = root.querySelector('[data-tool-feedback-error]');
            const success = root.querySelector('[data-tool-feedback-success]');

            const updateAttemptedAction = () => {
                const selected = typeInputs.find((input) => input.checked)?.value;
                const isProblem = selected === 'problem';
                attempted.hidden = !isProblem;
                if (!isProblem && attemptedInput) attemptedInput.value = '';
            };

            typeInputs.forEach((input) => input.addEventListener('change', updateAttemptedAction));

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                error.hidden = true;
                error.textContent = '';
                success.hidden = true;

                if (!form.reportValidity()) return;

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
                        throw new Error(validationMessage || payload?.message || 'Não foi possível enviar seu feedback. Tente novamente.');
                    }

                    form.reset();
                    updateAttemptedAction();
                    success.textContent = payload?.message || 'Obrigado! Seu feedback foi enviado para análise.';
                    success.hidden = false;
                } catch (exception) {
                    error.textContent = exception instanceof Error ? exception.message : 'Não foi possível enviar seu feedback.';
                    error.hidden = false;
                } finally {
                    submit.disabled = false;
                    submit.removeAttribute('aria-busy');
                    submit.textContent = 'Enviar feedback';
                }
            });

            root.addEventListener('hidden.bs.modal', () => {
                error.hidden = true;
                error.textContent = '';
                success.hidden = true;
            });
        })();
        </script>
    @endpush
@endonce
