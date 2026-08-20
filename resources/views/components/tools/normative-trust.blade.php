@props([
    'rules' => [],
    'assumptions' => [],
    'isEstimate' => false,
])

@php($trust = app(\App\Core\Normative\Application\NormativeTrustContent::class)->for((array) $rules, (array) $assumptions, (bool) $isEstimate))

@if ($trust !== null)
    <section {{ $attributes->class(['prazzu-normative-trust mt-4']) }} aria-labelledby="normative-trust-title" data-normative-trust data-testid="normative-trust">
        <div class="prazzu-normative-trust__header">
            <div>
                <span class="prazzu-eyebrow">Confiança do resultado</span>
                <h3 class="h5 mb-1" id="normative-trust-title">Base normativa e fontes oficiais</h3>
                <p class="small text-body-secondary mb-0">Confira a referência, a vigência e quando cada regra usada neste resultado foi verificada.</p>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-start">
                @if ($trust['is_estimate'])
                    <span class="badge text-bg-warning">Estimativa</span>
                @endif
                <span class="badge text-bg-light border">{{ count($trust['rules']) }} {{ count($trust['rules']) === 1 ? 'regra versionada' : 'regras versionadas' }}</span>
                <span class="badge text-bg-light border">{{ $trust['source_count'] }} {{ $trust['source_count'] === 1 ? 'fonte oficial' : 'fontes oficiais' }}</span>
            </div>
        </div>

        <div class="vstack gap-2 mt-3">
            @foreach ($trust['rules'] as $rule)
                <details class="prazzu-normative-trust__rule" @if($loop->first) open @endif>
                    <summary>
                        <span>
                            <strong>Regra {{ $loop->iteration }}</strong>
                            @if ($rule['version'])<span class="text-body-secondary"> · versão {{ $rule['version'] }}</span>@endif
                        </span>
                        <i class="bi bi-chevron-down" aria-hidden="true"></i>
                    </summary>
                    <div class="prazzu-normative-trust__body">
                        <dl class="row g-2 small mb-3">
                            @if ($rule['reference_date'])
                                <div class="col-12 col-md-4"><dt>Referência do cálculo</dt><dd class="mb-0">{{ $rule['reference_date'] }}</dd></div>
                            @endif
                            <div class="col-12 col-md-4"><dt>Vigência</dt><dd class="mb-0">
                                @if ($rule['effective_from'] && $rule['effective_until'])
                                    {{ $rule['effective_from'] }} a {{ $rule['effective_until'] }}
                                @elseif ($rule['effective_from'])
                                    desde {{ $rule['effective_from'] }}
                                @else
                                    informada pela fonte oficial
                                @endif
                            </dd></div>
                            @if ($rule['verified_at'])
                                <div class="col-12 col-md-4"><dt>Última verificação registrada</dt><dd class="mb-0">{{ $rule['verified_at'] }}</dd></div>
                            @endif
                        </dl>

                        <div class="small fw-semibold mb-2">Fontes utilizadas</div>
                        <ul class="small mb-0 ps-3">
                            @foreach ($rule['references'] as $reference)
                                <li class="mb-2">
                                    <a href="{{ $reference['official_url'] }}" target="_blank" rel="noopener noreferrer">{{ $reference['title'] }}</a>
                                    @if ($reference['identifier'])<span class="text-body-secondary"> — {{ $reference['identifier'] }}</span>@endif
                                    @if ($reference['article'])<span class="text-body-secondary"> · {{ $reference['article'] }}</span>@endif
                                </li>
                            @endforeach
                        </ul>

                        @if ($rule['verified_by'])
                            <div class="small text-body-secondary mt-2">Verificação registrada por {{ $rule['verified_by'] }}.</div>
                        @endif
                        @if ($rule['identifier'])
                            <div class="small text-body-tertiary mt-1">Identificador técnico: <code>{{ $rule['identifier'] }}</code></div>
                        @endif
                    </div>
                </details>
            @endforeach
        </div>

        @if ($trust['assumptions'] !== [])
            <details class="prazzu-normative-trust__scope mt-2">
                <summary>
                    <span><strong>Premissas e limites deste resultado</strong> <span class="text-body-secondary">({{ count($trust['assumptions']) }})</span></span>
                    <i class="bi bi-chevron-down" aria-hidden="true"></i>
                </summary>
                <div class="prazzu-normative-trust__body">
                    <ul class="small mb-0 ps-3">
                        @foreach ($trust['assumptions'] as $assumption)
                            <li class="mb-2">{{ $assumption }}</li>
                        @endforeach
                    </ul>
                </div>
            </details>
        @endif
    </section>
@endif
