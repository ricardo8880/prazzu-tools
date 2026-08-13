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

    <x-tools.form-panel class="mt-4" title="Prazzu Plus — análise por período ou segmento" description="Compare de 2 a 12 recortes usando exatamente a mesma fórmula do cálculo Essencial.">
        <form method="POST" action="{{ route('tools.calculadora-turnover.segmented-analysis') }}" class="row g-3">
            @csrf
            <div class="col-12">
                <label for="segments" class="form-label">Períodos/segmentos</label>
                <textarea id="segments" name="segments" rows="5" class="form-control" required data-e2e-value="1º trimestre|12|8|120&#10;2º trimestre|7|10|118" placeholder="1º trimestre|12|8|120
2º trimestre|7|10|118">{{ old('segments', $segmentInput ?? '') }}</textarea>
                <div class="form-text">Formato por linha: nome|admissões|desligamentos|quadro_médio.</div>
                @error('segments')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-12"><button class="btn btn-outline-primary" type="submit">Comparar turnover</button></div>
        </form>
    </x-tools.form-panel>

    @isset($segmentAnalysis)
        <x-tools.result-panel class="mt-4" title="Comparação de turnover">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Período/segmento</th><th>Admissões</th><th>Desligamentos</th><th>Quadro médio</th><th>Turnover</th></tr></thead>
                    <tbody>
                    @foreach($segmentAnalysis as $row)
                        <tr>
                            <th>{{ $row['segment'] }}</th>
                            <td>{{ $row['admissions'] }}</td>
                            <td>{{ $row['terminations'] }}</td>
                            <td>{{ $row['average_headcount'] }}</td>
                            <td>{{ $row['result']->summary[0]->value }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </x-tools.result-panel>
    @endisset

</x-tools.page>
@endsection
