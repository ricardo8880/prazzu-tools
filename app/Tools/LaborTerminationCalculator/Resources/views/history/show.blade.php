@extends('layouts.app')
@section('title', 'Final Pay & Severance Details | Prazzu Tools')
@section('content')
@php($result = $run->result ?? [])
<div class="prazzu-page tool-page">
    <nav aria-label="Breadcrumb" class="mb-3"><ol class="breadcrumb prazzu-breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('tools.final-pay-severance-calculator.history.index') }}">History</a></li><li class="breadcrumb-item active">Details</li></ol></nav>
    <x-tools.intro icon="file-earmark-text" tone="pink" title="Final Pay & Severance Details" :description="'Saved calculation from '.($run->finishedAt?->format('M j, Y g:i A') ?? '—')" badge="Saved calculation" badge-class="badge text-bg-success mb-2">
        <x-slot:actions><div class="text-lg-end"><small class="text-body-secondary d-block">Estimated net final pay</small><strong class="fs-3 text-success">{{ $result['net_final_pay'] ?? ($result['net_total'] ?? '—') }}</strong></div></x-slot:actions>
    </x-tools.intro>
    <div class="d-flex flex-wrap gap-2 mb-4"><form method="post" action="{{ route('tools.final-pay-severance-calculator.history.repeat', $run->id) }}">@csrf<button class="btn btn-primary">Reuse inputs</button></form><a class="btn btn-outline-primary" target="_blank" href="{{ route('tools.final-pay-severance-calculator.history.pdf', $run->id) }}">Export PDF</a><a class="btn btn-outline-secondary" href="{{ route('tools.final-pay-severance-calculator.history.index') }}">Back</a><form method="post" action="{{ route('tools.final-pay-severance-calculator.history.destroy', $run->id) }}" data-confirm="Delete this saved calculation?">@csrf @method('DELETE')<button class="btn btn-outline-danger">Delete</button></form></div>
    <section class="prazzu-tool-card"><h2 class="prazzu-section-title">Summary</h2><div class="table-responsive"><table class="table table-striped"><tbody>
    @foreach (['Regular pay due'=>'regular_pay_due','PTO payout'=>'pto_payout','Severance pay'=>'severance_pay','Bonus / commission'=>'bonus_commission_due','Gross final pay'=>'gross_final_pay','Federal withholding'=>'federal_withholding','State/local withholding'=>'state_local_withholding','Other deductions'=>'other_deductions','Estimated net final pay'=>'net_final_pay'] as $label=>$key)<tr><th>{{ $label }}</th><td class="text-end">{{ $result[$key] ?? '—' }}</td></tr>@endforeach
    </tbody></table></div></section>
</div>
@endsection
