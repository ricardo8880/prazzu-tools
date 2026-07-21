@extends('layouts.app')
@section('title', 'Histórico de DARF/GPS')
@section('content')
<div class="container py-4">
    <x-tools.intro icon="clock-history" tone="green" title="Histórico de DARF/GPS" description="Recupere cálculos, marque favoritos ou exclua registros.">
        <x-slot:actions>
            <a class="btn btn-outline-secondary" href="{{ route('tools.gerador-darf-gps.history.index', ['favorite' => $favorite ? null : 1]) }}">{{ $favorite ? 'Ver todos' : 'Somente favoritos' }}</a>
            <a class="btn btn-primary" href="{{ route('tools.gerador-darf-gps.index') }}">Novo cálculo</a>
        </x-slot:actions>
    </x-tools.intro>
    @if(session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif
    <div class="vstack gap-3">
        @forelse($runs as $run)
            <article class="card"><div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2"><strong>{{ strtoupper($run->input['guide_type'] ?? 'guia') }} {{ $run->input['revenue_code'] ?? '—' }}</strong>@if($run->favorite)<span class="badge text-bg-warning">Favorito</span>@endif</div>
                    <div class="small text-body-secondary">Principal: {{ $run->result['amounts']['principal'] ?? '—' }} · Total: {{ $run->result['amounts']['total'] ?? '—' }}</div>
                    <div class="small text-body-secondary">Pagamento previsto: {{ isset($run->input['payment_date']) ? \Carbon\CarbonImmutable::parse($run->input['payment_date'])->format('d/m/Y') : '—' }}</div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <form method="post" action="{{ route('tools.gerador-darf-gps.history.repeat', $run->id) }}">@csrf<button class="btn btn-sm btn-primary">Reutilizar</button></form>
                    @foreach(['csv' => 'CSV', 'json' => 'JSON', 'pdf' => 'PDF'] as $format => $label)<a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.gerador-darf-gps.history.export', [$run->id, $format]) }}">{{ $label }}</a>@endforeach
                    <form method="post" action="{{ route('tools.gerador-darf-gps.history.favorite', $run->id) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-warning">{{ $run->favorite ? 'Desfavoritar' : 'Favoritar' }}</button></form>
                    <form method="post" action="{{ route('tools.gerador-darf-gps.history.destroy', $run->id) }}" onsubmit="return confirm('Excluir este cálculo?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Excluir</button></form>
                </div>
            </div></article>
        @empty
            <div class="alert alert-light border">{{ $favorite ? 'Nenhum cálculo favorito.' : 'Nenhum cálculo salvo ainda.' }}</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $runs->links() }}</div>
</div>
@endsection
