<h2>Resumo</h2><table>@foreach(($result['summary'] ?? []) as $item)<tr><th>{{ $item['label'] ?? '' }}</th><td>{{ $item['value'] ?? '' }}</td></tr>@endforeach</table>
<h2>Dados informados</h2><table>@foreach($input as $key=>$value)<tr><th>{{ str_replace('_',' ',ucfirst($key)) }}</th><td>{{ is_bool($value) ? ($value ? 'Sim':'Não') : $value }}</td></tr>@endforeach</table>
