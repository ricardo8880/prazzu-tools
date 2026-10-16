@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
    <x-tools.form-panel title="Workforce movement" description="Use the same reporting period for hires, separations, and average headcount.">
        <form method="POST" action="{{ route('tools.employee-turnover-calculator.calculate') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-4"><x-tools.form.input name="admissions" label="Hires" type="number" min="0" step="1" :value="old('admissions')" required /></div>
            <div class="col-12 col-md-4"><x-tools.form.input name="terminations" label="Separations" type="number" min="0" step="1" :value="old('terminations')" required /></div>
            <div class="col-12 col-md-4"><x-tools.form.input name="average_headcount" label="Average headcount" type="number" min="1" step="1" :value="old('average_headcount')" required /></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calculate turnover</button></div>
        </form>
    </x-tools.form-panel>

    @isset($result)
        <span data-analytics-result="main" hidden></span>
        <x-tools.result-panel title="Turnover result">
            <div class="row g-3">
                @foreach($result->summary as $item)
                    <div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>
                @endforeach
            </div>
            <div class="alert alert-info mt-4 mb-0">
                Formula: ((hires + separations) ÷ 2) ÷ average headcount × 100. This operational metric may differ from your organization’s internal methodology.
            </div>
        </x-tools.result-panel>
    @endisset

    <x-tools.form-panel class="mt-4" title="Prazzu Plus — period or segment analysis" description="Compare 2 to 12 periods or segments using the same formula.">
        <form method="POST" action="{{ route('tools.employee-turnover-calculator.segmented-analysis') }}" class="row g-3">
            @csrf
            <div class="col-12">
                <label for="segments" class="form-label">Periods/segments</label>
                <textarea id="segments" name="segments" rows="5" class="form-control" required data-e2e-value="1º trimestre|12|8|120&#10;2º trimestre|7|10|118" placeholder="1º trimestre|12|8|120
2º trimestre|7|10|118">{{ old('segments', $segmentInput ?? '') }}</textarea>
                <div class="form-text">One line per segment: name|hires|separations|average_headcount.</div>
                @error('segments')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-12"><button class="btn btn-outline-primary" type="submit">Compare turnover</button></div>
        </form>
    </x-tools.form-panel>

    @isset($segmentAnalysis)
        <x-tools.result-panel class="mt-4" title="Turnover comparison">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Period/segment</th><th>Hires</th><th>Separations</th><th>Average headcount</th><th>Turnover</th></tr></thead>
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
