<h2>Resumo do cálculo</h2>
<table><tbody>@foreach(($result['summary'] ?? []) as $item)<tr><th>{{ $item['label'] ?? '' }}</th><td>{{ $item['value'] ?? '—' }}</td></tr>@endforeach</tbody></table>
<h2>Premissas informadas</h2>
<table><tbody>@foreach($input as $key => $value)@continue($key === 'confirm_assumptions')<tr><th>{{ str($key)->replace('_', ' ')->title() }}</th><td>{{ is_scalar($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE) }}</td></tr>@endforeach</tbody></table>
<p><strong>Aviso:</strong> simulação orientativa. Confirme o enquadramento e a escrituração com o profissional responsável.</p>
