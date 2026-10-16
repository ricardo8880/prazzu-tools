<h2>Summary</h2><table>@foreach(($result['summary'] ?? []) as $item)<tr><th>{{ $item['label'] ?? '' }}</th><td>{{ $item['value'] ?? '' }}</td></tr>@endforeach</table>
<h2>Inputs</h2><table>@foreach($input as $key=>$value)<tr><th>{{ str_replace('_',' ',ucwords($key, '_')) }}</th><td>{{ is_bool($value) ? ($value ? 'Yes':'No') : $value }}</td></tr>@endforeach</table>
