@extends('layouts.app')

@section('title', 'Detalhes do cálculo de rescisão — Prazzu Tools')

@section('content')
@php($result = $run->result_payload ?? [])
<div class="prazzu-page tool-page">
    <nav aria-label="Breadcrumb" class="mb-3"><ol class="breadcrumb prazzu-breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li><li class="breadcrumb-item"><a href="{{ route('tools.calculadora-de-rescisao.history.index') }}">Histórico</a></li><li class="breadcrumb-item active" aria-current="page">Detalhes</li></ol></nav>

    <header class="prazzu-tool-intro">
        <span class="prazzu-icon-tile prazzu-icon-tile--pink"><i class="bi bi-file-earmark-text"></i></span>
        <div class="flex-grow-1"><span class="badge text-bg-success mb-2">Cálculo salvo</span><h1>Detalhes da rescisão</h1><p>{{ $result['termination_type_label'] ?? 'Rescisão trabalhista' }} — realizado em {{ $run->finished_at?->format('d/m/Y H:i') }}.</p></div>
        <div class="text-lg-end"><small class="text-body-secondary d-block">Valor líquido estimado</small><strong class="fs-3 text-success">{{ $result['net_total'] ?? '—' }}</strong></div>
    </header>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <form method="post" action="{{ route('tools.calculadora-de-rescisao.history.repeat', $run) }}">@csrf<button class="btn btn-primary" type="submit"><i class="bi bi-arrow-repeat me-1"></i>Repetir cálculo</button></form>
        <a class="btn btn-outline-primary" target="_blank" href="{{ route('tools.calculadora-de-rescisao.history.pdf', $run) }}"><i class="bi bi-file-earmark-pdf me-1"></i>Exportar PDF</a>
        <a class="btn btn-outline-secondary" href="{{ route('tools.calculadora-de-rescisao.history.index') }}"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
        <form method="post" action="{{ route('tools.calculadora-de-rescisao.history.destroy', $run) }}" onsubmit="return confirm('Excluir este cálculo do histórico?')">@csrf @method('DELETE')<button class="btn btn-outline-danger" type="submit"><i class="bi bi-trash me-1"></i>Excluir</button></form>
    </div>

    <section class="prazzu-tool-card mb-4">
        <h2 class="prazzu-section-title">Resumo</h2>
        <div class="row g-3">
            @foreach ([
                'Total bruto' => 'gross_total', 'Total de descontos' => 'total_discounts', 'Valor líquido' => 'net_total',
                'FGTS rescisório' => 'fgts_termination_deposit', 'Multa ou indenização' => 'fgts_penalty', 'FGTS disponível estimado' => 'estimated_fgts_available',
            ] as $label => $key)
                <div class="col-12 col-md-6 col-xl-4"><div class="border rounded-3 p-3 h-100"><small class="text-body-secondary d-block">{{ $label }}</small><strong class="fs-5">{{ $result[$key] ?? '—' }}</strong></div></div>
            @endforeach
        </div>
    </section>

    <section class="prazzu-tool-card">
        <h2 class="prazzu-section-title">Verbas e descontos</h2>
        <div class="table-responsive"><table class="table table-striped align-middle mb-0"><thead><tr><th>Item</th><th class="text-end">Valor</th></tr></thead><tbody>
            @foreach ([
                'Saldo de salário' => 'salary_balance', 'Férias vencidas' => 'overdue_vacation', '1/3 das férias vencidas' => 'overdue_vacation_third',
                'Férias proporcionais' => 'proportional_vacation', '1/3 das férias proporcionais' => 'proportional_vacation_third', '13º proporcional' => 'proportional_thirteenth_salary',
                'Aviso-prévio' => 'notice_pay', 'Indenização do art. 479' => 'article_479_indemnity', 'Indenizações adicionais' => 'extraordinary_indemnities',
                'INSS sobre salário' => 'inss_salary', 'INSS sobre 13º' => 'inss_thirteenth', 'IRRF sobre salário' => 'irrf_salary', 'IRRF sobre 13º' => 'irrf_thirteenth',
                'Desconto de aviso' => 'notice_discount', 'Desconto do art. 480' => 'article_480_discount', 'Outros descontos' => 'other_discounts',
            ] as $label => $key)
                <tr><td>{{ $label }}</td><td class="text-end fw-semibold">{{ $result[$key] ?? '—' }}</td></tr>
            @endforeach
        </tbody></table></div>
    </section>

    <div class="alert alert-secondary mt-4 mb-0">Versão da ferramenta {{ $run->tool_version }}, regra {{ $run->rule_version }} e data de referência {{ $run->reference_date->format('d/m/Y') }}.</div>
</div>
@endsection
