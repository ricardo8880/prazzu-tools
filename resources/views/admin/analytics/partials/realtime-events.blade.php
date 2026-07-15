@forelse($events as $event)
<tr><td class="text-nowrap">{{ $event->occurred_at?->format('H:i:s') }}</td><td><code>{{ $event->event_name }}</code></td><td><span class="badge text-bg-light border">{{ $event->channel }}</span></td><td class="text-truncate" style="max-width:260px">{{ $event->subject_slug ?: ($event->path ?: '—') }}</td><td>{{ $event->source ?: '—' }}</td></tr>
@empty
<tr><td colspan="5" class="text-center text-body-secondary py-4">Nenhum evento recente.</td></tr>
@endforelse
