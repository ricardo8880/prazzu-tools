@if($company)
    <h2>Identificação</h2>
    <p>
        <strong>{{ $company['legal_name'] ?: $company['name'] }}</strong>
        @if($company['document']) — {{ $company['document'] }} @endif
    </p>
@endif

<h2>Consolidado</h2>
<div class="print-grid">
    <div class="print-card"><span>Funcionários</span><strong>{{ $batch['employee_count'] }}</strong></div>
    <div class="print-card"><span>Custo mensal</span><strong>{{ $batch['monthly_total'] }}</strong></div>
    <div class="print-card"><span>Custo anual</span><strong>{{ $batch['annual_total'] }}</strong></div>
</div>

<h2>Funcionários</h2>
<table>
    <thead>
    <tr>
        <th>Funcionário</th>
        <th>Departamento</th>
        <th>Cargo</th>
        <th class="print-right">Mensal</th>
        <th class="print-right">Anual</th>
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

<h2>Consolidado por departamento</h2>
<table>
    <thead>
    <tr>
        <th>Departamento</th>
        <th class="print-right">Mensal</th>
        <th class="print-right">Anual</th>
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

<h2>Projeção-base de 12 meses</h2>
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
    Estimativa gerencial. Confirme incidências e particularidades de cada
    vínculo antes de utilizar o consolidado em decisões trabalhistas.
</p>
