@extends('layouts.app')

@section('title', 'Gerador Inteligente de DARF/GPS — Prazzu Tools')
@section('meta_description', 'Calcule de forma orientativa multa e juros de DARF e GPS com memória transparente e conferência obrigatória nas fontes oficiais.')

@section('content')
    @php($result = $result ?? session('guide_result'))
    @php($recentHistory = $recentHistory ?? [])
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <x-tools.intro icon="receipt-cutoff" tone="green" title="Gerador Inteligente de DARF/GPS" description="Identifique um código inicial, confira vencimento e calcule multa e juros com memória transparente." badge="Ativa" />

                <x-tool-feature-tiers slug="gerador-darf-gps" />

                <div class="alert alert-warning">
                    Esta ferramenta auxilia a conferência. Ela não transmite, não emite e não substitui o SicalcWeb, e-CAC ou outro sistema oficial.
                </div>

                @if(session('history_message'))<div class="alert alert-info">{{ session('history_message') }}</div>@endif
                @if(($historySaved ?? session('history_saved', false)))<div class="alert alert-success">Cálculo salvo no seu histórico.</div>@endif

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
                        <form method="POST" action="{{ route('tools.gerador-darf-gps.calculate') }}" class="row g-3">
                            @csrf
                            <div class="col-md-4">
                                <label for="guide_type" class="form-label">Tipo de guia</label>
                                <select id="guide_type" name="guide_type" class="form-select" required>
                                    <option value="darf" @selected(old('guide_type', 'darf') === 'darf')>DARF</option>
                                    <option value="gps" @selected(old('guide_type') === 'gps')>GPS</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label for="revenue_code" class="form-label">Código e finalidade</label>
                                <select id="revenue_code" name="revenue_code" class="form-select" required>
                                    <optgroup label="DARF" data-guide-group="darf">
                                        @foreach ($codes['darf'] as $code)
                                            <option value="{{ $code['code'] }}" data-guide-type="darf" @selected(old('revenue_code') === $code['code'])>{{ $code['code'] }} — {{ $code['description'] }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="GPS" data-guide-group="gps">
                                        @foreach ($codes['gps'] as $code)
                                            <option value="{{ $code['code'] }}" data-guide-type="gps" @selected(old('revenue_code') === $code['code'])>{{ $code['code'] }} — {{ $code['description'] }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                <div class="form-text">O catálogo é orientativo e exige confirmação profissional no sistema oficial.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="principal" class="form-label">Valor principal</label>
                                <input id="principal" name="principal" value="{{ old('principal') }}" class="form-control" placeholder="1.000,00" inputmode="decimal" required>
                            </div>
                            <div class="col-md-4">
                                <label for="due_date" class="form-label">Vencimento oficial</label>
                                <input id="due_date" type="date" name="due_date" value="{{ old('due_date') }}" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="payment_date" class="form-label">Data prevista de pagamento</label>
                                <input id="payment_date" type="date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="selic_accumulated_percent" class="form-label">Selic acumulada do período (%)</label>
                                <input id="selic_accumulated_percent" name="selic_accumulated_percent" value="{{ old('selic_accumulated_percent', '0') }}" class="form-control" placeholder="1,25" inputmode="decimal">
                                <div class="form-text">Informe o percentual conferido na fonte oficial. Para pagamento no prazo, use zero.</div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="confirm_official_check" name="confirm_official_check" @checked(old('confirm_official_check')) required>
                                    <label class="form-check-label" for="confirm_official_check">Vou confirmar código, período, vencimento e acréscimos no sistema oficial antes do pagamento.</label>
                                </div>
                            </div>
                            <div class="col-12 d-flex gap-2">
                                <button class="btn btn-primary" type="submit">Calcular acréscimos</button>
                                <a class="btn btn-outline-secondary" href="{{ route('tools.gerador-darf-gps.index') }}">Limpar</a>
                            </div>
                        </form>
                    </div>
                </div>

                @if (is_array($result))
                    <section aria-labelledby="resultado-guia" class="mb-4">
                        <h2 id="resultado-guia" class="h3">Resultado orientativo</h2>
                        <div class="row g-3 mb-3">
                            <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="text-muted small">Principal</div><div class="h4 mb-0">{{ $result['amounts']['principal'] }}</div></div></div></div>
                            <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="text-muted small">Multa</div><div class="h4 mb-0">{{ $result['amounts']['penalty'] }}</div><small>{{ $result['calculation']['penalty_percent'] }}%</small></div></div></div>
                            <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="text-muted small">Juros</div><div class="h4 mb-0">{{ $result['amounts']['interest'] }}</div><small>{{ $result['calculation']['interest_percent'] }}%</small></div></div></div>
                            <div class="col-md-3"><div class="card h-100 border-primary"><div class="card-body"><div class="text-muted small">Total estimado</div><div class="h4 mb-0">{{ $result['amounts']['total'] }}</div></div></div></div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-3" aria-label="Exportações do cálculo atual">
                            @foreach(['csv' => 'CSV', 'json' => 'JSON', 'pdf' => 'PDF'] as $format => $label)
                                <form method="post" action="{{ route('tools.gerador-darf-gps.export', $format) }}">
                                    @csrf
                                    @foreach(old() as $key => $value)
                                        @if(!is_array($value) && $key !== '_token')<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif
                                    @endforeach
                                    <button class="btn btn-sm btn-outline-secondary" type="submit">Exportar {{ $label }} @if($format === 'csv')<span class="badge text-bg-primary">Plus</span>@endif</button>
                                </form>
                            @endforeach
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h3 class="h5">Memória do cálculo</h3>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Guia e código</dt><dd class="col-sm-8">{{ $result['guide']['type'] }} {{ $result['guide']['code'] }} — {{ $result['guide']['description'] }}</dd>
                                    <dt class="col-sm-4">Vencimento</dt><dd class="col-sm-8">{{ \Carbon\CarbonImmutable::parse($result['dates']['due_date'])->format('d/m/Y') }}</dd>
                                    <dt class="col-sm-4">Pagamento previsto</dt><dd class="col-sm-8">{{ \Carbon\CarbonImmutable::parse($result['dates']['payment_date'])->format('d/m/Y') }}</dd>
                                    <dt class="col-sm-4">Dias corridos de atraso</dt><dd class="col-sm-8">{{ $result['calculation']['calendar_days_late'] }}</dd>
                                    <dt class="col-sm-4">Referência do catálogo</dt><dd class="col-sm-8">{{ $result['guide']['official_reference'] }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="alert alert-danger">
                            <strong>Conferência obrigatória</strong>
                            <ul class="mb-0 mt-2">@foreach ($result['warnings'] as $warning)<li>{{ $warning }}</li>@endforeach</ul>
                        </div>
                    </section>
                @endif

                @auth
                    <section class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Cálculos recentes</h2><a href="{{ route('tools.gerador-darf-gps.history.index') }}">Ver histórico</a></div>
                            @forelse($recentHistory as $run)
                                <div class="d-flex justify-content-between align-items-center border-top py-2"><span>{{ strtoupper($run->input['guide_type'] ?? 'guia') }} {{ $run->input['revenue_code'] ?? '—' }} · {{ $run->result['amounts']['total'] ?? '—' }}</span><form method="post" action="{{ route('tools.gerador-darf-gps.history.repeat', $run->id) }}">@csrf<button class="btn btn-sm btn-outline-primary">Reutilizar</button></form></div>
                            @empty<p class="text-body-secondary mb-0">Seus próximos cálculos aparecerão aqui.</p>@endforelse
                        </div>
                    </section>
                @else
                    <div class="alert alert-light border">Entre na sua conta para salvar, favoritar e recuperar cálculos. O cálculo Essencial continua disponível sem login.</div>
                @endauth

                <section class="mt-5">
                    <h2 class="h4">Como a estimativa funciona</h2>
                    <p>A multa é calculada em 0,33% por dia corrido de atraso, limitada a 20%. Os juros usam exatamente o percentual Selic acumulado informado no formulário. O vencimento é uma entrada explícita para evitar inferência indevida entre códigos, competências e situações tributárias diferentes.</p>
                    <p class="text-muted mb-0">Não informe CPF, CNPJ, dados bancários ou outras informações pessoais. Visitantes não têm persistência. Usuários autenticados podem salvar, favoritar e reutilizar cálculos.</p>
                </section>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const type = document.getElementById('guide_type');
                const code = document.getElementById('revenue_code');
                const sync = () => {
                    const visible = Array.from(code.options).filter(option => option.dataset.guideType === type.value);
                    Array.from(code.options).forEach(option => option.hidden = option.dataset.guideType !== type.value);
                    if (!visible.some(option => option.selected) && visible[0]) visible[0].selected = true;
                };
                type.addEventListener('change', sync);
                sync();
            });
        </script>
    @endpush
@endsection
