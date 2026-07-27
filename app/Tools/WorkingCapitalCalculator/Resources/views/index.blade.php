@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
    <x-tools.form-panel title="Posição financeira atual" description="Informe os saldos na mesma data-base. Use zero quando uma rubrica não existir.">
        <form method="POST" action="{{ route('tools.capital-de-giro.calculate') }}" class="row g-3">
            @csrf
            @foreach ([
                'cash' => ['Caixa e bancos', 'Disponibilidades imediatas.'],
                'receivables' => ['Contas a receber', 'Valores operacionais a receber.'],
                'inventory' => ['Estoques', 'Saldo dos estoques.'],
                'other_current_assets' => ['Outros ativos circulantes', 'Outros recursos de curto prazo ligados à operação.'],
                'suppliers' => ['Fornecedores', 'Obrigações operacionais com fornecedores.'],
                'other_operating_liabilities' => ['Outras obrigações operacionais', 'Salários, tributos e outras contas do ciclo.'],
                'loans' => ['Empréstimos de curto prazo', 'Dívidas financeiras no passivo circulante.'],
                'other_current_liabilities' => ['Outras obrigações circulantes', 'Demais passivos de curto prazo.'],
            ] as $name => [$label, $help])
                <div class="col-12 col-md-6">
                    <x-tools.form.money :name="$name" :label="$label" :help="$help" :value="old($name, '0,00')" required />
                </div>
            @endforeach
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary" type="submit"><i class="bi bi-calculator me-1"></i> Calcular capital de giro</button>
                <a class="btn btn-outline-secondary" href="{{ route('tools.capital-de-giro.index') }}">Limpar</a>
            </div>
        </form>
    </x-tools.form-panel>

    @isset($result)
        <x-tools.result-panel title="Diagnóstico do capital de giro">
            <div class="row g-3">
                @foreach ($result->summary as $item)
                    <div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>
                @endforeach
            </div>
            <h3 class="h5 mt-4">Memória de cálculo</h3>
            <div class="table-responsive">
                <table class="table table-sm mb-0"><tbody>
                @foreach (($result->calculationMemory?->steps ?? []) as $step)
                    <tr><th scope="row">{{ $step->label }}<div class="small fw-normal text-body-secondary">{{ $step->formula }}</div></th><td class="text-end text-nowrap">{{ is_int($step->result) ? 'R$ '.number_format($step->result / 100, 2, ',', '.') : $step->result }}</td></tr>
                @endforeach
                </tbody></table>
            </div>
        </x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
