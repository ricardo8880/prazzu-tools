@php
    $context = $context ?? null;
    $value = static fn (string $field, mixed $default = '') => old($field, $context[$field] ?? $default);
    $selectedFeatured = old('featured_tools', $context['featured_tools'] ?? []);
    $selectedRecommended = old('recommended_tools', $context['recommended_tools'] ?? []);
    $selectedArticles = old('articles', $context['articles'] ?? []);
@endphp

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <h2 class="h6">Revise os campos abaixo:</h2>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent fw-semibold">Identificação</div>
            <div class="card-body row g-3">
                <div class="col-md-7">
                    <label class="form-label" for="name">Nome interno</label>
                    <input class="form-control" id="name" name="name" required maxlength="255" value="{{ $value('name') }}" placeholder="Ex.: Rescisão — Instagram vídeo 01">
                </div>
                <div class="col-md-5">
                    <label class="form-label" for="keyword">Palavra-chave</label>
                    <div class="input-group">
                        <span class="input-group-text">?context=</span>
                        <input class="form-control" id="keyword" name="keyword" required maxlength="255" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" value="{{ $value('keyword') }}" placeholder="rescisao-video-01">
                    </div>
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="campaign_identifier">Identificador da campanha</label>
                    <input class="form-control" id="campaign_identifier" name="campaign_identifier" maxlength="255" value="{{ $value('campaign_identifier') }}" placeholder="Ex.: instagram-rescisao-julho">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="inactive" @selected($value('status', 'inactive') === 'inactive')>Inativo</option>
                        <option value="active" @selected($value('status', 'inactive') === 'active')>Ativo</option>
                    </select>
                </div>
            </div>
        </div>


        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent fw-semibold">Rastreamento da campanha</div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="campaign_source">Origem</label>
                    <input class="form-control" id="campaign_source" name="campaign_source" maxlength="120" value="{{ $value('campaign_source') }}" placeholder="Ex.: instagram">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="campaign_medium">Mídia</label>
                    <input class="form-control" id="campaign_medium" name="campaign_medium" maxlength="120" value="{{ $value('campaign_medium') }}" placeholder="Ex.: social-video">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="content_identifier">Conteúdo</label>
                    <input class="form-control" id="content_identifier" name="content_identifier" maxlength="255" value="{{ $value('content_identifier') }}" placeholder="Ex.: post-rescisao-01">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="video_identifier">Vídeo</label>
                    <input class="form-control" id="video_identifier" name="video_identifier" maxlength="255" value="{{ $value('video_identifier') }}" placeholder="Ex.: reels-rescisao-01">
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="monthly_investment">Investimento mensal da campanha</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input class="form-control" id="monthly_investment" name="monthly_investment" inputmode="decimal" value="{{ $value('monthly_investment') }}" placeholder="1500,00"></div>
                    <div class="form-text">Usado para estimar custo, CPA, ROAS e ROI no período selecionado.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="investment_currency">Moeda</label>
                    <select class="form-select" id="investment_currency" name="investment_currency"><option value="BRL" @selected($value('investment_currency', 'BRL') === 'BRL')>BRL</option></select>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent fw-semibold">Banner da Home</div>
            <div class="card-body row g-3">
                <div class="col-12">
                    <label class="form-label" for="banner_identifier">Identificador do banner</label>
                    <input class="form-control" id="banner_identifier" name="banner_identifier" maxlength="255" value="{{ $value('banner_identifier') }}" placeholder="Ex.: hero-rescisao-julho">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="hero_title_before">Título antes</label>
                    <input class="form-control" id="hero_title_before" name="hero_title_before" maxlength="255" value="{{ $value('hero_title_before') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="hero_title_line">Linha principal</label>
                    <input class="form-control" id="hero_title_line" name="hero_title_line" maxlength="255" value="{{ $value('hero_title_line') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="hero_title_highlight">Texto em destaque</label>
                    <input class="form-control" id="hero_title_highlight" name="hero_title_highlight" maxlength="255" value="{{ $value('hero_title_highlight') }}">
                </div>
                <div class="col-12">
                    <label class="form-label" for="hero_description">Descrição</label>
                    <textarea class="form-control" id="hero_description" name="hero_description" rows="4" maxlength="3000">{{ $value('hero_description') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label" for="hero_search_placeholder">Placeholder da busca</label>
                    <input class="form-control" id="hero_search_placeholder" name="hero_search_placeholder" maxlength="255" value="{{ $value('hero_search_placeholder') }}">
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent fw-semibold">Chamada para ação</div>
            <div class="card-body row g-3">
                <div class="col-12">
                    <label class="form-label" for="cta_identifier">Identificador do CTA</label>
                    <input class="form-control" id="cta_identifier" name="cta_identifier" maxlength="255" value="{{ $value('cta_identifier') }}" placeholder="Ex.: cta-calcular-rescisao">
                </div>
                <div class="col-md-7">
                    <label class="form-label" for="cta_title">Título do CTA</label>
                    <input class="form-control" id="cta_title" name="cta_title" maxlength="255" value="{{ $value('cta_title') }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label" for="cta_label">Texto do botão</label>
                    <input class="form-control" id="cta_label" name="cta_label" maxlength="255" value="{{ $value('cta_label') }}">
                </div>
                <div class="col-12">
                    <label class="form-label" for="cta_description">Descrição do CTA</label>
                    <textarea class="form-control" id="cta_description" name="cta_description" rows="3" maxlength="3000">{{ $value('cta_description') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="cta_tool_slug">Destino em ferramenta</label>
                    <select class="form-select" id="cta_tool_slug" name="cta_tool_slug">
                        <option value="">Nenhuma ferramenta</option>
                        @foreach ($tools as $tool)
                            <option value="{{ $tool['slug'] }}" @selected($value('cta_tool_slug') === $tool['slug'])>{{ $tool['name'] }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Este destino tem prioridade sobre a URL manual.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="cta_url">URL manual</label>
                    <input class="form-control" id="cta_url" name="cta_url" type="url" maxlength="2048" value="{{ $value('cta_url') }}" placeholder="https://...">
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent fw-semibold">Barra de experiência contextual</div>
            <div class="card-body row g-3">
                <div class="col-12">
                    <label class="form-label" for="contextual_message">Mensagem</label>
                    <input class="form-control" id="contextual_message" name="contextual_message" maxlength="255" value="{{ $value('contextual_message') }}" placeholder="Você está explorando soluções para este tema.">
                    <div class="form-text">Quando vazio, será usado “Experiência personalizada para: nome do contexto”.</div>
                </div>
                <div class="col-md-5">
                    <label class="form-label" for="contextual_continue_label">Texto do botão principal</label>
                    <input class="form-control" id="contextual_continue_label" name="contextual_continue_label" maxlength="80" value="{{ $value('contextual_continue_label') }}" placeholder="Ver soluções recomendadas">
                </div>
                <div class="col-md-7">
                    <label class="form-label" for="contextual_continue_tool_slug">Destino em ferramenta</label>
                    <select class="form-select" id="contextual_continue_tool_slug" name="contextual_continue_tool_slug">
                        <option value="">Usar ferramenta principal ou catálogo</option>
                        @foreach ($tools as $tool)
                            <option value="{{ $tool['slug'] }}" @selected($value('contextual_continue_tool_slug') === $tool['slug'])>{{ $tool['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label" for="contextual_continue_url">URL manual</label>
                    <input class="form-control" id="contextual_continue_url" name="contextual_continue_url" type="url" maxlength="2048" value="{{ $value('contextual_continue_url') }}" placeholder="https://...">
                    <div class="form-text">A ferramenta selecionada tem prioridade sobre a URL manual.</div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent fw-semibold">Seção de ferramentas</div>
            <div class="card-body">
                <label class="form-label" for="tools_section_title">Título da seção</label>
                <input class="form-control" id="tools_section_title" name="tools_section_title" maxlength="255" value="{{ $value('tools_section_title') }}" placeholder="Ferramentas mais recentes">
                <div class="form-text">Quando vazio, a Home usa o título padrão “Ferramentas mais recentes”.</div>
            </div>
        </div>

        @include('admin.acquisition._ordered-selector', [
            'title' => 'Ferramentas em destaque',
            'description' => 'A ordem abaixo será usada para montar a seção de ferramentas da Home.',
            'name' => 'featured_tools',
            'items' => $tools,
            'selected' => $selectedFeatured,
            'valueKey' => 'slug',
            'labelKey' => 'name',
        ])

        @include('admin.acquisition._ordered-selector', [
            'title' => 'Ferramentas recomendadas',
            'description' => 'Base preparada para as recomendações da jornada nos próximos lotes.',
            'name' => 'recommended_tools',
            'items' => $tools,
            'selected' => $selectedRecommended,
            'valueKey' => 'slug',
            'labelKey' => 'name',
        ])

        @include('admin.acquisition._ordered-selector', [
            'title' => 'Artigos relacionados',
            'description' => 'Selecione e ordene os artigos que poderão ser destacados na Home.',
            'name' => 'articles',
            'items' => $articles,
            'selected' => $selectedArticles,
            'valueKey' => 'slug',
            'labelKey' => 'title',
        ])
    </div>

    <div class="col-xl-4">
        <div class="card border-0 shadow-sm mb-4 sticky-xl-top" style="top: 1rem;">
            <div class="card-header bg-transparent fw-semibold">Configuração principal</div>
            <div class="card-body">
                <label class="form-label" for="primary_tool_slug">Ferramenta principal</label>
                <select class="form-select mb-3" id="primary_tool_slug" name="primary_tool_slug">
                    <option value="">Nenhuma ferramenta</option>
                    @foreach ($tools as $tool)
                        <option value="{{ $tool['slug'] }}" @selected($value('primary_tool_slug') === $tool['slug'])>{{ $tool['name'] }}</option>
                    @endforeach
                </select>

                <div class="alert alert-secondary small" role="note">
                    Campos vazios usam automaticamente os conteúdos padrão da Home.
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-primary" type="submit">Salvar contexto</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.acquisition.contexts.index') }}">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(() => {
    document.querySelectorAll('[data-ordered-selector]').forEach((root) => {
        const select = root.querySelector('[data-selector-options]');
        const list = root.querySelector('[data-selector-list]');
        const add = root.querySelector('[data-selector-add]');
        const inputName = root.dataset.inputName;

        const refresh = () => {
            const selected = [...list.querySelectorAll('[data-selector-item]')].map((item) => item.dataset.value);
            [...select.options].forEach((option) => option.disabled = selected.includes(option.value));
        };

        const append = (value, label) => {
            if (!value || list.querySelector(`[data-value="${CSS.escape(value)}"]`)) return;
            const row = document.createElement('div');
            row.className = 'list-group-item d-flex align-items-center gap-2';
            row.dataset.selectorItem = '';
            row.dataset.value = value;
            row.innerHTML = `<input type="hidden" name="${inputName}[]"><span class="badge text-bg-light border" data-position></span><span class="flex-grow-1" data-label></span><button class="btn btn-sm btn-outline-secondary" type="button" data-up aria-label="Mover para cima"><i class="bi bi-arrow-up"></i></button><button class="btn btn-sm btn-outline-secondary" type="button" data-down aria-label="Mover para baixo"><i class="bi bi-arrow-down"></i></button><button class="btn btn-sm btn-outline-danger" type="button" data-remove aria-label="Remover"><i class="bi bi-x-lg"></i></button>`;
            row.querySelector('input').value = value;
            row.querySelector('[data-label]').textContent = label;
            list.append(row);
            renumber();
            refresh();
        };

        const renumber = () => {
            [...list.querySelectorAll('[data-selector-item]')].forEach((item, index) => {
                item.querySelector('[data-position]').textContent = index + 1;
            });
        };

        add.addEventListener('click', () => {
            const option = select.selectedOptions[0];
            if (option?.value) append(option.value, option.textContent.trim());
        });

        list.addEventListener('click', (event) => {
            const item = event.target.closest('[data-selector-item]');
            if (!item) return;
            if (event.target.closest('[data-remove]')) item.remove();
            if (event.target.closest('[data-up]') && item.previousElementSibling) list.insertBefore(item, item.previousElementSibling);
            if (event.target.closest('[data-down]') && item.nextElementSibling) list.insertBefore(item.nextElementSibling, item);
            renumber();
            refresh();
        });

        renumber();
        refresh();
    });
})();
</script>
@endpush
