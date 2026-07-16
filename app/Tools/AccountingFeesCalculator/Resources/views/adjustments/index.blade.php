@extends('layouts.app')

@section('title', 'Reajuste de Honorários Contábeis — Prazzu Tools')
@section('meta_description', 'Calcule reajustes de honorários por IPCA, INPC, IGP-M ou percentual manual e mantenha o histórico.')

@section('content')
<div class="prazzu-page tool-page">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.calculadora-de-honorarios-contabeis.index') }}">Honorários contábeis</a></li>
            <li class="breadcrumb-item active" aria-current="page">Reajustes</li>
        </ol>
    </nav>

    <header class="prazzu-tool-intro">
        <span class="prazzu-icon-tile prazzu-icon-tile--purple"><i class="bi bi-arrow-up-right-circle"></i></span>
        <div class="flex-grow-1">
            <span class="badge text-bg-primary mb-2">Lote 7</span>
            <h1>Reajuste de honorários</h1>
            <p>Aplique o índice do período, visualize a diferença e mantenha um histórico auditável.</p>
        </div>
        <a class="btn btn-outline-primary align-self-start" href="{{ route('tools.calculadora-de-honorarios-contabeis.crm.index') }}">
            <i class="bi bi-people me-1"></i>Abrir CRM
        </a>
    </header>

    @include('tools-calculadora-de-honorarios-contabeis::partials.navigation')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    @php($result = session('adjustment_result'))
    @if ($result)
        <section class="row g-3 mb-4" aria-label="Resultado do reajuste">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="small text-body-secondary">Valor atual</div>
                    <div class="h4 mb-0">R$ {{ number_format($result['current_value_cents'] / 100, 2, ',', '.') }}</div>
                </div></div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="small text-body-secondary">Diferença ({{ number_format($result['percentage'], 4, ',', '.') }}%)</div>
                    <div class="h4 mb-0 {{ $result['difference_cents'] >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $result['difference_cents'] >= 0 ? '+' : '-' }} R$ {{ number_format(abs($result['difference_cents']) / 100, 2, ',', '.') }}
                    </div>
                </div></div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-primary shadow-sm h-100"><div class="card-body">
                    <div class="small text-body-secondary">Novo honorário</div>
                    <div class="h3 text-primary mb-0">R$ {{ number_format($result['adjusted_value_cents'] / 100, 2, ',', '.') }}</div>
                </div></div>
            </div>
        </section>
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-5">
            <section class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3"><i class="bi bi-arrow-up-right-circle text-primary me-2"></i>Novo reajuste</h2>
                    <div class="alert alert-info small" role="alert">
                        Consulte a fonte oficial do índice e informe o percentual acumulado aplicável ao contrato. A ferramenta não busca índices automaticamente.
                    </div>
                    <form method="post" action="{{ route('tools.calculadora-de-honorarios-contabeis.adjustments.calculate') }}" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <label class="form-label" for="client_name">Cliente ou empresa *</label>
                            <input class="form-control @error('client_name') is-invalid @enderror" id="client_name" name="client_name" value="{{ old('client_name') }}" required maxlength="150">
                            @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="index_type">Índice *</label>
                            <select class="form-select @error('index_type') is-invalid @enderror" id="index_type" name="index_type" required>
                                @foreach (['ipca' => 'IPCA', 'inpc' => 'INPC', 'igpm' => 'IGP-M', 'manual' => 'Percentual manual'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('index_type', 'ipca') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('index_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="reference_period">Competência *</label>
                            <input class="form-control @error('reference_period') is-invalid @enderror" type="month" id="reference_period" name="reference_period" value="{{ old('reference_period', now()->format('Y-m')) }}" required>
                            @error('reference_period')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="current_value">Honorário atual *</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">R$</span>
                                <input class="form-control @error('current_value') is-invalid @enderror" id="current_value" name="current_value" inputmode="decimal" value="{{ old('current_value') }}" placeholder="1.500,00" required>
                                @error('current_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="percentage">Percentual *</label>
                            <div class="input-group has-validation">
                                <input class="form-control @error('percentage') is-invalid @enderror" type="number" step="0.0001" min="-100" max="1000" id="percentage" name="percentage" value="{{ old('percentage') }}" placeholder="4,6200" required>
                                <span class="input-group-text">%</span>
                                @error('percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="notes">Observações</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" maxlength="1000" placeholder="Fonte do índice, período acumulado ou condição negociada">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 d-grid">
                            <button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-calculator me-1"></i>Calcular</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-7">
            @guest
                <div class="alert alert-info d-flex gap-2 align-items-start" role="note">
                    <i class="bi bi-person-plus fs-5" aria-hidden="true"></i>
                    <div><strong>O reajuste funciona sem login.</strong><br>Crie uma conta gratuita apenas para salvar e consultar seus resultados depois.</div>
                </div>
            @endguest
            @auth
            <section class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h2 class="h5 mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Histórico de reajustes</h2>
                        <span class="badge text-bg-light border">{{ $adjustments->total() }} registro(s)</span>
                    </div>
                    @if ($adjustments->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-bar-chart display-5 text-body-tertiary"></i>
                            <h3 class="h6 mt-3">Nenhum reajuste calculado</h3>
                            <p class="text-body-secondary mb-0">Os cálculos salvos aparecerão aqui.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead><tr><th>Cliente</th><th>Índice</th><th>Atual</th><th>Novo</th><th>Competência</th><th class="text-end">Ação</th></tr></thead>
                                <tbody>
                                    @php($indexLabels = ['ipca' => 'IPCA', 'inpc' => 'INPC', 'igpm' => 'IGP-M', 'manual' => 'Manual'])
                                    @foreach ($adjustments as $adjustment)
                                        <tr>
                                            <td><div class="fw-semibold">{{ $adjustment->client_name }}</div><div class="small text-body-secondary">{{ number_format((float) $adjustment->percentage, 4, ',', '.') }}%</div></td>
                                            <td><span class="badge text-bg-light border">{{ $indexLabels[$adjustment->index_type] }}</span></td>
                                            <td class="text-nowrap">{{ $adjustment->formattedValue('current_value_cents') }}</td>
                                            <td class="fw-semibold text-primary text-nowrap">{{ $adjustment->formattedValue('adjusted_value_cents') }}</td>
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $adjustment->reference_period)->format('m/Y') }}</td>
                                            <td class="text-end">
                                                <form method="post" action="{{ route('tools.calculadora-de-honorarios-contabeis.adjustments.delete', $adjustment) }}" onsubmit="return confirm('Remover este reajuste do histórico?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Excluir reajuste"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $adjustments->links() }}</div>
                    @endif
                </div>
            </section>
            @endauth
        </div>
    </div>
</div>
@endsection
