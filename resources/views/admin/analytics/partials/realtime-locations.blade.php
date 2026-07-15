@forelse($locations as $row)
@php($location = collect([$row->city, $row->region, $row->country])->filter(fn($value) => $value && $value !== 'unknown')->implode(' · '))
<tr><td>{{ $location ?: 'Não identificado' }}</td><td class="text-end fw-semibold">{{ number_format($row->total, 0, ',', '.') }}</td></tr>
@empty
<tr><td colspan="2" class="text-body-secondary">Sem localização disponível.</td></tr>
@endforelse
