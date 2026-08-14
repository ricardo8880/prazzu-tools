@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
<x-tools.form-panel title="Dados do DAS"><form method="POST" action="{{route('tools.das-em-atraso.calculate')}}" class="row g-3">@csrf
<div class="col-md-6"><x-tools.form.money name="principal" label="Valor original" :value="old('principal')" required/></div><div class="col-md-3"><x-tools.form.input name="due_date" label="Vencimento" type="date" :value="old('due_date')" required/></div><div class="col-md-3"><x-tools.form.input name="payment_date" label="Data do pagamento" type="date" :value="old('payment_date',date('Y-m-d'))" required/></div>
<div class="col-md-6"><x-tools.form.input name="accumulated_selic" label="Selic acumulada até o mês anterior" type="number" step="0.000001" suffix="%" help="Consulte a taxa oficial acumulada aplicável ao período." :value="old('accumulated_selic')" data-e2e-value="5" placeholder="Ex.: 1,25" required/></div><div class="col-12"><button class="btn btn-primary">Atualizar DAS</button></div></form></x-tools.form-panel>
@isset($result)
    <span data-analytics-result="main" hidden></span><x-tools.result-panel title="Atualização"><div class="row g-3">@foreach($result->summary as $item)<div class="col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description"/></div>@endforeach</div><h3 class="h5 mt-4">Memória</h3><table class="table table-sm"><tbody>@foreach(($result->calculationMemory?->steps ?? []) as $step)<tr><th>{{ $step->label }}<div class="small fw-normal text-body-secondary">{{ $step->formula }}</div></th><td class="text-end">{{ is_int($step->result) ? 'R$ '.number_format($step->result / 100, 2, ',', '.') : $step->result }}</td></tr>@endforeach</tbody></table><div class="alert alert-warning">Estimativa. A emissão oficial deve ser feita no PGDAS-D/Portal do Simples, que aplica a Selic oficial.</div><x-tools.export-buttons :pdf-route="route('tools.das-em-atraso.export.pdf')" :excel-route="route('tools.das-em-atraso.export.excel')" :input="$calculationInput ?? []" /></x-tools.result-panel>@endisset</x-tools.page>
@endsection
