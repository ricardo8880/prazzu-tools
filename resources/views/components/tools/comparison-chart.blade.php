@props([
    'title',
    'series' => [],
    'unit' => '',
    'threshold' => null,
    'thresholdLabel' => null,
])

@php
    $numericValues = array_map(static fn (array $item): int => max(0, (int) ($item['value'] ?? 0)), $series);
    $maximum = max(1, ...$numericValues, $threshold === null ? 0 : (int) $threshold);
@endphp

<figure {{ $attributes->class(['card border-0 bg-body-tertiary']) }}>
    <div class="card-body">
        <figcaption class="h5">{{ $title }}</figcaption>
        @if ($threshold !== null)
            <p class="small text-body-secondary">
                <span class="d-inline-block bg-warning me-1" style="width: 1rem; height: .2rem;" aria-hidden="true"></span>
                {{ $thresholdLabel ?? 'Referência' }}: {{ $threshold }}{{ $unit }}
            </p>
        @endif

        <div class="d-grid gap-3" role="img" aria-label="{{ $title }}">
            @foreach ($series as $item)
                @php
                    $value = max(0, (int) ($item['value'] ?? 0));
                    $width = min(100, intdiv($value * 100, $maximum));
                    $thresholdWidth = $threshold === null ? null : min(100, intdiv(((int) $threshold) * 100, $maximum));
                @endphp
                <div>
                    <div class="d-flex justify-content-between gap-3 small mb-1">
                        <span>{{ $item['label'] }}</span>
                        <strong>{{ $item['display'] ?? ($value.$unit) }}</strong>
                    </div>
                    <div class="progress position-relative" style="height: 1rem;" aria-hidden="true">
                        <div class="progress-bar {{ $item['class'] ?? 'bg-primary' }}" style="width: {{ $width }}%"></div>
                        @if ($thresholdWidth !== null)
                            <span class="position-absolute top-0 bottom-0 border-start border-2 border-warning" style="left: {{ $thresholdWidth }}%"></span>
                        @endif
                    </div>
                    @if (!empty($item['description']))
                        <span class="visually-hidden">{{ $item['description'] }}</span>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-sm mb-0">
                <thead><tr><th>Item</th><th class="text-end">Valor</th></tr></thead>
                <tbody>
                    @foreach ($series as $item)
                        <tr><td>{{ $item['label'] }}</td><td class="text-end">{{ $item['display'] ?? (((int) ($item['value'] ?? 0)).$unit) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</figure>
