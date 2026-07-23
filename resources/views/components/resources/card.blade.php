@props(['item', 'section'])

<article class="prazzu-resource-card h-100">
    <div class="prazzu-resource-card__header">
        <span class="prazzu-icon-tile prazzu-icon-tile--violet">
            <i class="bi {{ $item['icon'] }}" aria-hidden="true"></i>
        </span>
        <span class="prazzu-resource-status prazzu-resource-status--{{ $item['status'] }}">
            {{ $item['status_label'] }}
        </span>
    </div>

    <span class="prazzu-resource-card__category">{{ $item['category'] }}</span>
    <h2>{{ $item['title'] }}</h2>
    <p>{{ $item['summary'] }}</p>

    <div class="prazzu-resource-card__meta">
        <span><i class="bi bi-bookmark" aria-hidden="true"></i>{{ $section['singular'] }}</span>
        @if (isset($item['reading_time']))
            <span><i class="bi bi-clock" aria-hidden="true"></i>{{ $item['reading_time'] }}</span>
        @endif
        @if (isset($item['format']))
            <span><i class="bi bi-file-earmark" aria-hidden="true"></i>{{ $item['format'] }}</span>
        @endif
    </div>

    <div class="prazzu-resource-card__footer">
        @if ($item['status'] === 'published' && isset($item['route']))
            <a class="btn btn-primary prazzu-resource-action btn-sm prazzu-resource-card__primary-link" href="{{ route($item['route'], ['resource' => $item['type'], 'slug' => $item['slug']]) }}">
                Acessar {{ mb_strtolower($section['singular']) }} <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
        @else
            <span class="small text-body-secondary">Relacionado a</span>
            <a class="btn btn-outline-primary btn-sm mt-1" href="{{ route($item['tool']['route']) }}">{{ $item['tool']['name'] }}</a>
        @endif
    </div>
</article>
