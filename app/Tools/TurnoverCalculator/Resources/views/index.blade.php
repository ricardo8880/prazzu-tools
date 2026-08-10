@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
    <x-tools.form-panel title="Movimentação no período" description="Use o mesmo período de referência para admissões, desligamentos e quadro médio.">
        <form method="POST" action="{{ route('tools.calculadora-turnover.calculate') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-4"><x-tools.form.input name="admissions" label="Admissões" type="number" min="0" step="1" :value="old('admissions')" required /></div>
            <div class="col-12 col-md-4"><x-tools.form.input name="terminations" label="Desligamentos" type="number" min="0" step="1" :value="old('terminations')" required /></div>
            <div class="col-12 col-md-4"><x-tools.form.input name="average_headcount" label="Quadro médio" type="number" min="1" step="1" :value="old('average_headcount')" required /></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calcular turnover</button></div>
        </form>
    </x-tools.form-panel>

    @isset($result)
        <span data-analytics-result="main" hidden></span>
        <x-tools.result-panel title="Resultado do turnover">
            <div class="row g-3">
                @foreach($result->summary as $item)
                    <div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>
                @endforeach
            </div>
            <div class="alert alert-info mt-4 mb-0">
                Fórmula utilizada: ((admissões + desligamentos) ÷ 2) ÷ quadro médio × 100. Este é um indicador operacional e pode diferir da metodologia interna da sua organização.
            </div>
        </x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
