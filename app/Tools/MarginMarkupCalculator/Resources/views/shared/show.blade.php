@extends('layouts.app')

@section('title', 'Formação de preço compartilhada — Prazzu Tools')
@section('meta_description', 'Visualize um cálculo compartilhado de margem, markup e formação de preço.')

@section('content')
<div class="prazzu-page tool-page">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Cálculo compartilhado</li>
        </ol>
    </nav>

    <header class="prazzu-tool-intro">
        <span class="prazzu-icon-tile prazzu-icon-tile--purple"><i class="bi bi-share"></i></span>
        <div class="flex-grow-1">
            <span class="badge text-bg-primary mb-2">Link compartilhado</span>
            <h1>Margem, markup e formação de preço</h1>
            <p class="mb-0">Relatório compartilhado para consulta. Os valores são estimativas gerenciais.</p>
        </div>
    </header>

    @if (! $unlocked)
        <section class="prazzu-tool-card mx-auto" style="max-width: 34rem;">
            <div class="text-center mb-4">
                <i class="bi bi-shield-lock fs-1 text-primary" aria-hidden="true"></i>
                <h2 class="h4 mt-2">Conteúdo protegido</h2>
                <p class="text-body-secondary mb-0">Informe o código de acesso recebido com o link.</p>
            </div>

            <form method="post" action="{{ route('tools.calculadora-margem-markup.shared.unlock', $share->token) }}" class="row g-3">
                @csrf
                <div class="col-12">
                    <label class="form-label" for="access_code">Código de acesso</label>
                    <input class="form-control @error('access_code') is-invalid @enderror" id="access_code" name="access_code" type="password" maxlength="40" autocomplete="one-time-code" required autofocus>
                    @error('access_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 d-grid">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-unlock me-1"></i>Acessar relatório</button>
                </div>
            </form>
        </section>
    @else
        @php($result = $run->result_payload ?? [])
        @php($input = $run->input_payload ?? [])

        <div class="alert alert-light border d-flex flex-column flex-md-row justify-content-between gap-2 align-items-md-center">
            <span><i class="bi bi-calendar-check me-1"></i>Referência: <strong>{{ $run->reference_date?->format('d/m/Y') }}</strong></span>
            <span class="text-body-secondary">Gerado em {{ $run->finished_at?->format('d/m/Y H:i') }}</span>
        </div>

        <section class="prazzu-tool-card mb-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center mb-3">
                <div>
                    <h2 class="prazzu-section-title mb-1">Resumo da formação de preço</h2>
                    <p class="text-body-secondary mb-0">Composição baseada nos dados informados pelo responsável pelo cálculo.</p>
                </div>
                <button class="btn btn-outline-secondary d-print-none" type="button" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimir</button>
            </div>

            <div class="row g-3">
                @foreach ([
                    'sale_price' => ['Preço de venda sugerido', 'bi-tag'],
                    'net_profit' => ['Lucro líquido estimado', 'bi-graph-up-arrow'],
                    'total_cost' => ['Custo total', 'bi-wallet2'],
                    'gross_profit' => ['Lucro bruto', 'bi-cash-stack'],
                    'margin' => ['Margem líquida', 'bi-percent'],
                    'markup' => ['Markup sobre o custo', 'bi-calculator'],
                    'markup_multiplier' => ['Índice de markup', 'bi-x-lg'],
                ] as $key => [$label, $icon])
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="d-flex gap-2 align-items-center text-body-secondary mb-1"><i class="bi {{ $icon }}"></i><small>{{ $label }}</small></div>
                            <strong class="fs-5">{{ $result[$key] ?? '—' }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="prazzu-tool-card mb-4">
            <h2 class="prazzu-section-title">Descontos incidentes sobre a venda</h2>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <tbody>
                        <tr><th scope="row">Impostos</th><td class="text-end">{{ $result['taxes_amount'] ?? '—' }}</td></tr>
                        <tr><th scope="row">Comissão</th><td class="text-end">{{ $result['commission_amount'] ?? '—' }}</td></tr>
                        <tr><th scope="row">Taxas de cartão</th><td class="text-end">{{ $result['card_fees_amount'] ?? '—' }}</td></tr>
                        <tr><th scope="row">Taxas de marketplace</th><td class="text-end">{{ $result['marketplace_fees_amount'] ?? '—' }}</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="prazzu-tool-card mb-4">
            <h2 class="prazzu-section-title">Dados utilizados</h2>
            <div class="row g-3">
                @foreach ([
                    'base_cost' => 'Custo base', 'additional_costs' => 'Outros custos', 'freight_cost' => 'Frete',
                    'packaging_cost' => 'Embalagem', 'fixed_expenses' => 'Despesas rateadas',
                    'desired_margin' => 'Margem desejada (%)', 'taxes_percentage' => 'Impostos (%)',
                    'commission_percentage' => 'Comissão (%)', 'card_fees_percentage' => 'Cartão (%)',
                    'marketplace_fees_percentage' => 'Marketplace (%)',
                ] as $key => $label)
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="d-flex justify-content-between gap-3 border-bottom pb-2 h-100">
                            <span class="text-body-secondary">{{ $label }}</span><strong>{{ $input[$key] ?? '0' }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="alert alert-warning mb-0">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Este relatório não substitui análise contábil, tributária ou financeira profissional. Regra de cálculo {{ $result['rule_version'] ?? $run->rule_version }}.
        </div>
    @endif
</div>
@endsection
