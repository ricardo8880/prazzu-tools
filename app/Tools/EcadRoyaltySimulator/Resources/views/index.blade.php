@extends('layouts.app')
@section('title','Simulador Orientativo de ECAD e Direitos Autorais — Prazzu Tools')
@section('meta_description','Simule valores de referência de execução pública musical por UDA, UDA por m² ou percentual, com parâmetros da tabela oficial aplicável.')
@section('content')
<x-tools.page title="Simulador Orientativo de ECAD e Direitos Autorais" description="Transcreva o critério da tabela vigente do Ecad e confira o valor matemático sem o sistema inventar seu enquadramento." icon="music-note-beamed" slug="simulador-ecad-direitos-autorais">
    <x-tools.validation-summary />
    <div class="alert alert-info small">Referência oficial atual: UDA de <strong>R$ 107,31</strong>, vigente até dezembro de 2026. O enquadramento e a tarifa aplicável devem ser confirmados no Regulamento de Arrecadação/Tabela de Preços do Ecad.</div>
    <form method="post" action="{{ route('tools.simulador-ecad-direitos-autorais.calculate') }}" class="d-grid gap-4" data-testid="tool-form-panel">
        @csrf
        <x-tools.form-panel title="Critério oficial informado" description="Escolha a forma de cálculo que aparece na linha aplicável da tabela do Ecad.">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="method">Forma de cálculo</label><select class="form-select" id="method" name="method" required><option value="uda" @selected(old('method','uda')==='uda')>Quantidade de UDA</option><option value="uda_per_sqm" @selected(old('method')==='uda_per_sqm')>UDA por m²</option><option value="percentage" @selected(old('method')==='percentage')>Percentual sobre uma base monetária</option></select></div>
                <div class="col-md-6"><label class="form-label" for="uda_value">Valor da UDA usado na competência</label><div class="input-group"><span class="input-group-text">R$</span><input class="form-control" id="uda_value" name="uda_value" value="{{ old('uda_value','107,31') }}" required></div><div class="form-text">Atualize este campo quando a UDA da sua competência for diferente da referência de 2026.</div></div>
                <div class="col-md-4"><label class="form-label" for="uda_quantity">Quantidade de UDA</label><input class="form-control" id="uda_quantity" name="uda_quantity" value="{{ old('uda_quantity','1') }}" placeholder="Ex.: 3"></div>
                <div class="col-md-4"><label class="form-label" for="area_square_meters">Área (m²)</label><input class="form-control" id="area_square_meters" name="area_square_meters" value="{{ old('area_square_meters') }}" placeholder="Ex.: 120"></div>
                <div class="col-md-4"><label class="form-label" for="uda_per_square_meter">UDA por m²</label><input class="form-control" id="uda_per_square_meter" name="uda_per_square_meter" value="{{ old('uda_per_square_meter') }}" placeholder="Ex.: 0,012"></div>
                <div class="col-md-6"><label class="form-label" for="reference_amount">Base monetária</label><div class="input-group"><span class="input-group-text">R$</span><input class="form-control" id="reference_amount" name="reference_amount" value="{{ old('reference_amount') }}" placeholder="Ex.: 10.000,00"></div></div>
                <div class="col-md-6"><label class="form-label" for="percentage_rate">Percentual da tabela</label><div class="input-group"><input class="form-control" id="percentage_rate" name="percentage_rate" value="{{ old('percentage_rate') }}" placeholder="Ex.: 2,50"><span class="input-group-text">%</span></div></div>
                <div class="col-12"><input type="hidden" name="project_periods" value="0"><div class="form-check"><input class="form-check-input" type="checkbox" id="project_periods" name="project_periods" value="1" @checked(old('project_periods',false))><label class="form-check-label" for="project_periods">Projetar o mesmo valor por múltiplos períodos (Plus)</label></div></div>
                <div class="col-md-4"><label class="form-label" for="periods">Quantidade de períodos</label><input class="form-control" type="number" min="1" max="60" id="periods" name="periods" value="{{ old('periods','12') }}"></div>
            </div>
        </x-tools.form-panel>
        <div><button class="btn btn-primary" type="submit">Simular valor</button></div>
    </form>
    @isset($result)
        <div class="mt-5" data-testid="tool-result">
            <x-tools.result-panel title="Estimativa orientativa">
                <div class="row g-3">
                    @foreach($result->summary as $item)
                        <div class="col-md-4">
                            <x-tools.result-metric :label="$item->label" :value="$item->value" icon="calculator" />
                        </div>
                    @endforeach
                </div>

                @foreach($result->warnings as $warning)
                    <div class="alert alert-light border small mt-3">{{ $warning->message }}</div>
                @endforeach

                @if($showProjection ?? false)
                    <div class="alert alert-secondary small mt-3">
                        Projeção calculada para {{ $result->details['periods'] }} períodos, sem reajustes futuros ou alteração de enquadramento.
                    </div>
                @endif
            </x-tools.result-panel>
        </div>
    @endisset
</x-tools.page>
@endsection
