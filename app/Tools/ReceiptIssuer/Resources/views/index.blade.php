@extends('layouts.app')

@section('title', 'Emissor de Recibos — Prazzu Tools')
@section('meta_description', 'Preencha os dados do pagamento e gere um recibo completo, com valor por extenso e identificação verificável.')

@section('content')
    @php($result = $result ?? session('receipt_result'))
    @php($receipt = is_array($result) ? ($result['details']['receipt'] ?? null) : null)

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <x-tools.intro icon="receipt" tone="green" title="Emissor de Recibos" description="Preencha, revise e gere um recibo completo sem depender de modelos improvisados." badge="Em desenvolvimento" />

                <x-tool-feature-tiers slug="emissor-de-recibos" />

                @if(session('profile_message'))
                    <div class="alert alert-success">{{ session('profile_message') }}</div>
                @endif

                @if(session('history_message'))
                    <div class="alert alert-success">{{ session('history_message') }}</div>
                @endif

                @if($historySaved ?? session('history_saved', false))
                    <div class="alert alert-success">Recibo salvo no seu histórico.</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <strong>Revise os dados informados.</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3">Dados do recibo</h2>
                        <form method="POST" action="{{ route('tools.emissor-de-recibos.issue') }}" class="row g-3">
                            @csrf


                            @auth
                                <div class="col-12">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 rounded border p-3">
                                        <div>
                                            <strong>Perfis salvos</strong>
                                            <div class="small text-body-secondary">Preencha pagador ou recebedor com um clique.</div>
                                        </div>
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('tools.emissor-de-recibos.profiles.index') }}">Gerenciar perfis</a>
                                    </div>
                                </div>
                                @if($partyProfiles->isNotEmpty())
                                    <div class="col-12 d-flex flex-wrap gap-2">
                                        @foreach($partyProfiles as $profile)
                                            <form method="POST" action="{{ route('tools.emissor-de-recibos.profiles.use', $profile->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-secondary" type="submit">
                                                    {{ $profile->party_type === 'payer' ? 'Pagador' : 'Recebedor' }}: {{ $profile->label }}
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                @endif
                            @endauth

                            <div class="col-md-4">
                                <label for="number" class="form-label">Número do recibo</label>
                                <input id="number" name="number" value="{{ old('number', now()->format('Ymd').'-001') }}" class="form-control" maxlength="40" required>
                            </div>
                            <div class="col-md-4">
                                <label for="amount" class="form-label">Valor recebido</label>
                                <input id="amount" name="amount" value="{{ old('amount') }}" class="form-control" placeholder="1.000,00" inputmode="decimal" required>
                            </div>
                            <div class="col-md-4">
                                <label for="issued_at" class="form-label">Data de emissão</label>
                                <input id="issued_at" type="date" name="issued_at" value="{{ old('issued_at', now()->format('Y-m-d')) }}" class="form-control" required>
                            </div>

                            <div class="col-12"><hr><h3 class="h5">Pagador</h3></div>
                            <div class="col-md-6">
                                <label for="payer_name" class="form-label">Nome ou razão social</label>
                                <input id="payer_name" name="payer_name" value="{{ old('payer_name') }}" class="form-control" maxlength="160" required>
                            </div>
                            <div class="col-md-2">
                                <label for="payer_document_type" class="form-label">Documento</label>
                                <select id="payer_document_type" name="payer_document_type" class="form-select">
                                    <option value="">Sem documento</option>
                                    <option value="cpf" @selected(old('payer_document_type') === 'cpf')>CPF</option>
                                    <option value="cnpj" @selected(old('payer_document_type') === 'cnpj')>CNPJ</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="payer_document" class="form-label">Número do documento</label>
                                <input id="payer_document" name="payer_document" value="{{ old('payer_document') }}" class="form-control" maxlength="18" inputmode="numeric">
                            </div>

                            <div class="col-12"><hr><h3 class="h5">Recebedor</h3></div>
                            <div class="col-md-6">
                                <label for="payee_name" class="form-label">Nome ou razão social</label>
                                <input id="payee_name" name="payee_name" value="{{ old('payee_name') }}" class="form-control" maxlength="160" required>
                            </div>
                            <div class="col-md-2">
                                <label for="payee_document_type" class="form-label">Documento</label>
                                <select id="payee_document_type" name="payee_document_type" class="form-select">
                                    <option value="">Sem documento</option>
                                    <option value="cpf" @selected(old('payee_document_type') === 'cpf')>CPF</option>
                                    <option value="cnpj" @selected(old('payee_document_type') === 'cnpj')>CNPJ</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="payee_document" class="form-label">Número do documento</label>
                                <input id="payee_document" name="payee_document" value="{{ old('payee_document') }}" class="form-control" maxlength="18" inputmode="numeric">
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Referente a</label>
                                <textarea id="description" name="description" class="form-control" rows="3" maxlength="1000" required>{{ old('description') }}</textarea>
                                <div class="form-text">Descreva de forma objetiva o pagamento recebido.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="form-label">Cidade <span class="text-muted">(opcional)</span></label>
                                <input id="city" name="city" value="{{ old('city') }}" class="form-control" maxlength="120">
                            </div>
                            <div class="col-12 d-flex gap-2">
                                <button class="btn btn-primary" type="submit">Gerar e revisar recibo</button>
                                <a class="btn btn-outline-secondary" href="{{ route('tools.emissor-de-recibos.index') }}">Limpar</a>
                            </div>
                        </form>
                    </div>
                </div>

                @if (is_array($receipt))
                    <section aria-labelledby="recibo-gerado" class="mb-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <h2 id="recibo-gerado" class="h3 mb-0">Revisão do recibo</h2>
                            <div class="d-flex flex-wrap gap-2">
                                <x-tools.print-button label="Imprimir página" />
                                <form method="POST" action="{{ route('tools.emissor-de-recibos.export.pdf') }}" target="_blank">
                                    @csrf
                                    @foreach (['number', 'payer_name', 'payer_document_type', 'payer_document', 'payee_name', 'payee_document_type', 'payee_document', 'amount', 'description', 'issued_at', 'city'] as $field)
                                        <input type="hidden" name="{{ $field }}" value="{{ old($field) }}">
                                    @endforeach
                                    <button class="btn btn-success" type="submit">
                                        <i class="bi bi-file-earmark-pdf me-1" aria-hidden="true"></i>Exportar PDF
                                    </button>
                                </form>
                            </div>
                        </div>

                        <article class="card shadow-sm" id="receipt-preview">
                            <div class="card-body p-4 p-md-5">
                                <div class="d-flex justify-content-between gap-3 border-bottom pb-3 mb-4">
                                    <div>
                                        <div class="text-uppercase text-muted small">Recibo</div>
                                        <h3 class="h4 mb-0">Nº {{ $receipt['number'] }}</h3>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-muted small">Valor</div>
                                        <div class="h3 mb-0">{{ $receipt['amount'] }}</div>
                                    </div>
                                </div>

                                <p class="fs-5 lh-lg">
                                    Recebi de <strong>{{ $receipt['payer']['name'] }}</strong>
                                    @if($receipt['payer']['document'])<span>({{ strtoupper($receipt['payer']['document_type']) }} {{ $receipt['payer']['document'] }})</span>@endif,
                                    a importância de <strong>{{ $receipt['amount_in_words'] }}</strong>, referente a
                                    <strong>{{ $receipt['description'] }}</strong>.
                                </p>

                                <p class="mb-5">Para maior clareza, firmo o presente recibo.</p>

                                <div class="row g-4 align-items-end">
                                    <div class="col-md-6">
                                        {{ $receipt['city'] ? $receipt['city'].', ' : '' }}{{ \Carbon\CarbonImmutable::parse($receipt['issued_at'])->translatedFormat('d \\d\\e F \\d\\e Y') }}.
                                    </div>
                                    <div class="col-md-6 text-center">
                                        <div class="border-top pt-2 mt-5">
                                            <strong>{{ $receipt['payee']['name'] }}</strong><br>
                                            @if($receipt['payee']['document'])<span>{{ strtoupper($receipt['payee']['document_type']) }} {{ $receipt['payee']['document'] }}</span>@endif
                                        </div>
                                    </div>
                                </div>

                                <div class="text-muted small mt-5">Identificador: {{ $receipt['identifier'] }}</div>
                            </div>
                        </article>
                        <div class="alert alert-info mt-3 mb-0">Revise nomes, documentos, valor, descrição e data antes de imprimir ou salvar o PDF.</div>
                    </section>
                @endif

                @auth
                    <section class="mt-5" aria-labelledby="historico-recente">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div>
                                <h2 id="historico-recente" class="h4 mb-1">Recibos recentes</h2>
                                <p class="text-body-secondary mb-0">Acesse rapidamente os últimos recibos salvos na sua conta.</p>
                            </div>
                            <a class="btn btn-outline-primary" href="{{ route('tools.emissor-de-recibos.batch.index') }}"><i class="bi bi-files me-1"></i> Gerar em lote</a>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('tools.emissor-de-recibos.history.index') }}">Ver histórico completo</a>
                        </div>
                        <div class="row g-3">
                            @forelse($recentHistory as $run)
                                @php($historyReceipt = $run->result['details']['receipt'] ?? [])
                                <div class="col-md-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <strong>Recibo nº {{ $historyReceipt['number'] ?? ($run->input['number'] ?? '—') }}</strong>
                                            <div class="small text-body-secondary mt-1">{{ $historyReceipt['amount'] ?? 'Valor não disponível' }}</div>
                                            <div class="small mt-2">{{ $historyReceipt['payer']['name'] ?? ($run->input['payer_name'] ?? 'Pagador') }}</div>
                                            <form method="POST" action="{{ route('tools.emissor-de-recibos.history.repeat', $run->id) }}" class="mt-3">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-primary" type="submit">Reutilizar dados</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12"><div class="alert alert-light border mb-0">Seus próximos recibos emitidos aparecerão aqui.</div></div>
                            @endforelse
                        </div>
                    </section>
                @endauth

                <section class="mt-5">
                    <h2 class="h4">Como funciona</h2>
                    <p>O Prazzu valida os dados principais, transforma o valor em reais por extenso e monta o texto completo do recibo. O uso continua disponível sem login; usuários autenticados podem salvar e reutilizar recibos pelo histórico.</p>
                </section>
            </div>
        </div>
    </div>
@endsection
