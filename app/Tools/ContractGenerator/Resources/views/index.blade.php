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

            <div class="d-flex flex-wrap gap-2 align-items-center mb-3" aria-label="Etapas do gerador">
                <span class="badge text-bg-primary">1. Modalidade</span>
                <span class="badge text-bg-secondary">2. Perguntas</span>
                <span class="badge text-bg-secondary">3. Revisão</span>
                <span class="badge text-bg-secondary">4. Exportação</span>
            </div>

            <x-tools.form-panel
                title="1. Escolha o tipo de contrato"
                description="Cada modalidade apresenta somente as perguntas necessárias ao seu contexto."
                class="mb-4"
            >
                <div class="row g-3">
                    @foreach ($contractTypes as $contractType)
                        <div class="col-12 col-md-6">
                            <a
                                href="{{ route('tools.gerador-de-contratos.index', ['tipo' => $contractType->value]) }}"
                                class="card h-100 text-decoration-none {{ $selectedType === $contractType ? 'border-primary' : '' }}"
                                @if($selectedType === $contractType) aria-current="true" @endif
                            >
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between gap-3">
                                        <div>
                                            <h3 class="h5 text-body mb-1">{{ $contractType->label() }}</h3>
                                            <p class="text-body-secondary mb-0">
                                                @if ($contractType->value === 'prestacao-servicos')
                                                    Para formalizar a contratação e execução de serviços.
                                                @else
                                                    Para formalizar a venda e entrega de um bem móvel.
                                                @endif
                                            </p>
                                        </div>
                                        @if ($selectedType === $contractType)
                                            <span class="badge text-bg-primary">Selecionado</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </x-tools.form-panel>

            @if ($selectedType !== null && $contractText === null)
                <form method="POST" action="{{ route('tools.gerador-de-contratos.build') }}" aria-label="Questionário para gerar contrato">
                    @csrf
                    <input type="hidden" name="contract_type" value="{{ $selectedType->value }}">

                    <x-tools.form-panel
                        title="2. Identifique as partes"
                        description="Informe os dados que identificam quem participa do contrato."
                        class="mb-4"
                    >
                        <div class="row g-3">
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
                                        placeholder="Selecione"
                                        required
                                    />
                                </div>
                                <div class="col-12 col-md-8 col-lg-4">
                                    <x-tools.form.input
                                        :name="$party['prefix'].'_document'"
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
                                    <x-tools.form.input name="termination_notice_days" label="Aviso prévio para encerramento" type="number" min="0" max="365" suffix="dias" :value="old('termination_notice_days', 30)" required />
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
            @endif

            @if ($contractText !== null)
                <x-tools.form-panel
                    title="5. Revise e edite o contrato"
                    description="O texto abaixo é totalmente editável. Ajuste qualquer cláusula antes da exportação."
                    class="mb-4"
                >
                    @if ($edited)
                        <div class="alert alert-success" role="status" aria-live="polite">
                            Visualização atualizada com o texto editado. Nenhum dado foi salvo.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('tools.gerador-de-contratos.preview') }}" aria-label="Editor e exportação do contrato">
                        @csrf
                        <input type="hidden" name="contract_type" value="{{ $selectedType?->value }}">
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
                            <button
                                type="submit"
                                class="btn btn-outline-primary"
                                formaction="{{ route('tools.gerador-de-contratos.export.pdf') }}"
                                formtarget="_blank"
                            >
                                <i class="bi bi-file-earmark-pdf me-1" aria-hidden="true"></i>
                                Exportar PDF
                            </button>
                            <button
                                type="submit"
                                class="btn btn-outline-primary"
                                formaction="{{ route('tools.gerador-de-contratos.export.docx') }}"
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
                    description="Esta prévia usa exatamente o conteúdo atual do editor."
                >
                    <pre class="border rounded bg-body-tertiary p-3 p-md-4 mb-0 text-wrap overflow-auto" tabindex="0" aria-label="Prévia textual do contrato">{{ $contractText['content'] }}</pre>
                </x-tools.result-panel>
            @endif
        </x-tools.page>
    </div>
@endsection
