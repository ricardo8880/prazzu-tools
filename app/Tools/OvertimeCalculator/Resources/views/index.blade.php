@extends('layouts.app')
@section('title','Calculadora de Hora Extra, Adicional Noturno e DSR')
@section('meta_description','Calcule hora extra 50% e 100%, adicional noturno com hora reduzida, DSR e projeção de reflexos trabalhistas.')
@section('content')
<x-tools.page title="Calculadora de Hora Extra, Adicional Noturno e DSR" description="Calcule verbas variáveis de jornada com memória de cálculo e premissas explícitas." icon="bi-clock-history" slug="calculadora-hora-extra">
<div class="alert alert-info"><strong>Escopo.</strong> Empregado urbano CLT comum. Convenções coletivas, escalas especiais, banco de horas, 12x36 e categorias especiais devem ser conferidos separadamente.</div>
<form method="POST" action="{{ route('tools.calculadora-hora-extra.calculate') }}" class="vstack gap-4">@csrf
<x-tools.form-panel title="Hora extra" description="Resolve o cálculo principal do valor da hora e das horas extraordinárias." badge="Essencial"><div class="row g-3">
<div class="col-md-3"><x-tools.form.input name="competence" label="Competência" type="month" :value="old('competence', now()->format('Y-m'))" required /></div>
<div class="col-md-3"><x-tools.form.money name="base_salary" label="Salário-base" :value="old('base_salary')" required /></div>
<div class="col-md-3"><x-tools.form.input name="monthly_hours" label="Divisor mensal" type="number" min="1" max="744" :value="old('monthly_hours')" required help="Use o divisor aplicável à jornada/contrato." /></div>
<div class="col-md-3"><x-tools.form.input name="overtime_50_hours" label="Horas extras 50%" :value="old('overtime_50_hours')" /></div>
<div class="col-md-3"><x-tools.form.input name="overtime_100_hours" label="Horas extras 100%" :value="old('overtime_100_hours')" /></div>
<div class="col-md-3"><x-tools.form.input name="custom_overtime_hours" label="Horas extras personalizadas" :value="old('custom_overtime_hours')" /></div>
<div class="col-md-3"><x-tools.form.input name="custom_premium" label="Adicional personalizado (%)" type="number" min="50" max="500" step="0.01" :value="old('custom_premium')" /></div>
</div></x-tools.form-panel>
<x-tools.form-disclosure title="Noturno, DSR e reflexos" description="Abra para adicionar jornada noturna, DSR ou projeção de reflexos." badge="Prazzu Plus" :open="$errors->hasAny(['night_clock_hours', 'night_overtime_hours', 'night_overtime_premium', 'working_days', 'rest_days', 'include_dsr', 'include_reflexes'])"><div class="row g-3">
<div class="col-md-3"><x-tools.form.input name="night_clock_hours" label="Horas-relógio noturnas" :value="old('night_clock_hours')" help="Período urbano noturno; a ferramenta converte pela hora reduzida de 52m30s." /></div>
<div class="col-md-3"><x-tools.form.input name="night_overtime_hours" label="Horas extras noturnas" :value="old('night_overtime_hours')" /></div>
<div class="col-md-3"><x-tools.form.input name="night_overtime_premium" label="Adicional da extra noturna (%)" type="number" min="50" max="500" step="0.01" :value="old('night_overtime_premium')" /></div>
<div class="col-md-3"><x-tools.form.input name="working_days" label="Dias úteis/trabalhados" type="number" min="0" max="31" :value="old('working_days')" /></div>
<div class="col-md-3"><x-tools.form.input name="rest_days" label="Repousos/feriados" type="number" min="0" max="15" :value="old('rest_days')" /></div>
<div class="col-md-4 form-check mt-4 ms-2"><input class="form-check-input" type="checkbox" name="include_dsr" value="1" id="include_dsr" @checked(old('include_dsr'))><label class="form-check-label" for="include_dsr">Calcular DSR sobre as verbas variáveis</label></div>
<div class="col-md-4 form-check mt-4 ms-2"><input class="form-check-input" type="checkbox" name="include_reflexes" value="1" id="include_reflexes" @checked(old('include_reflexes'))><label class="form-check-label" for="include_reflexes">Projetar reflexos em 13º, férias + 1/3 e FGTS</label></div>
</div></x-tools.form-disclosure>
<div class="form-check"><input class="form-check-input" type="checkbox" name="confirm_assumptions" value="1" id="confirm_assumptions" required @checked(old('confirm_assumptions'))><label class="form-check-label" for="confirm_assumptions">Confirmo que o divisor, percentuais, calendário e enquadramento informados são aplicáveis ao caso.</label></div>
<div><button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-calculator me-2"></i>Calcular</button></div></form>
@isset($result)
    <span data-analytics-result="main" hidden></span>
@php
$money = static fn (int $v) => \App\Core\Money\Money::fromMinor($v)->formatPtBr();
@endphp
<div class="mt-5"><x-tools.result-panel title="Horas extras calculadas" description="Estimativa baseada nos dados e parâmetros informados." eyebrow="Cálculo concluído"><div class="row g-3 mb-4">@foreach($result->summary as $item)<div class="col-12 col-md-6 col-xl"><x-tools.result-metric :label="$item->label" :value="$item->value" icon="clock-history" /></div>@endforeach</div>
@php
$reflexTotalMinor = $result->details['thirteenth_minor'] + $result->details['vacation_minor'] + $result->details['vacation_third_minor'] + $result->details['fgts_minor'];
@endphp
<x-tools.result-insight
    :title="'As verbas variáveis do mês somam '.$money($result->details['total_minor'])"
    :description="'Esse total reúne as horas extras e adicionais calculados e, quando solicitado, o DSR. Ele deve ser lido além do salário-base informado, não como salário líquido.'"
    :items="[
        'Valor da hora normal usado como base: '.$money($result->details['hourly_minor']).'.',
        $result->details['dsr_minor'] > 0 ? 'DSR incluído no total mensal: '.$money($result->details['dsr_minor']).'.' : 'DSR não foi incluído neste cenário.',
        $reflexTotalMinor > 0 ? 'Reflexos projetados separadamente (13º, férias, 1/3 e FGTS): '.$money($reflexTotalMinor).'. Eles não devem ser confundidos com o pagamento variável do mês.' : 'Nenhuma projeção de reflexos foi solicitada.',
    ]"
    icon="bi-clock-history"
/>
@foreach($result->warnings as $warning)<div class="alert alert-warning">{{ $warning->message }}</div>@endforeach
<div class="table-responsive"><table class="table table-sm"><tbody>
<tr><th>Hora extra 50%</th><td class="text-end">{{ $money($result->details['ot50_minor']) }}</td></tr><tr><th>Hora extra 100%</th><td class="text-end">{{ $money($result->details['ot100_minor']) }}</td></tr><tr><th>Hora extra personalizada</th><td class="text-end">{{ $money($result->details['custom_minor']) }}</td></tr><tr><th>Adicional noturno</th><td class="text-end">{{ $money($result->details['night_minor']) }}</td></tr><tr><th>Horas extras noturnas</th><td class="text-end">{{ $money($result->details['night_overtime_minor']) }}</td></tr><tr><th>DSR</th><td class="text-end">{{ $money($result->details['dsr_minor']) }}</td></tr><tr class="table-light"><th>Total variável</th><td class="text-end fw-bold">{{ $money($result->details['total_minor']) }}</td></tr>
@if($result->details['thirteenth_minor'] || $result->details['vacation_minor'])<tr><th>Projeção 13º</th><td class="text-end">{{ $money($result->details['thirteenth_minor']) }}</td></tr><tr><th>Projeção férias</th><td class="text-end">{{ $money($result->details['vacation_minor']) }}</td></tr><tr><th>1/3 sobre férias projetadas</th><td class="text-end">{{ $money($result->details['vacation_third_minor']) }}</td></tr><tr><th>FGTS estimado</th><td class="text-end">{{ $money($result->details['fgts_minor']) }}</td></tr>@endif
</tbody></table></div>
<h3 class="h5 mt-4">Memória de cálculo</h3><div class="accordion mb-4" id="ot-memory">@foreach($result->calculationMemory->steps as $step)<div class="accordion-item"><h4 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ot-memory-{{ $loop->index }}">{{ $step->label }}</button></h4><div id="ot-memory-{{ $loop->index }}" class="accordion-collapse collapse"><div class="accordion-body"><strong>Fórmula:</strong> {{ $step->formula }}</div></div></div>@endforeach</div>
@if(!empty($historySaved))<div class="alert alert-success">Cálculo salvo no histórico da sua conta.</div>@endif
@php
$exportInput = [
    'competence' => $result->details['input']['competence'],
    'base_salary' => $result->details['input']['baseSalary'],
    'monthly_hours' => $result->details['input']['monthlyHours'],
    'overtime_50_hours' => $result->details['input']['overtime50Hours'],
    'overtime_100_hours' => $result->details['input']['overtime100Hours'],
    'custom_overtime_hours' => $result->details['input']['customOvertimeHours'],
    'custom_premium' => $result->details['input']['customPremium'],
    'night_clock_hours' => $result->details['input']['nightClockHours'],
    'night_overtime_hours' => $result->details['input']['nightOvertimeHours'],
    'night_overtime_premium' => $result->details['input']['nightOvertimePremium'],
    'working_days' => $result->details['input']['workingDays'],
    'rest_days' => $result->details['input']['restDays'],
    'include_dsr' => $result->details['input']['includeDsr'],
    'include_reflexes' => $result->details['input']['includeReflexes'],
    'confirm_assumptions' => 1,
];
@endphp
<x-tools.export-buttons
    :pdf-route="route('tools.calculadora-hora-extra.export', 'pdf')"
    :excel-route="route('tools.calculadora-hora-extra.export', 'xlsx')"
    :input="$exportInput"
/>

            <x-tools.normative-trust
                :rules="$result->calculationMemory?->normativeRules ?? []"
                :assumptions="$result->calculationMemory?->assumptions ?? []"
                :is-estimate="$result->calculationMemory?->isEstimate ?? false"
            />
</x-tools.result-panel></div>@endisset
</x-tools.page>@endsection
