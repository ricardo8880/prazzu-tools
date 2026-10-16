@if($company)
    <h2>Identification</h2>
    <div class="print-grid">
        <div class="print-card">
            <span>Company</span>
            <strong>{{ $company['legal_name'] ?: $company['name'] }}</strong>
        </div>
        @if($company['document'])
            <div class="print-card">
                <span>ID / Tax ID</span>
                <strong>{{ $company['document'] }}</strong>
            </div>
        @endif
        @if($company['accountant_name'] || $company['accountant_registration'])
            <div class="print-card">
                <span>Accounting contact</span>
                <strong>{{ trim(($company['accountant_name'] ?? '').' '.($company['accountant_registration'] ?? '')) }}</strong>
            </div>
        @endif
    </div>
@endif

<h2>Assumptions used</h2>
<div class="print-grid">
    <div class="print-card">
        <span>Employee</span>
        <strong>{{ filled($input['employee_name'] ?? null) ? $input['employee_name'] : 'Not provided' }}</strong>
    </div>
    <div class="print-card">
        <span>Department</span>
        <strong>{{ filled($input['department'] ?? null) ? $input['department'] : 'Not provided' }}</strong>
    </div>
    <div class="print-card">
        <span>Monthly hours</span>
        <strong>{{ $input['monthly_hours'] }} hours</strong>
    </div>
</div>

<h2>Cost summary</h2>
<div class="print-grid">
    @foreach($result['summary'] ?? [] as $item)
        <div class="print-card">
            <span>{{ $item['label'] }}</span>
            <strong>{{ $item['value'] }}</strong>
            @if($item['description'] ?? null)
                <small>{{ $item['description'] }}</small>
            @endif
        </div>
    @endforeach
</div>

<h2>Calculation details</h2>
<table>
    <tbody>
    @foreach($result['calculation_memory']['steps'] ?? [] as $step)
        <tr>
            <th scope="row">{{ $step['label'] }}<br><small>{{ $step['formula'] }}</small></th>
            <td class="print-right">{{ is_int($step['result']) ? '$ '.number_format($step['result'] / 100, 2, '.', ',') : $step['result'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p class="print-warning">
    Management estimate based on the assumptions provided. Review applicable federal, state, and local requirements,
    state unemployment rules, workers’ compensation, benefits, and worker classification before making a decision.
</p>

<footer class="print-footer">
    This estimate does not replace payroll processing or review by a qualified professional.
</footer>
