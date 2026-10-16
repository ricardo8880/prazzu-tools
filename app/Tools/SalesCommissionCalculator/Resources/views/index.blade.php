@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">

    <x-tools.form-panel title="Sales and commission rules">
        <form method="POST" action="{{ route('tools.sales-commission-calculator.calculate') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-6"><x-tools.form.money name="sales" label="Sales amount" :value="old('sales')" required /></div>
            <div class="col-12 col-md-6"><x-tools.form.money name="reversals" label="Refunds and chargebacks" help="Amounts que não compõem a base comissionável." :value="old('reversals')" /></div>
            <div class="col-12 col-md-6"><x-tools.form.input name="rate" label="Base commission rate" type="number" step="0.000001" min="0" max="100" suffix="%" :value="old('rate')" required /></div>
            <div class="col-12 col-md-6"><x-tools.form.money name="goal" label="Sales target" help="Opcional. Deixe em branco para calcular somente a comissão-base." :value="old('goal')" /></div>
            <div class="col-12 col-md-6"><x-tools.form.input name="goal_bonus_rate" label="Target bonus" type="number" step="0.000001" min="0" max="100" suffix="%" :value="old('goal_bonus_rate')" help="Opcional; só é aplicado quando uma meta positiva é informada e atingida." /></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calculate commission</button></div>
        </form>
    </x-tools.form-panel>
    @isset($result)
    <span data-analytics-result="main" hidden></span>
        <x-tools.result-panel title="Commission result">
            <div class="row g-3">@foreach($result->summary as $item)<div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div>
            <h3 class="h5 mt-4">Calculation details</h3>
            <div class="table-responsive"><table class="table table-sm mb-0"><tbody>@foreach($result->calculationMemory->steps as $step)<tr><th>{{ $step->label }}<div class="small fw-normal text-body-secondary">{{ $step->formula }}</div></th><td class="text-end">{{ is_int($step->result) ? '$'.number_format($step->result / 100, 2, '.', ',') : $step->result }}</td></tr>@endforeach</tbody></table></div>
            <div class="alert alert-info mt-3 mb-0">Commissionable sales and target attainment reflect the refunds entered. Review the compensation plan for timing and future chargebacks.</div>
        <x-tools.export-buttons :pdf-route="route('tools.sales-commission-calculator.export.pdf')" :excel-route="route('tools.sales-commission-calculator.export.excel')" :input="$calculationInput ?? []" />
        </x-tools.result-panel>
    @endisset
    <x-tools.form-panel class="mt-4" title="Prazzu Plus — batch processing" description="One line per seller: Name;Sales;Refunds."><form method="POST" action="{{ route('tools.sales-commission-calculator.batch') }}" class="row g-3">@csrf <div class="col-12"><label class="form-label">Sellers</label><textarea name="seller_batch" class="form-control" rows="4" required data-e2e-value="Ana;11000,00;0,00&#10;Bruno;13800,00;450,00" placeholder="Ex.: Ana;11000,00;0,00&#10;Bruno;13800,00;450,00">{{ old('seller_batch') }}</textarea></div><div class="col-md-4"><x-tools.form.input name="rate" label="Base commission rate" type="number" step="0.000001" placeholder="Ex.: 4,5" suffix="%" required /></div><div class="col-md-4"><x-tools.form.money name="goal" label="Meta individual" placeholder="Ex.: 12.000,00" required /></div><div class="col-md-4"><x-tools.form.input name="goal_bonus_rate" label="Bônus da meta" type="number" step="0.000001" placeholder="Ex.: 1,5" suffix="%" required /></div><div class="col-12"><button class="btn btn-outline-primary">Processar vendedores</button></div></form></x-tools.form-panel>
    @isset($sellerBatchResults)<x-tools.result-panel class="mt-4" title="Comissões em lote">@foreach($sellerBatchResults as $seller)<h3 class="h5 mt-3">{{ $seller['name'] }}</h3><div class="row g-3">@foreach($seller['result']->summary as $item)<div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" /></div>@endforeach</div>@endforeach</x-tools.result-panel>@endisset
</x-tools.page>
@endsection
