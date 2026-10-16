@if($company)
    <h2>Identification</h2>
    <p>
        <strong>{{ $company['legal_name'] ?: $company['name'] }}</strong>
        @if($company['document']) — {{ $company['document'] }} @endif
    </p>
@endif

<h2>Summary</h2>
<div class="print-grid">
    <div class="print-card"><span>Employees</span><strong>{{ $batch['employee_count'] }}</strong></div>
    <div class="print-card"><span>Monthly cost</span><strong>{{ $batch['monthly_total'] }}</strong></div>
    <div class="print-card"><span>Annual cost</span><strong>{{ $batch['annual_total'] }}</strong></div>
</div>

<h2>Employees</h2>
<table>
    <thead>
    <tr>
        <th>Employee</th>
        <th>Department</th>
        <th>Role</th>
        <th class="print-right">Monthly</th>
        <th class="print-right">Annual</th>
    </tr>
    </thead>
    <tbody>
    @foreach($batch['employees'] as $employee)
        <tr>
            <td>{{ $employee['name'] }}</td>
            <td>{{ $employee['department'] }}</td>
            <td>{{ $employee['role'] ?: '—' }}</td>
            <td class="print-right">{{ $employee['monthly_cost'] }}</td>
            <td class="print-right">{{ $employee['annual_cost'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>Summary by department</h2>
<table>
    <thead>
    <tr>
        <th>Department</th>
        <th class="print-right">Monthly</th>
        <th class="print-right">Annual</th>
    </tr>
    </thead>
    <tbody>
    @foreach($batch['departments'] as $department)
        <tr>
            <td>{{ $department['department'] }}</td>
            <td class="print-right">{{ $department['monthly_cost'] }}</td>
            <td class="print-right">{{ $department['annual_cost'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>12-month baseline projection</h2>
<p>{{ $batch['projection_assumption'] }}</p>
<table>
    <thead>
    <tr>
        @foreach($batch['projection'] as $month)
            <th>{{ $month['competence'] }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    <tr>
        @foreach($batch['projection'] as $month)
            <td>{{ $month['cost'] }}</td>
        @endforeach
    </tr>
    </tbody>
</table>

<p class="print-warning">
    Management estimate. Confirm applicable taxes, benefits, and worker-specific requirements
    before using this summary for employment decisions.
</p>
