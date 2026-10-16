@extends('layouts.app')
@section('title', 'Audience | Analytics | Prazzu Tools')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-xl-row justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex flex-wrap gap-2 mb-2">
                <span class="badge text-bg-primary">Analytics 2.0</span>
                <a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.funnels') }}">Funnels</a>
                <a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.index') }}">Dashboard</a>
                <a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.acquisition') }}">Acquisition</a>
            </div>
            <h1 class="h2 mb-1">Audience</h1>
            <p class="text-body-secondary mb-0">Aggregate visitor, device, and geographic distribution.</p>
        </div>
        <form method="get" class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="row g-2 align-items-end">
                    <div class="col-auto"><label class="form-label small" for="period">Period</label><select class="form-select" id="period" name="period">@foreach(['today'=>'Today','yesterday'=>'Yesterday','7'=>'7 days','30'=>'30 days','90'=>'90 days'] as $value=>$label)<option value="{{ $value }}" @selected($selected_period===$value)>{{ $label }}</option>@endforeach</select></div>
                    <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Apply</button></div>
                </div>
            </div>
        </form>
    </div>

    @include('admin.analytics.partials.page-guide', ['page' => 'audience'])
    <div class="row g-3 mb-4">
        @foreach([
            ['Visitors',$summary['visitors'],'bi-people'],['New',$summary['new_visitors'],'bi-person-plus'],['Returning',$summary['returning_visitors'],'bi-arrow-repeat'],['Return rate',number_format($summary['returning_rate'],1,',','.').' %','bi-percent'],['Sessions',$summary['sessions'],'bi-window'],['Identified users',$summary['identified_users'],'bi-person-check']
        ] as [$label,$value,$icon])
            <div class="col-6 col-xl-2"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="d-flex justify-content-between"><span class="small text-body-secondary">{{ $label }}</span><i class="bi {{ $icon }} text-primary"></i></div><div class="h4 mt-2 mb-0">{{ is_numeric($value) ? number_format($value,0,',','.') : $value }}</div></div></div></div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        @foreach([['Devices',$devices],['Browsers',$browsers],['Operating systems',$operating_systems]] as [$title,$rows])
            <div class="col-lg-4"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">{{ $title }}</div><div class="card-body">@forelse($rows as $row)<div class="mb-3"><div class="d-flex justify-content-between small mb-1"><span>{{ ucfirst(str_replace('_',' ',$row->label)) }}</span><strong>{{ $row->total }} · {{ number_format($row->percentage,1,',','.') }}%</strong></div><div class="progress" role="progressbar" aria-valuenow="{{ $row->percentage }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width: {{ $row->percentage }}%"></div></div></div>@empty<p class="text-body-secondary mb-0">No data for this period.</p>@endforelse</div></div></div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-7"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">Daily trend</div><div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Date</th><th class="text-end">Visitors</th><th class="text-end">New</th><th class="text-end">Sessions</th></tr></thead><tbody>@foreach($daily as $row)<tr><td>{{ \Carbon\CarbonImmutable::parse($row->day)->format('M j, Y') }}</td><td class="text-end">{{ $row->visitors }}</td><td class="text-end">{{ $row->new_visitors }}</td><td class="text-end">{{ $row->sessions }}</td></tr>@endforeach</tbody></table></div></div></div>
        <div class="col-xl-5"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">U.S. regions</div><div class="card-body">@php($maxRegion=max(1,(int)$us_regions->max('total')))@foreach($us_regions as $row)<div class="row align-items-center g-2 mb-3"><div class="col-4 small">{{ $row->label }}</div><div class="col"><div class="progress"><div class="progress-bar" style="width: {{ round($row->total/$maxRegion*100,1) }}%"></div></div></div><div class="col-auto fw-semibold">{{ $row->total }}</div></div>@endforeach<p class="small text-body-secondary mb-0">U.S. census-style regions are derived from two-letter state or district codes when available.</p></div></div></div>
    </div>

    <div class="row g-4 mb-4">
        @foreach([['Countries',$countries,'label'],['States / regions',$regions,'label'],['Cities',$cities,'label']] as [$title,$rows,$field])
            <div class="col-lg-4"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">{{ $title }}</div><div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Location</th><th class="text-end">Sessions</th><th class="text-end">Visitors</th></tr></thead><tbody>@forelse($rows->take(12) as $row)<tr><td>{{ strtoupper($row->$field)==='UNKNOWN' ? 'Unknown' : $row->$field }}</td><td class="text-end">{{ $row->total }}</td><td class="text-end">{{ $row->visitors }}</td></tr>@empty<tr><td colspan="3" class="text-body-secondary">No data.</td></tr>@endforelse</tbody></table></div></div></div>
        @endforeach
    </div>

    <div class="row g-4">
        @foreach([['Languages',$languages],['Time zones',$timezones],['Screen resolutions',$resolutions]] as [$title,$rows])
            <div class="col-lg-4"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">{{ $title }}</div><ul class="list-group list-group-flush">@forelse($rows as $row)<li class="list-group-item d-flex justify-content-between gap-3"><span class="text-truncate">{{ $row->label === 'unknown' ? 'Unknown' : $row->label }}</span><strong>{{ $row->total }}</strong></li>@empty<li class="list-group-item text-body-secondary">No data.</li>@endforelse</ul></div></div>
        @endforeach
    </div>
</div>
@endsection
