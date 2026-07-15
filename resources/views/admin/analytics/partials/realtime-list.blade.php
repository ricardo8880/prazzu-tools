@forelse($rows as $row)
<li class="list-group-item d-flex justify-content-between gap-3"><span class="text-truncate" title="{{ $row->label }}">{{ $row->label ?: 'Não identificado' }}</span><span class="badge text-bg-light border">{{ number_format($row->total, 0, ',', '.') }}</span></li>
@empty
<li class="list-group-item text-body-secondary">Sem atividade agora.</li>
@endforelse
