@props(['item', 'relatedItems'])

<section class="prazzu-resource-journey" aria-labelledby="resource-next-steps">
    <div class="prazzu-resource-journey__intro">
        <span class="prazzu-eyebrow">Jornada recomendada</span>
        <h2 id="resource-next-steps">Aprenda, organize e calcule</h2>
        <p>Cada etapa tem uma função diferente. Use somente o que fizer sentido para o seu momento.</p>
    </div>

    <div class="prazzu-resource-journey__steps">
        @foreach ($relatedItems as $related)
            <a href="{{ route($related['route'], ['resource' => $related['type'], 'slug' => $related['slug']]) }}">
                <i class="bi {{ $related['icon'] }}" aria-hidden="true"></i>
                <span><small>{{ config('resources.sections.'.$related['type'].'.singular') }}</small><strong>{{ $related['title'] }}</strong></span>
                <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
        @endforeach

        <a href="{{ route($item['tool']['route']) }}">
            <i class="bi bi-calculator" aria-hidden="true"></i>
            <span><small>Ferramenta</small><strong>{{ $item['tool']['name'] }}</strong></span>
            <i class="bi bi-arrow-right" aria-hidden="true"></i>
        </a>
    </div>
</section>
