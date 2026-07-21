@extends('layouts.app')
@section('title', 'Histórico de férias')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h3 mb-1">Histórico de férias</h1><p class="text-body-secondary mb-0">Recupere, exporte ou exclua cálculos salvos.</p></div><a class="btn btn-outline-primary" href="{{ route('tools.calculadora-ferias.index') }}">Novo cálculo</a></div>
    @if(session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif
    <div class="vstack gap-3">
    @forelse($runs as $run)
        <article class="card"><div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div><strong>{{ $run->input['vacation_start_date'] ?? 'Data não informada' }}</strong><div class="small text-body-secondary">Salário: R$ {{ number_format((float)($run->input['monthly_salary'] ?? 0), 2, ',', '.') }}</div></div>
            <div class="d-flex flex-wrap gap-2">
                <form method="post" action="{{ route('tools.calculadora-ferias.history.repeat', $run->id) }}">@csrf<button class="btn btn-sm btn-primary">Reutilizar</button></form>
                @foreach(['csv'=>'CSV','json'=>'JSON','pdf'=>'PDF'] as $format=>$label)<a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.calculadora-ferias.history.export', [$run->id,$format]) }}">{{ $label }}</a>@endforeach
                <form method="post" action="{{ route('tools.calculadora-ferias.history.destroy', $run->id) }}" onsubmit="return confirm('Excluir este cálculo?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Excluir</button></form>
            </div>
        </div></article>
    @empty<div class="alert alert-light border">Nenhum cálculo salvo ainda.</div>@endforelse
    </div><div class="mt-4">{{ $runs->links() }}</div>
</div>
@endsection
