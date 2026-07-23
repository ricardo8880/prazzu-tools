@extends('layouts.app')

@section('title', 'Sobre o Prazzu Tools — Plataforma de ferramentas contábeis')
@section('meta_description', 'Conheça o Prazzu Tools, uma plataforma criada para reunir ferramentas contábeis confiáveis, gratuitas e preparadas para evoluir com a rotina dos profissionais da contabilidade.')

@section('content')
    <div class="prazzu-page prazzu-about">
        <section class="text-center py-4 py-lg-5 mb-5" aria-labelledby="about-title">
            <span class="badge rounded-pill prazzu-about__badge mb-3">
                <i class="bi bi-info-circle me-1" aria-hidden="true"></i>Sobre o Prazzu Tools
            </span>

            <h1 id="about-title" class="display-5 fw-bold mb-3">
                Ferramentas contábeis que resolvem de verdade
            </h1>

            <p class="lead text-body-secondary mx-auto mb-4" style="max-width: 840px;">
                O Prazzu Tools nasceu para reunir, em uma única plataforma, soluções confiáveis para a rotina de
                contadores, escritórios contábeis e profissionais que precisam tomar decisões com mais segurança e agilidade.
            </p>

            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a class="btn btn-primary btn-lg" href="{{ route('tools.index') }}">
                    <i class="bi bi-grid-3x3-gap me-2" aria-hidden="true"></i>Explorar ferramentas
                </a>
                <a class="btn btn-outline-primary btn-lg" href="{{ route('plans') }}">
                    Conhecer o Prazzu Plus
                </a>
            </div>
        </section>

        <section class="mb-5" aria-labelledby="problem-title">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-lg-6">
                    <span class="prazzu-eyebrow">O problema que queremos resolver</span>
                    <h2 id="problem-title" class="h1 fw-bold mb-3">A rotina contábil não deveria depender de soluções espalhadas</h2>
                    <p class="text-body-secondary fs-5">
                        Cálculos, conferências, simulações e decisões fazem parte do trabalho diário de quem atua com
                        contabilidade. Muitas vezes, essas tarefas dependem de planilhas antigas, sistemas caros ou
                        ferramentas incompletas encontradas em diferentes lugares.
                    </p>
                    <p class="text-body-secondary mb-0">
                        O Prazzu Tools foi criado para concentrar essas soluções em um ambiente simples, organizado e
                        preparado para crescer sem perder qualidade. A plataforma resolve tarefas pontuais; gestão de
                        clientes, processos e operações contínuas pertence ao Prazzu Core ou a outro produto apropriado.
                    </p>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card border-0 bg-body-tertiary shadow-sm prazzu-about__feature-panel">
                        <div class="card-body p-4 p-lg-5">
                            <div class="row g-4">
                                <div class="col-6">
                                    <i class="bi bi-calculator fs-2 text-primary" aria-hidden="true"></i>
                                    <h3 class="h5 mt-3">Cálculos</h3>
                                    <p class="small text-body-secondary mb-0">Resultados claros e memória de cálculo quando aplicável.</p>
                                </div>
                                <div class="col-6">
                                    <i class="bi bi-sliders fs-2 text-primary" aria-hidden="true"></i>
                                    <h3 class="h5 mt-3">Simulações</h3>
                                    <p class="small text-body-secondary mb-0">Cenários que ajudam o profissional a decidir melhor.</p>
                                </div>
                                <div class="col-6">
                                    <i class="bi bi-journal-check fs-2 text-primary" aria-hidden="true"></i>
                                    <h3 class="h5 mt-3">Conteúdo</h3>
                                    <p class="small text-body-secondary mb-0">Informação contábil útil conectada às ferramentas.</p>
                                </div>
                                <div class="col-6">
                                    <i class="bi bi-graph-up-arrow fs-2 text-primary" aria-hidden="true"></i>
                                    <h3 class="h5 mt-3">Produtividade</h3>
                                    <p class="small text-body-secondary mb-0">Recursos avançados para quem usa a plataforma com frequência.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-5 py-4 py-lg-5 border-top border-bottom" aria-labelledby="platform-title">
            <div class="text-center mb-5">
                <span class="prazzu-eyebrow">Uma plataforma, não apenas calculadoras</span>
                <h2 id="platform-title" class="h1 fw-bold mb-3">Cada ferramenta evolui sozinha. A plataforma evolui para todas.</h2>
                <p class="text-body-secondary mx-auto mb-0" style="max-width: 760px;">
                    As ferramentas são independentes para que uma possa crescer sem quebrar outra. Ao mesmo tempo,
                    recursos universais como planos, usuários, analytics e histórico pertencem à plataforma e podem
                    beneficiar todo o ecossistema.
                </p>
            </div>

            <div class="row g-3 justify-content-center text-center">
                <div class="col-12 col-md-5 col-xl-3">
                    <div class="card h-100 shadow-sm prazzu-about__card">
                        <div class="card-body p-4">
                            <i class="bi bi-boxes fs-1 text-primary" aria-hidden="true"></i>
                            <h3 class="h5 mt-3">Plataforma compartilhada</h3>
                            <p class="text-body-secondary mb-0">Usuários, planos, analytics e serviços comuns em um único Core.</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-auto d-none d-md-flex align-items-center">
                    <i class="bi bi-arrow-right fs-3 text-body-secondary" aria-hidden="true"></i>
                </div>

                <div class="col-12 col-md-5 col-xl-3">
                    <div class="card h-100 shadow-sm prazzu-about__card">
                        <div class="card-body p-4">
                            <i class="bi bi-grid-3x3-gap fs-1 text-primary" aria-hidden="true"></i>
                            <h3 class="h5 mt-3">Ferramentas independentes</h3>
                            <p class="text-body-secondary mb-0">Cada módulo concentra apenas as regras específicas do seu domínio.</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-auto d-none d-xl-flex align-items-center">
                    <i class="bi bi-arrow-right fs-3 text-body-secondary" aria-hidden="true"></i>
                </div>

                <div class="col-12 col-md-5 col-xl-3">
                    <div class="card h-100 shadow-sm border-primary prazzu-about__card prazzu-about__card--featured">
                        <div class="card-body p-4">
                            <i class="bi bi-gem fs-1 text-primary" aria-hidden="true"></i>
                            <h3 class="h5 mt-3">Um único plano</h3>
                            <p class="text-body-secondary mb-0">Uma assinatura libera os recursos Plus de toda a plataforma.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-5" aria-labelledby="philosophy-title">
            <div class="row g-4 align-items-stretch">
                <div class="col-12 col-lg-5">
                    <div class="card h-100 shadow-sm prazzu-about__card prazzu-about__card--essential">
                        <div class="card-body p-4 p-lg-5">
                            <span class="badge prazzu-about__badge prazzu-about__badge--soft mb-3">Boa · Gratuita</span>
                            <h2 id="philosophy-title" class="h2 fw-bold">A versão gratuita resolve o problema</h2>
                            <p class="text-body-secondary">
                                Uma ferramenta gratuita do Prazzu não deve entregar um cálculo incompleto nem criar
                                limitações artificiais para forçar uma assinatura.
                            </p>
                            <ul class="list-unstyled d-grid gap-3 mb-0">
                                <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-primary" aria-hidden="true"></i><span>Resultado completo e confiável</span></li>
                                <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-primary" aria-hidden="true"></i><span>Uso ilimitado da experiência gratuita</span></li>
                                <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-primary" aria-hidden="true"></i><span>Transparência para entender o cálculo</span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card h-100 shadow-sm prazzu-about__card prazzu-about__card--plus">
                        <div class="card-body p-4 p-lg-5">
                            <span class="badge prazzu-about__badge mb-3">Excelente · Prazzu Plus</span>
                            <h2 class="h2 fw-bold">A versão Plus transforma a forma de trabalhar</h2>
                            <p class="text-body-secondary">
                                O Plus acrescenta produtividade, histórico, comparações, projeções, alertas e
                                automações. O usuário não paga para conseguir calcular corretamente; paga para trabalhar melhor.
                            </p>
                            <div class="alert prazzu-about__note mb-0" role="note">
                                <i class="bi bi-stars me-2" aria-hidden="true"></i>
                                Durante o lançamento, os recursos públicos Essenciais e Plus estão liberados gratuitamente. A futura assinatura manterá o Essencial completo e financiará os ganhos de produtividade do Plus.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-5" aria-labelledby="commitments-title">
            <div class="text-center mb-4">
                <span class="prazzu-eyebrow">Nosso compromisso</span>
                <h2 id="commitments-title" class="h1 fw-bold">Confiança antes de qualquer coisa</h2>
            </div>

            <div class="row g-3">
                @foreach ([
                    ['icon' => 'bi-bullseye', 'title' => 'Precisão', 'text' => 'Resultados consistentes e regras de cálculo tratadas com responsabilidade.'],
                    ['icon' => 'bi-eye', 'title' => 'Transparência', 'text' => 'O usuário deve compreender de onde vem o resultado apresentado.'],
                    ['icon' => 'bi-lightning-charge', 'title' => 'Facilidade', 'text' => 'Interfaces diretas, responsivas e focadas na tarefa que precisa ser resolvida.'],
                    ['icon' => 'bi-arrow-repeat', 'title' => 'Evolução', 'text' => 'Ferramentas preparadas para receber melhorias sem comprometer as demais.'],
                    ['icon' => 'bi-shield-check', 'title' => 'Confiabilidade', 'text' => 'A confiança do profissional é o principal ativo da plataforma.'],
                    ['icon' => 'bi-people', 'title' => 'Foco contábil', 'text' => 'Decisões orientadas pela rotina real de quem trabalha com contabilidade.'],
                ] as $commitment)
                    <div class="col-12 col-md-6 col-xl-4">
                        <article class="card h-100 shadow-sm prazzu-about__card">
                            <div class="card-body p-4">
                                <i class="bi {{ $commitment['icon'] }} fs-2 text-primary" aria-hidden="true"></i>
                                <h3 class="h5 mt-3">{{ $commitment['title'] }}</h3>
                                <p class="text-body-secondary mb-0">{{ $commitment['text'] }}</p>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mb-5" aria-labelledby="audience-title">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-lg-5">
                    <span class="prazzu-eyebrow">Para quem foi criada</span>
                    <h2 id="audience-title" class="h1 fw-bold mb-3">Soluções pontuais para a rotina contábil</h2>
                    <p class="text-body-secondary mb-0">
                        O Prazzu Tools atende diferentes perfis, mantendo a mesma preocupação com clareza, qualidade e praticidade.
                    </p>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="row g-3">
                        @foreach ([
                            ['icon' => 'bi-briefcase', 'label' => 'Escritórios contábeis'],
                            ['icon' => 'bi-person-workspace', 'label' => 'Contadores autônomos'],
                            ['icon' => 'bi-building', 'label' => 'Departamentos financeiros'],
                            ['icon' => 'bi-mortarboard', 'label' => 'Estudantes da área'],
                            ['icon' => 'bi-person-lines-fill', 'label' => 'Consultores'],
                            ['icon' => 'bi-shop', 'label' => 'Empresários'],
                        ] as $audience)
                            <div class="col-12 col-sm-6">
                                <div class="d-flex align-items-center gap-3 border rounded-3 p-3 h-100 prazzu-about__audience-item">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle prazzu-about__audience-icon flex-shrink-0" style="width: 44px; height: 44px;">
                                        <i class="bi {{ $audience['icon'] }}" aria-hidden="true"></i>
                                    </span>
                                    <strong>{{ $audience['label'] }}</strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="card border-0 text-white shadow prazzu-about__cta" aria-labelledby="future-title">
            <div class="card-body p-4 p-lg-5 text-center">
                <i class="bi bi-rocket-takeoff fs-1" aria-hidden="true"></i>
                <span class="d-block text-uppercase small fw-semibold mt-3 mb-2">O futuro da plataforma</span>
                <h2 id="future-title" class="display-6 fw-bold mb-3">Construída para evoluir junto com a contabilidade</h2>
                <p class="lead mx-auto mb-4" style="max-width: 800px;">
                    Novas ferramentas, conteúdos e recursos serão adicionados continuamente. Nosso objetivo é transformar
                    o Prazzu Tools na plataforma de referência para quem trabalha com contabilidade.
                </p>
                <a class="btn btn-light btn-lg text-primary fw-semibold" href="{{ route('tools.index') }}">
                    Conhecer as ferramentas
                </a>
            </div>
        </section>
    </div>
@endsection
