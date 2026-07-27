@if($company)
    <h2>Identificação</h2>
    <div class="print-grid">
        <div class="print-card">
            <span>Empresa</span>
            <strong>{{ $company['legal_name'] ?: $company['name'] }}</strong>
        </div>
        @if($company['document'])
            <div class="print-card">
                <span>Documento</span>
                <strong>{{ $company['document'] }}</strong>
            </div>
        @endif
        @if($company['accountant_name'] || $company['accountant_registration'])
            <div class="print-card">
                <span>Responsável contábil</span>
                <strong>{{ trim(($company['accountant_name'] ?? '').' '.($company['accountant_registration'] ?? '')) }}</strong>
            </div>
        @endif
    </div>
@endif

<h2>Premissas consideradas</h2>
<div class="print-grid">
    <div class="print-card">
        <span>Funcionário</span>
        <strong>{{ filled($input['employee_name'] ?? null) ? $input['employee_name'] : 'Não informado' }}</strong>
    </div>
    <div class="print-card">
        <span>Departamento</span>
        <strong>{{ filled($input['department'] ?? null) ? $input['department'] : 'Não informado' }}</strong>
    </div>
    <div class="print-card">
        <span>Jornada mensal</span>
        <strong>{{ $input['monthly_hours'] }} horas</strong>
    </div>
</div>

<h2>Resumo do custo</h2>
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

<h2>Memória de cálculo</h2>
<table>
    <tbody>
    @foreach($result['calculation_memory']['steps'] ?? [] as $step)
        <tr>
            <th scope="row">{{ $step['label'] }}<br><small>{{ $step['formula'] }}</small></th>
            <td class="print-right">{{ is_int($step['result']) ? 'R$ '.number_format($step['result'] / 100, 2, ',', '.') : $step['result'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p class="print-warning">
    Estimativa gerencial baseada nas premissas informadas. Revise incidências,
    convenção coletiva, FPAS, RAT/FAP e enquadramento antes de uma decisão.
</p>

<footer class="print-footer">
    Relatório gerado pelo Prazzu Tools. Não substitui a folha de pagamento,
    o eSocial ou a análise do profissional responsável.
</footer>
