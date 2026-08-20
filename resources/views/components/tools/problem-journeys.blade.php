@props([
    'journeys',
    'placement' => 'home',
    'title' => 'O que você quer resolver?',
    'description' => 'Escolha uma rotina e comece pela ferramenta indicada. Depois, siga pelos próximos passos quando fizer sentido para o seu caso.',
])

@if($journeys->isNotEmpty())
    <section {{ $attributes->class(['prazzu-problem-journeys']) }} aria-labelledby="problem-journeys-{{ $placement }}-title">
        <div class="prazzu-problem-journeys__header">
            <div>
                <span class="prazzu-eyebrow">Por onde começar</span>
                <h2 id="problem-journeys-{{ $placement }}-title" class="prazzu-section-title mb-1">{{ $title }}</h2>
                <p class="prazzu-section-caption mb-0">{{ $description }}</p>
            </div>
        </div>

        <div class="row g-3">
            @foreach($journeys as $journey)
                <div class="col-12 col-md-6 col-xl-3">
                    <article class="prazzu-problem-journey h-100">
                        <div class="prazzu-problem-journey__heading">
                            <span class="prazzu-icon-tile prazzu-icon-tile--purple">
                                <i class="bi {{ $journey['icon'] }}" aria-hidden="true"></i>
                            </span>
                            <div>
                                <h3>{{ $journey['title'] }}</h3>
                                <p>{{ $journey['description'] }}</p>
                            </div>
                        </div>

                        <ol class="prazzu-problem-journey__steps">
                            @foreach($journey['steps'] as $step)
                                <li @class(['is-start' => $loop->first])>
                                    <span class="prazzu-problem-journey__step-number">{{ $loop->iteration }}</span>
                                    <span>{{ $step['name'] }}</span>
                                </li>
                            @endforeach
                        </ol>

                        <a
                            class="prazzu-problem-journey__action text-decoration-none"
                            href="{{ url()->query(route($journey['start_route_name']), [
                                'source' => 'problem_journey',
                                'journey' => $journey['key'],
                                'placement' => $placement,
                                'position' => $journey['position'],
                            ]) }}"
                        >
                            Começar por {{ $journey['start_name'] }}
                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                    </article>
                </div>
            @endforeach
        </div>
    </section>
@endif
