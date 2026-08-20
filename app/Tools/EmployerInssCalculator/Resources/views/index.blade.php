@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
    <x-tools.form-panel title="Folha e enquadramento">
        <form method="POST" action="{{ route('tools.inss-patronal.calculate') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-6"><x-tools.form.money name="payroll" label="Base da folha" :value="old('payroll')" required /></div>
            <div class="col-12 col-md-6">
                <x-tools.form.select
                    name="regime"
                    label="Enquadramento"
                    :options="[
                        'general' => 'Regime geral (Lucro Presumido/Real)',
                        'simples_annex_iv' => 'Simples Nacional — Anexo IV',
                        'simples_other' => 'Simples Nacional — demais anexos',
                    ]"
                    :value="old('regime', 'general')"
                    required
                />
            </div>
            <div class="col-12 col-md-6"><x-tools.form.input name="adjusted_rat" label="RAT ajustado pelo FAP" type="number" step="0.000001" min="0" max="15" suffix="%" :value="old('adjusted_rat')" data-e2e-value="1.2" required placeholder="Ex.: 1,20" /></div>
            <div class="col-12 col-md-6"><x-tools.form.input name="third_parties" label="Terceiros" type="number" step="0.000001" min="0" max="15" suffix="%" :value="old('third_parties')" data-e2e-value="5.8" required placeholder="Ex.: 5,80" /></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calcular INSS patronal</button></div>
        </form>
    </x-tools.form-panel>
    @isset($result)
    <span data-analytics-result="main" hidden></span>
        <x-tools.result-panel title="Contribuições patronais">
            <div class="row g-3">@foreach($result->summary as $item)<div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div>
            <h3 class="h5 mt-4">Memória de cálculo</h3>
            <div class="table-responsive"><table class="table table-sm mb-0"><tbody>@foreach(($result->calculationMemory?->steps ?? []) as $step)<tr><th>{{ $step->label }}<div class="small fw-normal text-body-secondary">{{ $step->formula }}</div></th><td class="text-end">{{ is_int($step->result) ? 'R$ '.number_format($step->result / 100, 2, ',', '.') : $step->result }}</td></tr>@endforeach</tbody></table></div>
            <div class="alert alert-warning mt-3 mb-0">Confirme FPAS, código de terceiros, CNAE preponderante, RAT, FAP, desoneração e decisões judiciais.</div>
        <x-tools.export-buttons :pdf-route="route('tools.inss-patronal.export.pdf')" :excel-route="route('tools.inss-patronal.export.excel')" :input="$calculationInput ?? []" />
            <x-tools.normative-trust
                :rules="$result->calculationMemory?->normativeRules ?? []"
                :assumptions="$result->calculationMemory?->assumptions ?? []"
                :is-estimate="$result->calculationMemory?->isEstimate ?? false"
            />
</x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
