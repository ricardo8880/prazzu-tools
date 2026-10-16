@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">

    <x-tools.form-panel title="Current financial position" description="Enter balances as of the same date. Use zero when an item does not apply.">
        <form method="POST" action="{{ route('tools.working-capital-calculator.calculate') }}" class="row g-3" data-analytics-form="main">
            @csrf
            @foreach ([
                'cash' => ['Caixa e bancos', 'Disponibilidades imediatas.'],
                'receivables' => ['Contas a receber', 'Amounts operacionais a receber.'],
                'inventory' => ['Estoques', 'Saldo dos estoques.'],
                'other_current_assets' => ['Outros ativos circulantes', 'Outros recursos de curto prazo ligados à operação.'],
                'suppliers' => ['Fornecedores', 'Obrigações operacionais com fornecedores.'],
                'other_operating_liabilities' => ['Outras obrigações operacionais', 'Wages, tributos e outras contas do ciclo.'],
                'loans' => ['Empréstimos de curto prazo', 'Dívidas financeiras no passivo circulante.'],
                'other_current_liabilities' => ['Outras obrigações circulantes', 'Demais passivos de curto prazo.'],
            ] as $name => [$label, $help])
                <div class="col-12 col-md-6">
                    <x-tools.form.money :name="$name" :label="$label" :help="$help" :value="old($name)" required />
                </div>
            @endforeach
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary" type="submit"><i class="bi bi-calculator me-1"></i> Calculate working capital</button>
                <a class="btn btn-outline-secondary" href="{{ route('tools.working-capital-calculator.index') }}">Clear</a>
            </div>
        </form>
    </x-tools.form-panel>

    @isset($result)
        <x-tools.result-panel title="Working capital result" data-analytics-result="main">
            <div class="row g-3">
                @foreach ($result->summary as $item)
                    <div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>
                @endforeach
            </div>
            @foreach($result->warnings as $warning)
                <div class="alert {{ $warning->level->value === 'danger' ? 'alert-danger' : ($warning->level->value === 'warning' ? 'alert-warning' : 'alert-info') }} mt-3" role="status">@if($warning->title)<strong>{{ $warning->title }}.</strong> @endif{{ $warning->message }}</div>
            @endforeach
            <h3 class="h5 mt-4">Calculation details</h3>
            <div class="table-responsive">
                <table class="table table-sm mb-0"><tbody>
                @foreach (($result->calculationMemory?->steps ?? []) as $step)
                    <tr><th scope="row">{{ $step->label }}<div class="small fw-normal text-body-secondary">{{ $step->formula }}</div></th><td class="text-end text-nowrap">{{ is_int($step->result) ? '$'.number_format($step->result / 100, 2, '.', ',') : $step->result }}</td></tr>
                @endforeach
                </tbody></table>
            </div>
        <x-tools.export-buttons :pdf-route="route('tools.working-capital-calculator.export.pdf')" :excel-route="route('tools.working-capital-calculator.export.excel')" :input="$calculationInput ?? []" /></x-tools.result-panel>
        <x-tools.form-panel class="mt-4" title="Prazzu Plus — scenario projection" description="Project the position by applying changes to the financial groups.">
            <form method="POST" action="{{ route('tools.working-capital-calculator.projections') }}" class="row g-3">@csrf
                @foreach (($calculationInput ?? []) as $name => $value)<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endforeach
                <div class="col-md-4"><x-tools.form.input name="asset_growth_rate" label="Asset change" type="number" step="0.01" placeholder="Ex.: 12" suffix="%" required /></div>
                <div class="col-md-4"><x-tools.form.input name="operating_liability_growth_rate" label="Operating liabilities" type="number" step="0.01" placeholder="Ex.: 6" suffix="%" required /></div>
                <div class="col-md-4"><x-tools.form.input name="financial_liability_growth_rate" label="Financial liabilities" type="number" step="0.01" placeholder="Ex.: 2" suffix="%" required /></div>
                <div class="col-12"><button class="btn btn-outline-primary">Project scenario</button></div>
            </form>
        </x-tools.form-panel>
    @endisset
    @isset($projectionScenarios)
        <x-tools.result-panel class="mt-4" title="Working capital comparison">
            @foreach($projectionScenarios as $scenario)<h3 class="h5 mt-3">{{ $scenario['name'] }}</h3><div class="row g-3">@foreach($scenario['result']->summary as $item)<div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" /></div>@endforeach</div>@endforeach
        </x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
