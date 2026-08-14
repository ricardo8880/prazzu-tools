@extends('layouts.app')

@section('title', 'Calculadora de Distribuição de Lucros | Prazzu Tools')
@section('meta_description', 'Calcule o lucro disponível e a parcela de cada sócio conforme os valores e premissas informados.')
@section('canonical_url', route('tools.distribuicao-de-lucros.index'))

@section('content')
    <x-tools.page title="Calculadora de Distribuição de Lucros" description="Calcule o lucro disponível, a distribuição por participação societária e o saldo remanescente." icon="bi-pie-chart" slug="distribuicao-de-lucros">

        <form data-testid="tool-form-panel" method="post" action="{{ route('tools.distribuicao-de-lucros.calculate') }}" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label class="form-label" for="partner_label">Sócio</label>
                <input class="form-control" id="partner_label" name="partner_label" value="{{ old('partner_label') }}" placeholder="Ex.: Sócio administrador">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="ownership_percentage">Participação (%)</label>
                <input class="form-control" id="ownership_percentage" name="ownership_percentage" value="{{ old('ownership_percentage') }}" required placeholder="Ex.: 100">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="accounting_profit">Lucro contábil</label>
                <input class="form-control" id="accounting_profit" name="accounting_profit" value="{{ old('accounting_profit') }}" required placeholder="Ex.: 60.000,00" data-e2e-value="50000,00">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="accumulated_losses">Prejuízos acumulados</label>
                <input class="form-control" id="accumulated_losses" name="accumulated_losses" value="{{ old('accumulated_losses') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="reserves_and_unavailable_amounts">Reservas/indisponíveis</label>
                <input class="form-control" id="reserves_and_unavailable_amounts" name="reserves_and_unavailable_amounts" value="{{ old('reserves_and_unavailable_amounts') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="adjustments">Ajustes</label>
                <input class="form-control" id="adjustments" name="adjustments" value="{{ old('adjustments') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="prior_distributions">Distribuições anteriores</label>
                <input class="form-control" id="prior_distributions" name="prior_distributions" value="{{ old('prior_distributions') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="intended_distribution">Distribuição pretendida</label>
                <input class="form-control" id="intended_distribution" name="intended_distribution" value="{{ old('intended_distribution') }}" required placeholder="Ex.: 45.000,00" data-e2e-value="10000,00">
            </div>
            @if($plusEnabled ?? true)
            <div class="col-12">
                <div class="card border-primary-subtle" data-plus-feature="partners"><div class="card-body">
                    <h2 class="h6">Múltiplos sócios <span class="badge text-bg-primary">Prazzu Plus</span></h2>
                    <p class="small text-body-secondary">Adicione sócios e informe as participações. A soma do sócio principal com os adicionais deve ser exatamente 100%.</p>
                    @for($i = 0; $i < 4; $i++)
                        <div class="row g-2 mb-2">
                            <div class="col-md-8"><input class="form-control" name="partners[{{ $i }}][label]" value="{{ old('partners.'.$i.'.label') }}" placeholder="Sócio adicional {{ $i + 2 }}"></div>
                            <div class="col-md-4"><div class="input-group"><input class="form-control" name="partners[{{ $i }}][ownership_percentage]" value="{{ old('partners.'.$i.'.ownership_percentage') }}" placeholder="0"><span class="input-group-text">%</span></div></div>
                        </div>
                    @endfor
                </div></div>
            </div>
            @endif
            <div class="col-12 form-check ms-2">
                <input class="form-check-input" type="checkbox" name="confirm_assumptions" value="1" id="confirm-profit" required>
                <label class="form-check-label" for="confirm-profit">Confirmo que revisei escrituração, saldos e participação.</label>
            </div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calcular</button></div>
        </form>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">{{ $errors->first() }}</div>
        @endif

        @isset($result)
    <span data-analytics-result="main" hidden></span>
            <div class="card mt-4">
                <div class="card-body">
                    <h2 class="h4">Resultado</h2>
                    @foreach ($result->summary as $item)
                        <p><strong>{{ $item->label }}:</strong> {{ $item->value }}</p>
                    @endforeach
                    @if(count($result->details['partners'] ?? []) > 1)
                        <h3 class="h6 mt-3">Distribuição por sócio <span class="badge text-bg-primary">Plus</span></h3>
                        <div class="table-responsive"><table class="table table-sm"><thead><tr><th>Sócio</th><th class="text-end">Participação</th><th class="text-end">Distribuição</th></tr></thead><tbody>
                        @foreach($result->details['partners'] as $partner)<tr><td>{{ $partner['label'] }}</td><td class="text-end">{{ $partner['ownership_percentage'] }}%</td><td class="text-end">{{ \App\Core\Money\Money::fromMinor($partner['distributed_amount_minor'])->formatPtBr() }}</td></tr>@endforeach
                        </tbody></table></div>
                    @endif
                </div>
            </div>
            <div class="d-flex gap-2 mt-3 flex-wrap">
                @foreach (['pdf' => 'Baixar PDF', 'excel' => 'Baixar Excel (.xlsx)'] as $format => $label)
                    <form method="post" action="{{ route('tools.distribuicao-de-lucros.export.'.$format) }}">
                        @csrf
                        @foreach ($result->details['input'] as $name => $value)
                            @if(is_array($value))
                                @foreach($value as $index => $row)
                                    @foreach($row as $field => $nestedValue)<input type="hidden" name="{{ $name }}[{{ $index }}][{{ $field }}]" value="{{ $nestedValue }}">@endforeach
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $name }}" value="{{ is_bool($value) ? ($value ? 1 : 0) : $value }}">
                            @endif
                        @endforeach
                        <input type="hidden" name="confirm_assumptions" value="1">
                        <button class="btn {{ $format === 'pdf' ? 'btn-outline-primary' : 'btn-outline-success' }}">{{ $label }}</button>
                    </form>
                @endforeach
            </div>
        @endisset
    </x-tools.page>
@endsection
