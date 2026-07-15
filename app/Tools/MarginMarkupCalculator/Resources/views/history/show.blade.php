@extends('layouts.app')
@section('title', 'Detalhes de Margem e Markup — Prazzu Tools')
@section('content')
@php($result = $run->result_payload ?? [])
@php($type = $result['calculation_type'] ?? $run->input_payload['calculation_type'] ?? 'single')
<div class="prazzu-page tool-page">
    <nav aria-label="Breadcrumb" class="mb-3"><ol class="breadcrumb prazzu-breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li><li class="breadcrumb-item"><a href="{{ route('tools.calculadora-margem-markup.history.index') }}">Histórico</a></li><li class="breadcrumb-item active">Detalhes</li></ol></nav>
    <header class="prazzu-tool-intro"><span class="prazzu-icon-tile prazzu-icon-tile--purple"><i class="bi bi-file-earmark-text"></i></span><div class="flex-grow-1"><span class="badge text-bg-success mb-2">Cálculo salvo</span><h1>Detalhes do cálculo</h1><p>Realizado em {{ $run->finished_at?->format('d/m/Y H:i') }} — referência {{ $run->reference_date->format('d/m/Y') }}.</p></div></header>
    <div class="d-flex flex-wrap gap-2 mb-4">
        <form method="post" action="{{ route('tools.calculadora-margem-markup.history.repeat', $run) }}">@csrf<button class="btn btn-primary"><i class="bi bi-arrow-repeat me-1"></i>Repetir</button></form>
        @if($type === 'single')<a class="btn btn-outline-primary" target="_blank" href="{{ route('tools.calculadora-margem-markup.history.pdf', $run) }}"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>@endif
        <a class="btn btn-outline-secondary" href="{{ route('tools.calculadora-margem-markup.history.index') }}"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
        <form method="post" action="{{ route('tools.calculadora-margem-markup.history.destroy', $run) }}" onsubmit="return confirm('Excluir este registro?')">@csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Excluir</button></form>
    </div>

    @if($type === 'single')
        <section class="prazzu-tool-card"><h2 class="prazzu-section-title">Resumo</h2><div class="row g-3">
            @foreach(['Preço de venda'=>'sale_price','Custo total'=>'total_cost','Lucro líquido'=>'net_profit','Margem'=>'margin','Markup'=>'markup','Índice'=>'markup_multiplier'] as $label=>$key)
                <div class="col-12 col-md-6 col-xl-4"><div class="border rounded-3 p-3 h-100"><small class="text-body-secondary d-block">{{ $label }}</small><strong class="fs-5">{{ $result[$key] ?? '—' }}</strong></div></div>
            @endforeach
        </div></section>

        <section class="prazzu-tool-card mt-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
                <div>
                    <h2 class="prazzu-section-title mb-1">Compartilhamento</h2>
                    <p class="text-body-secondary mb-0">Gere um link público com validade e código de acesso opcional.</p>
                </div>
                @if($activeShare)
                    <span class="badge text-bg-success align-self-start">Link ativo até {{ $activeShare->expires_at?->format('d/m/Y H:i') }}</span>
                @endif
            </div>

            @if(session('share_url'))
                <div class="alert alert-success">
                    <label class="form-label fw-semibold" for="share-url">Link criado</label>
                    <div class="input-group">
                        <input class="form-control" id="share-url" value="{{ session('share_url') }}" readonly>
                        <button class="btn btn-outline-success" type="button" onclick="navigator.clipboard.writeText(document.getElementById('share-url').value)"><i class="bi bi-copy me-1"></i>Copiar</button>
                    </div>
                    <a class="btn btn-success mt-2" target="_blank" rel="noopener" href="https://wa.me/?text={{ urlencode('Confira este cálculo de formação de preço: '.session('share_url')) }}"><i class="bi bi-whatsapp me-1"></i>Enviar pelo WhatsApp</a>
                </div>
            @endif

            @if($activeShare)
                @php($activeShareUrl = route('tools.calculadora-margem-markup.shared.show', $activeShare->token))
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-lg">
                        <label class="form-label" for="active-share-url">Link ativo</label>
                        <div class="input-group">
                            <input class="form-control" id="active-share-url" value="{{ $activeShareUrl }}" readonly>
                            <button class="btn btn-outline-primary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('active-share-url').value)"><i class="bi bi-copy"></i><span class="visually-hidden">Copiar link</span></button>
                        </div>
                    </div>
                    <div class="col-12 col-lg-auto d-flex flex-wrap gap-2">
                        <a class="btn btn-outline-success" target="_blank" rel="noopener" href="https://wa.me/?text={{ urlencode('Confira este cálculo de formação de preço: '.$activeShareUrl) }}"><i class="bi bi-whatsapp me-1"></i>WhatsApp</a>
                        <form method="post" action="{{ route('tools.calculadora-margem-markup.history.share.revoke', $run) }}" onsubmit="return confirm('Revogar este link?')">@csrf @method('DELETE')<button class="btn btn-outline-danger" type="submit"><i class="bi bi-link-45deg me-1"></i>Revogar</button></form>
                    </div>
                </div>
            @else
                <form method="post" action="{{ route('tools.calculadora-margem-markup.history.share', $run) }}" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="validity_days">Validade</label>
                        <select class="form-select" id="validity_days" name="validity_days" required>
                            @foreach([1=>'1 dia',7=>'7 dias',15=>'15 dias',30=>'30 dias',90=>'90 dias'] as $days=>$label)<option value="{{ $days }}" @selected(old('validity_days', 7) == $days)>{{ $label }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-5">
                        <label class="form-label" for="access_code">Código de acesso <span class="text-body-secondary">(opcional)</span></label>
                        <input class="form-control @error('access_code') is-invalid @enderror" id="access_code" name="access_code" minlength="4" maxlength="40" autocomplete="new-password">
                        @error('access_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-3 d-grid"><button class="btn btn-primary" type="submit"><i class="bi bi-share me-1"></i>Criar link</button></div>
                </form>
            @endif
        </section>
    @else
        <section class="prazzu-tool-card"><h2 class="prazzu-section-title">{{ $type === 'batch' ? 'Produtos calculados' : 'Cenários simulados' }}</h2><div class="table-responsive"><table class="table table-striped align-middle mb-0"><thead><tr>@foreach(array_keys(($result['results'][0] ?? [])) as $key)<th>{{ str($key)->replace('_',' ')->title() }}</th>@endforeach</tr></thead><tbody>@foreach($result['results'] ?? [] as $row)<tr>@foreach($row as $value)<td>{{ $value }}</td>@endforeach</tr>@endforeach</tbody></table></div></section>
    @endif
    <div class="alert alert-secondary mt-4 mb-0">Versão {{ $run->tool_version }}, regra {{ $run->rule_version }}.</div>
</div>
@endsection
