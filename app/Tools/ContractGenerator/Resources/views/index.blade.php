@extends('layouts.app')

@section('title', 'Gerador de Contratos — Prazzu Tools')
@section('meta_description', 'Prepare contratos completos com perguntas guiadas, revise o texto e exporte em PDF ou Word.')

@section('content')
    <div class="container py-5">
        <x-tools.page
            title="Gerador de Contratos"
            description="Responda às perguntas, gere o contrato completo e edite livremente o texto antes da exportação."
            icon="file-earmark-text"
            slug="gerador-de-contratos"
            badge="Beta"
            tone="green"
        >
            <div class="alert alert-warning mb-4" role="note" aria-label="Aviso sobre o modelo contratual">
                <strong>Modelo geral:</strong> revise integralmente o conteúdo antes de utilizar. Situações específicas, relações de consumo, trabalho, imóveis, garantias ou outras regras especiais podem exigir cláusulas e análise próprias.
            </div>


            @if(session('history_message'))
                <div class="alert alert-success mb-4">{{ session('history_message') }}</div>
            @endif

            <div class="d-flex flex-wrap gap-2 align-items-center mb-3" aria-label="Etapas do gerador">
                <span class="badge text-bg-primary">1. Modalidade</span>
                <span class="badge text-bg-secondary">2. Perguntas</span>
                <span class="badge text-bg-secondary">3. Revisão</span>
                <span class="badge text-bg-secondary">4. Exportação</span>
            </div>

            <x-tools.form-panel
                data-testid="contract-type-panel"
                title="1. Escolha o modelo de contrato"
                description="Os modelos essenciais continuam gratuitos. A biblioteca ampliada adiciona variações profissionais no Prazzu Plus."
                class="mb-4"
            >
                <div class="row g-3">
                    @foreach ($contractTemplates as $template)
                        @php($lockedTemplate = $template->isPlus() && ! $featureAccess['contract_library'])
                        <div class="col-12 col-md-6">
                            @if ($lockedTemplate)
                                <div class="card h-100 border-secondary" data-testid="contract-template-{{ $template->value }}-locked">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between gap-3">
                                            <div>
                                                <h3 class="h5 mb-1">{{ $template->label() }}</h3>
                                                <p class="text-body-secondary mb-0">{{ $template->description() }}</p>
                                            </div>
                                            <span class="badge text-bg-purple">Plus</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <a
                                    href="{{ route('tools.gerador-de-contratos.index', ['modelo' => $template->value]) }}"
                                    @if($template->value === 'servicos-padrao') data-testid="contract-type-service" @elseif($template->value === 'venda-padrao') data-testid="contract-type-sale" @endif
                                    class="card h-100 text-decoration-none {{ $selectedTemplate === $template ? 'border-primary' : '' }}"
                                    @if($selectedTemplate === $template) aria-current="true" @endif
                                >
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between gap-3">
                                            <div>
                                                <h3 class="h5 text-body mb-1">{{ $template->label() }}</h3>
                                                <p class="text-body-secondary mb-0">{{ $template->description() }}</p>
                                            </div>
                                            <div class="d-flex flex-column gap-1 align-items-end">
                                                @if($template->isPlus())<span class="badge text-bg-primary">Plus</span>@endif
                                                @if($selectedTemplate === $template)<span class="badge text-bg-success">Selecionado</span>@endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-tools.form-panel>

            @if ($selectedType !== null && $contractText === null)
                <form method="POST" action="{{ route('tools.gerador-de-contratos.build') }}" aria-label="Questionário para gerar contrato" data-analytics-form="draft">
                    @csrf
                    <input type="hidden" name="contract_type" value="{{ $selectedType->value }}">
                    <input type="hidden" name="template_key" value="{{ $selectedTemplate?->value }}">

                    <x-tools.form-panel
                        data-testid="contract-parties-panel"
                        title="2. Identifique as partes"
                        description="Informe os dados que identificam quem participa do contrato."
                        class="mb-4"
                    >
                        <div class="row g-3">
                            @if ($featureAccess['company_autofill'] && $companyProfiles->isNotEmpty())
                                <div class="col-12">
                                    <div class="border rounded p-3 bg-body-tertiary">
                                        <label class="form-label" for="company-autofill">Preenchimento automático da empresa <span class="badge text-bg-primary">Plus</span></label>
                                        <div id="company-autofill" class="d-flex flex-wrap gap-2">
                                            @foreach($companyProfiles as $profile)
                                                <a class="btn btn-sm {{ request('empresa') == $profile->id ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('tools.gerador-de-contratos.index', ['modelo' => $selectedTemplate?->value, 'empresa' => $profile->id]) }}">
                                                    {{ $profile->legal_name ?: $profile->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                        <div class="form-text">Preenche razão social e CNPJ a partir do perfil compartilhado. Endereço e demais dados continuam sob conferência do usuário.</div>
                                    </div>
                                </div>
                            @endif
                            @foreach ([
                                ['prefix' => 'first_party', 'label' => $selectedType->firstPartyLabel()],
                                ['prefix' => 'second_party', 'label' => $selectedType->secondPartyLabel()],
                            ] as $party)
                                <div class="col-12">
                                    <h3 class="h5 mb-0">{{ $party['label'] }}</h3>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <x-tools.form.input
                                        :name="$party['prefix'].'_name'"
                                        :value="$party['prefix'] === 'first_party' ? ($companyAutofill['name'] ?? null) : null"
                                        label="Nome completo ou razão social"
                                        maxlength="180"
                                        required
                                    />
                                </div>
                                <div class="col-12 col-md-4 col-lg-2">
                                    <x-tools.form.select
                                        :name="$party['prefix'].'_document_type'"
                                        label="Documento"
                                        :options="$documentTypes"
                                        :value="$party['prefix'] === 'first_party' ? ($companyAutofill['document_type'] ?? null) : null"
                                        placeholder="Selecione"
                                        required
                                    />
                                </div>
                                <div class="col-12 col-md-8 col-lg-4">
                                    <x-tools.form.input
                                        :name="$party['prefix'].'_document'"
                                        :value="$party['prefix'] === 'first_party' ? ($companyAutofill['document'] ?? null) : null"
                                        label="Número do documento"
                                        maxlength="18"
                                        required
                                    />
                                </div>
                                <div class="col-12 col-lg-6">
                                    <x-tools.form.input
                                        :name="$party['prefix'].'_address'"
                                        label="Endereço completo"
                                        maxlength="240"
                                        required
                                    />
                                </div>
                                <div class="col-8 col-lg-4">
                                    <x-tools.form.input
                                        :name="$party['prefix'].'_city'"
                                        label="Cidade"
                                        maxlength="120"
                                        required
                                    />
                                </div>
                                <div class="col-4 col-lg-2">
                                    <x-tools.form.input
                                        :name="$party['prefix'].'_state'"
                                        label="UF"
                                        maxlength="2"
                                        required
                                    />
                                </div>
                                @if (! $loop->last)
                                    <div class="col-12"><hr class="my-2"></div>
                                @endif
                            @endforeach
                        </div>
                    </x-tools.form-panel>

                    <x-tools.form-panel
                        data-testid="contract-terms-panel"
                        title="3. Defina o objeto e as condições"
                        description="As respostas abaixo serão usadas para redigir as cláusulas do contrato."
                        class="mb-4"
                    >
                        <div class="row g-3">
                            @if ($selectedType->value === 'prestacao-servicos')
                                <div class="col-12">
                                    <label for="service_description" class="form-label">Quais serviços serão prestados?</label>
                                    <textarea
                                        id="service_description"
                                        name="service_description"
                                        rows="5"
                                        maxlength="4000"
                                        class="form-control @error('service_description') is-invalid @enderror"
                                        required
                                    >{{ old('service_description') }}</textarea>
                                    @error('service_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <x-tools.form.input name="start_date" label="Início dos serviços" type="date" required />
                                </div>
                                <div class="col-12 col-md-4">
                                    <x-tools.form.input name="end_date" label="Término previsto" type="date" help="Opcional para contrato por prazo indeterminado." />
                                </div>
                                <div class="col-12 col-md-4">
                                    <x-tools.form.input name="termination_notice_days" label="Aviso prévio para encerramento" type="number" min="0" max="365" suffix="dias" :value="old('termination_notice_days')" required />
                                </div>
                            @else
                                <div class="col-12">
                                    <label for="asset_description" class="form-label">Qual bem será vendido?</label>
                                    <textarea
                                        id="asset_description"
                                        name="asset_description"
                                        rows="5"
                                        maxlength="4000"
                                        class="form-control @error('asset_description') is-invalid @enderror"
                                        required
                                    >{{ old('asset_description') }}</textarea>
                                    <div class="form-text">Inclua características que permitam identificar o bem com clareza.</div>
                                    @error('asset_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <x-tools.form.input name="delivery_date" label="Data da entrega" type="date" required />
                                </div>
                                <div class="col-12 col-md-8">
                                    <x-tools.form.input name="delivery_location" label="Local da entrega" maxlength="240" required />
                                </div>
                            @endif

                            <div class="col-12 col-md-4">
                                <x-tools.form.money name="amount" label="Valor do contrato" placeholder="1.000,00" required />
                            </div>
                            <div class="col-12 col-md-8">
                                <label for="payment_terms" class="form-label">Como será feito o pagamento?</label>
                                <textarea
                                    id="payment_terms"
                                    name="payment_terms"
                                    rows="3"
                                    maxlength="1200"
                                    class="form-control @error('payment_terms') is-invalid @enderror"
                                    required
                                >{{ old('payment_terms') }}</textarea>
                                @error('payment_terms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </x-tools.form-panel>

                    <x-tools.form-panel
                        title="Cláusulas inteligentes"
                        description="Adicione cláusulas opcionais identificáveis ao contrato. Modelos profissionais podem pré-selecionar cláusulas compatíveis."
                        class="mb-4"
                    >
                        @if ($featureAccess['smart_clauses'])
                            <div class="row g-2">
                                @foreach($smartClauseOptions as $clause)
                                    @php($preset = in_array($clause, $selectedTemplate?->presetClauses() ?? [], true))
                                    <div class="col-12 col-md-6">
                                        <div class="form-check border rounded p-3 ps-5 h-100">
                                            <input class="form-check-input" type="checkbox" name="smart_clauses[]" value="{{ $clause->value }}" id="smart-{{ $clause->value }}" @checked($preset || in_array($clause->value, old('smart_clauses', []), true))>
                                            <label class="form-check-label" for="smart-{{ $clause->value }}"><strong>{{ $clause->label() }}</strong></label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-secondary mb-0">Cláusulas inteligentes fazem parte do Prazzu Plus no modo monetizado.</div>
                        @endif
                    </x-tools.form-panel>

                    <x-tools.form-panel
                        data-testid="contract-closing-panel"
                        title="4. Fechamento do contrato"
                        description="Defina foro, local e data de assinatura e, se necessário, condições adicionais."
                        class="mb-4"
                    >
                        <div class="row g-3">
                            <div class="col-8 col-md-5">
                                <x-tools.form.input name="jurisdiction_city" label="Cidade do foro" maxlength="120" required />
                            </div>
                            <div class="col-4 col-md-2">
                                <x-tools.form.input name="jurisdiction_state" label="UF do foro" maxlength="2" required />
                            </div>
                            <div class="col-12 col-md-5">
                                <x-tools.form.input name="signing_city" label="Cidade da assinatura" maxlength="120" required />
                            </div>
                            <div class="col-12 col-md-4">
                                <x-tools.form.input name="signing_date" label="Data da assinatura" type="date" required />
                            </div>
                            <div class="col-12">
                                <label for="additional_terms" class="form-label">Condições adicionais</label>
                                <textarea
                                    id="additional_terms"
                                    name="additional_terms"
                                    rows="4"
                                    maxlength="4000"
                                    class="form-control @error('additional_terms') is-invalid @enderror"
                                >{{ old('additional_terms') }}</textarea>
                                <div class="form-text">Opcional. Use apenas para condições específicas que não foram cobertas pelas perguntas anteriores.</div>
                                @error('additional_terms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-file-earmark-text me-1" aria-hidden="true"></i>
                                Gerar contrato completo
                            </button>
                            <a href="{{ route('tools.gerador-de-contratos.index') }}" class="btn btn-outline-secondary">Trocar modalidade</a>
                        </div>
                    </x-tools.form-panel>
                </form>
            @elseif ($selectedType === null)
                <div class="alert alert-info mb-4" role="status">
                    Escolha uma modalidade acima para iniciar o questionário.
                </div>
            @endif

            @if ($draft !== null)
                <x-tools.result-panel
                    title="Dados conferidos e contrato gerado"
                    data-analytics-result="draft"
                    description="As respostas foram normalizadas e usadas para montar o texto contratual abaixo."
                    class="mb-4"
                >
                    <div class="row g-4">
                        <div class="col-12 col-lg-6">
                            <h3 class="h6 text-uppercase text-body-secondary">{{ $draft['first_party_label'] }}</h3>
                            <p class="mb-1"><strong>{{ $draft['first_party']['name'] }}</strong></p>
                            <p class="mb-1">{{ strtoupper($draft['first_party']['document_type']) }} {{ $draft['first_party']['document'] }}</p>
                            <p class="mb-0">{{ $draft['first_party']['address'] }}, {{ $draft['first_party']['city'] }}/{{ $draft['first_party']['state'] }}</p>
                        </div>
                        <div class="col-12 col-lg-6">
                            <h3 class="h6 text-uppercase text-body-secondary">{{ $draft['second_party_label'] }}</h3>
                            <p class="mb-1"><strong>{{ $draft['second_party']['name'] }}</strong></p>
                            <p class="mb-1">{{ strtoupper($draft['second_party']['document_type']) }} {{ $draft['second_party']['document'] }}</p>
                            <p class="mb-0">{{ $draft['second_party']['address'] }}, {{ $draft['second_party']['city'] }}/{{ $draft['second_party']['state'] }}</p>
                        </div>
                        <div class="col-12"><hr class="my-0"></div>
                        <div class="col-12 col-md-4">
                            <span class="text-body-secondary d-block">Modalidade</span>
                            <strong>{{ $draft['type_label'] }}</strong>
                        </div>
                        <div class="col-12 col-md-4">
                            <span class="text-body-secondary d-block">Valor</span>
                            <strong>{{ $draft['amount_formatted'] }}</strong>
                        </div>
                        <div class="col-12 col-md-4">
                            <span class="text-body-secondary d-block">Foro</span>
                            <strong>{{ $draft['jurisdiction_city'] }}/{{ $draft['jurisdiction_state'] }}</strong>
                        </div>
                    </div>
                </x-tools.result-panel>
                @if($currentRunId)
                    <div class="alert alert-success mb-4">Contrato salvo no histórico Plus. Você poderá favoritar e comparar esta versão com outras.</div>
                @endif
            @endif

            @if ($contractText !== null)
                <x-tools.form-panel
                    data-testid="contract-editor-panel"
                    title="5. Revise e edite o contrato"
                    description="O texto abaixo é totalmente editável. Ajuste qualquer cláusula antes da exportação."
                    class="mb-4"
                >
                    @if ($edited)
                        <div class="alert alert-success" role="status" aria-live="polite">
                            Visualização atualizada com o texto editado. Nenhum dado foi salvo.
                        </div>
                    @endif

                    <form data-testid="contract-editor" method="POST" action="{{ route('tools.gerador-de-contratos.preview') }}" aria-label="Editor e exportação do contrato" data-analytics-form="editor">
                        @csrf
                        <input type="hidden" name="contract_type" value="{{ $selectedType?->value }}">
                        <input type="hidden" name="source_run_id" value="{{ $currentRunId }}">
                        <label for="contract_text" class="form-label">Texto completo do contrato</label>
                        <textarea
                            id="contract_text"
                            name="contract_text"
                            rows="32"
                            maxlength="60000"
                            class="form-control font-monospace @error('contract_text') is-invalid @enderror"
                            required
                        >{{ old('contract_text', $contractText['content']) }}</textarea>
                        <div class="form-text">A edição é temporária e permanece somente nesta resposta. PDF e Word usam exatamente o texto atual do editor.</div>
                        @error('contract_text')<div class="invalid-feedback">{{ $message }}</div>@enderror

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-eye me-1" aria-hidden="true"></i>
                                Atualizar visualização
                            </button>
                            @if($featureAccess['history'] && auth()->check())
                                <button type="submit" class="btn btn-outline-dark" formaction="{{ route('tools.gerador-de-contratos.versions.store') }}">
                                    <i class="bi bi-clock-history me-1" aria-hidden="true"></i>
                                    Salvar nova versão
                                </button>
                            @endif
                            <button
                                type="submit"
                                class="btn btn-outline-primary"
                                data-testid="contract-export-pdf"
                                formaction="{{ route('tools.gerador-de-contratos.export.pdf') }}" data-analytics-action="export" data-analytics-form="editor" data-analytics-format="pdf"
                            >
                                <i class="bi bi-file-earmark-pdf me-1" aria-hidden="true"></i>
                                Exportar PDF
                            </button>
                            <button
                                type="submit"
                                class="btn btn-outline-success"
                                data-testid="contract-export-xlsx"
                                formaction="{{ route('tools.gerador-de-contratos.export.xlsx') }}" data-analytics-action="export" data-analytics-form="editor" data-analytics-format="xlsx"
                            >
                                <i class="bi bi-file-earmark-excel me-1" aria-hidden="true"></i>
                                Baixar Excel
                            </button>
                            <button
                                type="submit"
                                class="btn btn-outline-primary"
                                data-testid="contract-export-docx"
                                formaction="{{ route('tools.gerador-de-contratos.export.docx') }}" data-analytics-action="export" data-analytics-form="editor" data-analytics-format="docx"
                            >
                                <i class="bi bi-file-earmark-word me-1" aria-hidden="true"></i>
                                Baixar Word
                            </button>
                            <a href="{{ route('tools.gerador-de-contratos.index', ['tipo' => $selectedType?->value]) }}" class="btn btn-outline-secondary">Refazer perguntas</a>
                        </div>
                    </form>
                </x-tools.form-panel>

                <x-tools.result-panel
                    title="Visualização do contrato"
                    data-analytics-result="editor"
                    description="Esta prévia usa exatamente o conteúdo atual do editor."
                >
                    <pre data-testid="contract-preview" class="border rounded bg-body-tertiary p-3 p-md-4 mb-0 text-wrap overflow-auto" tabindex="0" aria-label="Prévia textual do contrato">{{ $contractText['content'] }}</pre>
                </x-tools.result-panel>
            @endif

            @if($featureAccess['history'] && auth()->check())
                <x-tools.form-panel title="Histórico de contratos" description="Versões persistidas na sua conta e disponíveis para favoritos e comparação." class="mt-4">
                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                        <span class="text-body-secondary">{{ count($recentHistory) }} versão(ões) recente(s)</span>
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('tools.gerador-de-contratos.history.index') }}">Abrir histórico completo</a>
                    </div>
                    @forelse($recentHistory as $run)
                        <div class="border rounded p-3 mb-2 d-flex justify-content-between gap-3 align-items-center">
                            <div><strong>{{ $run->result['contract_title'] ?? 'Contrato' }}</strong><div class="small text-body-secondary">{{ $run->createdAt->format('d/m/Y H:i') }}</div></div>
                            @if($run->favorite)<span class="badge text-bg-warning">Favorito</span>@endif
                        </div>
                    @empty
                        <p class="text-body-secondary mb-0">Nenhuma versão salva ainda.</p>
                    @endforelse
                </x-tools.form-panel>
            @endif
        </x-tools.page>
    </div>
@endsection

@if ($contractText !== null)
    @push('scripts')
        <script>
            (() => {
                const editor = document.querySelector('[data-testid="contract-editor"]');

                if (!(editor instanceof HTMLElement)) {
                    return;
                }

                requestAnimationFrame(() => {
                    editor.scrollIntoView({ block: 'start', inline: 'nearest', behavior: 'auto' });
                });
            })();
        </script>
    @endpush
@endif
