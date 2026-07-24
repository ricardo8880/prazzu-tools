@extends('layouts.app')
@section('title', 'Detalhes de Margem e Markup — Prazzu Tools')
@section('content')
@php
    $result = $run->result ?? [];
@endphp
@php
    $type = $result['calculation_type'] ?? $run->input['calculation_type'] ?? 'single';
@endphp
<div class="prazzu-page tool-page">
    <nav aria-label="Breadcrumb" class="mb-3"><ol class="breadcrumb prazzu-breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li><li class="breadcrumb-item"><a href="{{ route('tools.calculadora-margem-markup.history.index') }}">Histórico</a></li><li class="breadcrumb-item active">Detalhes</li></ol></nav>
    <x-tools.intro icon="file-earmark-text" title="Detalhes do cálculo" :description="'Realizado em '.($run->finishedAt?->format('d/m/Y H:i') ?? '—').' — referência '.$run->referenceDate->format('d/m/Y').'.'" badge="Cálculo salvo" badge-class="badge text-bg-success mb-2" />
    <div class="d-flex flex-wrap gap-2 mb-4">
        <form method="post" action="{{ route('tools.calculadora-margem-markup.history.repeat', $run->id) }}">@csrf<button class="btn btn-primary"><i class="bi bi-arrow-repeat me-1"></i>Repetir</button></form>
        @if($type === 'single')<a class="btn btn-outline-primary" target="_blank" href="{{ route('tools.calculadora-margem-markup.history.pdf', $run->id) }}"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>@endif
        <a class="btn btn-outline-secondary" href="{{ route('tools.calculadora-margem-markup.history.index') }}"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
        <form method="post" action="{{ route('tools.calculadora-margem-markup.history.destroy', $run->id) }}" onsubmit="return confirm('Excluir este registro?')">@csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Excluir</button></form>
    </div>

    @if($type === 'single')
        <section class="prazzu-tool-card"><h2 class="prazzu-section-title">Resumo</h2><div class="row g-3">
            @foreach(['Preço de venda'=>'sale_price','Custo total'=>'total_cost','Lucro líquido'=>'net_profit','Margem'=>'margin','Markup'=>'markup','Índice'=>'markup_multiplier'] as $label=>$key)
                <div class="col-12 col-md-6 col-xl-4"><div class="border rounded-3 p-3 h-100"><small class="text-body-secondary d-block">{{ $label }}</small><strong class="fs-5">{{ $result[$key] ?? '—' }}</strong></div></div>
            @endforeach
        </div></section>

    @else
        <section class="prazzu-tool-card"><h2 class="prazzu-section-title">{{ $type === 'batch' ? 'Produtos calculados' : 'Cenários simulados' }}</h2><div class="table-responsive"><table class="table table-striped align-middle mb-0"><thead><tr>@foreach(array_keys(($result['results'][0] ?? [])) as $key)<th>{{ str($key)->replace('_',' ')->title() }}</th>@endforeach</tr></thead><tbody>@foreach($result['results'] ?? [] as $row)<tr>@foreach($row as $value)<td>{{ $value }}</td>@endforeach</tr>@endforeach</tbody></table></div></section>
    @endif
    <div class="alert alert-secondary mt-4 mb-0">Versão {{ $run->toolVersion }}, regra {{ $run->ruleVersion }}.</div>
</div>
@endsection
