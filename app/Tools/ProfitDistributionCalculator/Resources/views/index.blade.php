@extends('layouts.app')

@section('title', 'Calculadora de Distribuição de Lucros | Prazzu Tools')
@section('meta_description', 'Calcule o lucro disponível e a parcela de cada sócio conforme os valores e premissas informados.')
@section('canonical_url', route('tools.distribuicao-de-lucros.index'))

@section('content')
    <x-tools.page title="Calculadora de Distribuição de Lucros" description="Calcule o lucro disponível, a distribuição por participação societária e o saldo remanescente." icon="bi-pie-chart" slug="distribuicao-de-lucros">

        <form method="post" action="{{ route('tools.distribuicao-de-lucros.calculate') }}" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label class="form-label" for="partner_label">Sócio</label>
                <input class="form-control" id="partner_label" name="partner_label" value="{{ old('partner_label', 'Sócio') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="ownership_percentage">Participação (%)</label>
                <input class="form-control" id="ownership_percentage" name="ownership_percentage" value="{{ old('ownership_percentage', '100') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="accounting_profit">Lucro contábil</label>
                <input class="form-control" id="accounting_profit" name="accounting_profit" value="{{ old('accounting_profit', '50000,00') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="accumulated_losses">Prejuízos acumulados</label>
                <input class="form-control" id="accumulated_losses" name="accumulated_losses" value="{{ old('accumulated_losses', '0,00') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="reserves_and_unavailable_amounts">Reservas/indisponíveis</label>
                <input class="form-control" id="reserves_and_unavailable_amounts" name="reserves_and_unavailable_amounts" value="{{ old('reserves_and_unavailable_amounts', '0,00') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="adjustments">Ajustes</label>
                <input class="form-control" id="adjustments" name="adjustments" value="{{ old('adjustments', '0,00') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="prior_distributions">Distribuições anteriores</label>
                <input class="form-control" id="prior_distributions" name="prior_distributions" value="{{ old('prior_distributions', '0,00') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="intended_distribution">Distribuição pretendida</label>
                <input class="form-control" id="intended_distribution" name="intended_distribution" value="{{ old('intended_distribution', '50000,00') }}" required>
            </div>
            <div class="col-12 form-check ms-2">
                <input class="form-check-input" type="checkbox" name="confirm_assumptions" value="1" id="confirm-profit" required>
                <label class="form-check-label" for="confirm-profit">Confirmo que revisei escrituração, saldos e participação.</label>
            </div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calcular</button></div>
        </form>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">{{ $errors->first() }}</div>
        @endif

        @isset($result)
            <div class="card mt-4">
                <div class="card-body">
                    <h2 class="h4">Resultado</h2>
                    @foreach ($result->summary as $item)
                        <p><strong>{{ $item->label }}:</strong> {{ $item->value }}</p>
                    @endforeach
                </div>
            </div>
        @endisset
    </x-tools.page>
@endsection
