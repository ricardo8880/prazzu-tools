@extends('layouts.app')

@section('title', 'Simulador de Pró-Labore Ideal | Prazzu Tools')
@section('meta_description', 'Calcule retenções, valor líquido e custo empresarial do pró-labore com memória de cálculo.')
@section('canonical_url', route('tools.simulador-pro-labore-ideal.index'))

@section('content')
    <x-tools.page title="Simulador de Pró-Labore Ideal" description="Simule pró-labore, INSS, IRRF, valor líquido e custo empresarial com memória transparente." icon="bi-person-badge" slug="simulador-pro-labore-ideal">

        <form method="post" action="{{ route('tools.simulador-pro-labore-ideal.calculate') }}" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label class="form-label" for="competence">Competência</label>
                <input class="form-control" id="competence" name="competence" value="{{ old('competence', '2026-01') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="company_regime">Regime</label>
                <select class="form-select" id="company_regime" name="company_regime">
                    <option value="simples_outside_annex_iv">Simples fora do Anexo IV</option>
                    <option value="simples_annex_iv">Simples Anexo IV</option>
                    <option value="presumed_profit">Lucro Presumido</option>
                    <option value="actual_profit">Lucro Real</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="gross_pro_labore">Pró-labore bruto</label>
                <input class="form-control" id="gross_pro_labore" name="gross_pro_labore" value="{{ old('gross_pro_labore', '5000,00') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="dependents">Dependentes</label>
                <input class="form-control" id="dependents" type="number" name="dependents" value="{{ old('dependents', 0) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="other_official_social_security">Outras contribuições oficiais</label>
                <input class="form-control" id="other_official_social_security" name="other_official_social_security" value="{{ old('other_official_social_security', '0,00') }}">
            </div>
            <div class="col-12 form-check ms-2">
                <input class="form-check-input" type="checkbox" name="confirm_assumptions" value="1" id="confirm" required>
                <label class="form-check-label" for="confirm">Confirmo que revisei competência, regime e premissas.</label>
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
