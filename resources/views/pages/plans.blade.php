@extends('layouts.app')

@section('title', 'Planos — Prazzu Tools')
@section('meta_description', 'Compare o Prazzu Gratuito, o Prazzu Plus individual e o plano empresarial para distribuir acessos Plus aos colaboradores.')

@section('content')
    <div class="prazzu-page prazzu-plans">
        <section class="text-center mb-5" aria-labelledby="plans-title">
            <span class="badge rounded-pill prazzu-plans__badge mb-3">
                <i class="bi bi-gem me-1" aria-hidden="true"></i>Prazzu Plus
            </span>
            <h1 id="plans-title" class="display-6 fw-bold mb-3">Todas as ferramentas. Uma única assinatura.</h1>
            <p class="lead text-body-secondary mx-auto mb-4" style="max-width: 780px;">
                Durante o lançamento, os recursos Essenciais e Prazzu Plus estão liberados gratuitamente.
                Quando a assinatura for ativada, o Essencial continuará completo e o Plus reunirá históricos,
                volume, simulações, projeções, alertas e automações.
            </p>

            <div class="d-inline-flex flex-wrap justify-content-center gap-2" aria-label="Destaques do Prazzu Plus">
                <span class="badge rounded-pill prazzu-plans__highlight px-3 py-2">
                    <i class="bi bi-infinity me-1 prazzu-plans__accent" aria-hidden="true"></i>Uso ilimitado
                </span>
                <span class="badge rounded-pill prazzu-plans__highlight px-3 py-2">
                    <i class="bi bi-grid-3x3-gap me-1 prazzu-plans__accent" aria-hidden="true"></i>Todas as ferramentas
                </span>
                <span class="badge rounded-pill prazzu-plans__highlight px-3 py-2">
                    <i class="bi bi-stars me-1 prazzu-plans__accent" aria-hidden="true"></i>Novos recursos incluídos
                </span>
            </div>
        </section>

        <section class="mb-5" aria-labelledby="billing-heading">
            <div class="text-center mb-4">
                <span class="prazzu-eyebrow">Escolha seu período</span>
                <h2 id="billing-heading" class="h2 mb-2">O mesmo Prazzu Plus em qualquer opção</h2>
                <p class="text-body-secondary mb-0">Muda apenas o período de cobrança e a economia.</p>
            </div>

            <div class="d-flex justify-content-center mb-4">
                <div class="nav nav-pills rounded-pill p-1 prazzu-plans__billing" id="billing-options" role="tablist" aria-label="Período da assinatura">
                    <button class="nav-link rounded-pill active" id="billing-monthly-tab" data-bs-toggle="pill" data-bs-target="#billing-monthly" type="button" role="tab" aria-controls="billing-monthly" aria-selected="true">
                        Mensal
                    </button>
                    <button class="nav-link rounded-pill" id="billing-quarterly-tab" data-bs-toggle="pill" data-bs-target="#billing-quarterly" type="button" role="tab" aria-controls="billing-quarterly" aria-selected="false">
                        Trimestral
                        <span class="badge prazzu-plans__saving ms-1">Economize</span>
                    </button>
                    <button class="nav-link rounded-pill" id="billing-annual-tab" data-bs-toggle="pill" data-bs-target="#billing-annual" type="button" role="tab" aria-controls="billing-annual" aria-selected="false">
                        Anual
                        <span class="badge prazzu-plans__saving ms-1">Melhor valor</span>
                    </button>
                </div>
            </div>

            <div class="row g-4 justify-content-center align-items-stretch">
                <div class="col-12 col-xl-5">
                    <article class="card h-100 shadow-sm prazzu-plans__card prazzu-plans__card--free">
                        <div class="card-body p-4 p-lg-5 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                                <div>
                                    <span class="badge prazzu-plans__badge prazzu-plans__badge--muted mb-3">Gratuito</span>
                                    <h2 class="h3 fw-bold mb-2">Prazzu Gratuito</h2>
                                    <p class="text-body-secondary mb-0">Tudo o que você precisa para resolver o cálculo com qualidade.</p>
                                </div>
                                <i class="bi bi-shield-check fs-2 prazzu-plans__check" aria-hidden="true"></i>
                            </div>

                            <div class="mb-4">
                                <span class="display-5 fw-bold">R$ 0</span>
                                <span class="text-body-secondary"> para sempre</span>
                            </div>

                            <ul class="list-unstyled d-grid gap-3 mb-4">
                                <li class="d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>Versões gratuitas completas, sem cálculos artificialmente limitados</span></li>
                                <li class="d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>Uso ilimitado de todas as ferramentas gratuitas</span></li>
                                <li class="d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>Resultados transparentes e memória de cálculo quando disponível</span></li>
                                <li class="d-flex gap-2"><i class="bi bi-stars prazzu-plans__spark flex-shrink-0" aria-hidden="true"></i><span>Recursos Plus liberados durante a fase de lançamento</span></li>
                            </ul>

                            <a class="btn prazzu-btn-outline btn-lg w-100 mt-auto" href="{{ route('tools.index') }}">
                                Explorar ferramentas gratuitas
                            </a>
                        </div>
                    </article>
                </div>

                <div class="col-12 col-xl-7">
                    <article class="card h-100 shadow position-relative overflow-hidden prazzu-plans__card prazzu-plans__card--plus">
                        <div class="card-header border-0 p-3 text-center prazzu-plans__plus-header">
                            <strong><i class="bi bi-stars me-1" aria-hidden="true"></i>Uma assinatura libera toda a plataforma Plus</strong>
                        </div>

                        <div class="card-body p-4 p-lg-5 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                                <div>
                                    <span class="badge prazzu-plans__badge mb-3">Prazzu Plus</span>
                                    <h2 class="h3 fw-bold mb-2">Mais produtividade em todas as ferramentas</h2>
                                    <p class="text-body-secondary mb-0">Assine uma vez e use os recursos Plus das ferramentas atuais e futuras.</p>
                                </div>
                                <i class="bi bi-gem fs-2 prazzu-plans__accent" aria-hidden="true"></i>
                            </div>

                            <div class="tab-content mb-4" id="billing-content">
                                <div class="tab-pane fade show active" id="billing-monthly" role="tabpanel" aria-labelledby="billing-monthly-tab" tabindex="0">
                                    <div class="d-flex flex-wrap align-items-end gap-2">
                                        <span class="display-5 fw-bold">R$ 39,90</span>
                                        <span class="text-body-secondary fs-5 mb-2">/mês</span>
                                    </div>
                                    <p class="text-body-secondary mb-0">Cobrança mensal. A opção mais flexível para começar.</p>
                                </div>

                                <div class="tab-pane fade" id="billing-quarterly" role="tabpanel" aria-labelledby="billing-quarterly-tab" tabindex="0">
                                    <div class="d-flex flex-wrap align-items-end gap-2">
                                        <span class="display-5 fw-bold">R$ 109,90</span>
                                        <span class="text-body-secondary fs-5 mb-2">a cada 3 meses</span>
                                    </div>
                                    <p class="mb-1"><strong>R$ 36,63 por mês</strong></p>
                                    <p class="prazzu-plans__check mb-0"><i class="bi bi-piggy-bank me-1" aria-hidden="true"></i>Economia de R$ 9,80 por trimestre.</p>
                                </div>

                                <div class="tab-pane fade" id="billing-annual" role="tabpanel" aria-labelledby="billing-annual-tab" tabindex="0">
                                    <div class="d-flex flex-wrap align-items-end gap-2">
                                        <span class="display-5 fw-bold">R$ 399,90</span>
                                        <span class="text-body-secondary fs-5 mb-2">/ano</span>
                                    </div>
                                    <p class="mb-1"><strong>R$ 33,33 por mês</strong></p>
                                    <p class="prazzu-plans__check mb-0"><i class="bi bi-piggy-bank me-1" aria-hidden="true"></i>Economia de R$ 78,90 por ano.</p>
                                </div>
                            </div>

                            <ul class="list-unstyled row g-3 mb-4">
                                <li class="col-12 col-md-6 d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>Recursos Plus ilimitados</span></li>
                                <li class="col-12 col-md-6 d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>Históricos e salvamentos</span></li>
                                <li class="col-12 col-md-6 d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>Simulações e comparações</span></li>
                                <li class="col-12 col-md-6 d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>Projeções e alertas</span></li>
                                <li class="col-12 col-md-6 d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>Automações e produtividade</span></li>
                                <li class="col-12 col-md-6 d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>Novas ferramentas incluídas</span></li>
                            </ul>

                            <button class="btn prazzu-btn-primary btn-lg w-100 mt-auto" type="button" disabled>
                                <i class="bi bi-lock me-1" aria-hidden="true"></i>Assinaturas em breve
                            </button>
                            <p class="small text-body-secondary text-center mt-2 mb-0">Nenhum pagamento será realizado nesta etapa.</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="mb-5" aria-labelledby="business-plan-heading">
            <article class="card border-info-subtle shadow-sm overflow-hidden">
                <div class="row g-0 align-items-stretch">
                    <div class="col-12 col-lg-7">
                        <div class="card-body p-4 p-lg-5 h-100">
                            <span class="badge text-bg-info mb-3">
                                <i class="bi bi-buildings me-1" aria-hidden="true"></i>Plano empresarial
                            </span>
                            <h2 id="business-plan-heading" class="h2 fw-bold mb-3">Prazzu Plus para sua equipe</h2>
                            <p class="lead text-body-secondary mb-4">
                                A empresa contrata uma quantidade de acessos Plus e distribui as vagas entre seus colaboradores.
                                Cada pessoa entra com a própria conta e continua trabalhando de forma independente.
                            </p>

                            <ul class="list-unstyled d-grid gap-3 mb-4">
                                <li class="d-flex gap-2">
                                    <i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i>
                                    <span>Contratação por quantidade de acessos Prazzu Plus</span>
                                </li>
                                <li class="d-flex gap-2">
                                    <i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i>
                                    <span>Convites individuais e administração das vagas contratadas</span>
                                </li>
                                <li class="d-flex gap-2">
                                    <i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i>
                                    <span>Login, histórico, favoritos, resultados e preferências separados para cada colaborador</span>
                                </li>
                                <li class="d-flex gap-2">
                                    <i class="bi bi-shield-lock-fill prazzu-plans__accent flex-shrink-0" aria-hidden="true"></i>
                                    <span>A empresa administra licenças, sem acesso automático aos dados de uso dos membros</span>
                                </li>
                            </ul>

                            <div class="row g-3 mb-4" aria-label="Faixas de preço do plano empresarial">
                                <div class="col-12 col-md-4">
                                    <div class="border rounded-3 p-3 h-100 bg-body">
                                        <div class="fw-semibold mb-1">Até 5 usuários</div>
                                        <div class="h4 mb-1">R$ 29,90 <small class="fs-6 fw-normal text-body-secondary">/usuário/mês</small></div>
                                        <div class="small text-body-secondary">Ideal para pequenas equipes.</div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="border rounded-3 p-3 h-100 bg-body">
                                        <div class="fw-semibold mb-1">De 6 a 20 usuários</div>
                                        <div class="h4 mb-1">R$ 24,90 <small class="fs-6 fw-normal text-body-secondary">/usuário/mês</small></div>
                                        <div class="small text-body-secondary">Mais economia conforme a equipe cresce.</div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="border rounded-3 p-3 h-100 bg-body">
                                        <div class="fw-semibold mb-1">21 ou mais usuários</div>
                                        <div class="h4 mb-1">R$ 19,90 <small class="fs-6 fw-normal text-body-secondary">/usuário/mês</small></div>
                                        <div class="small text-body-secondary">Melhor valor por licença para equipes maiores.</div>
                                    </div>
                                </div>
                            </div>

                            <p class="small text-body-secondary mb-4">
                                Os valores empresariais são cobrados por usuário licenciado. Como referência, o Prazzu Plus individual custa R$ 39,90 por mês.
                            </p>

                            <div class="d-flex flex-wrap gap-2">
                                @auth
                                    <a class="btn prazzu-btn-outline btn-lg" href="{{ route('organizations.create') }}">
                                        <i class="bi bi-building-add me-1" aria-hidden="true"></i>Criar conta empresarial
                                    </a>
                                @else
                                    <a class="btn prazzu-btn-outline btn-lg" href="{{ route('login') }}">
                                        <i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i>Entrar para criar uma empresa
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5 prazzu-plans__business-aside border-start-lg">
                        <div class="p-4 p-lg-5 h-100 d-flex flex-column justify-content-center">
                            <h3 class="h4 mb-3">O que o plano empresarial não faz</h3>
                            <p class="text-body-secondary">
                                O Prazzu Tools continua sendo uma plataforma de ferramentas, não um ambiente colaborativo ou ERP.
                            </p>
                            <ul class="list-unstyled d-grid gap-3 mb-0">
                                <li class="d-flex gap-2"><i class="bi bi-x-circle text-body-secondary flex-shrink-0" aria-hidden="true"></i><span>Não compartilha históricos ou cálculos</span></li>
                                <li class="d-flex gap-2"><i class="bi bi-x-circle text-body-secondary flex-shrink-0" aria-hidden="true"></i><span>Não transfere a propriedade da conta pessoal</span></li>
                                <li class="d-flex gap-2"><i class="bi bi-x-circle text-body-secondary flex-shrink-0" aria-hidden="true"></i><span>Não cria departamentos, workflows ou gestão de clientes</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </article>
        </section>

        <section class="mb-5" aria-labelledby="comparison-heading">
            <div class="text-center mb-4">
                <span class="prazzu-eyebrow">Compare as experiências</span>
                <h2 id="comparison-heading" class="h2">Gratuito resolve. Plus acelera.</h2>
                <p class="text-body-secondary mb-0">A qualidade do cálculo é a mesma. O Plus adiciona produtividade e continuidade.</p>
            </div>

            <div class="table-responsive rounded-3 shadow-sm prazzu-plans__comparison">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th scope="col" class="py-3">Recurso</th>
                        <th scope="col" class="text-center py-3">Gratuito</th>
                        <th scope="col" class="text-center py-3"><i class="bi bi-gem me-1 prazzu-plans__accent" aria-hidden="true"></i>Prazzu Plus</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><th scope="row">Cálculos e resultados completos</th><td class="text-center"><i class="bi bi-check-circle-fill prazzu-plans__check" aria-label="Incluído"></i></td><td class="text-center"><i class="bi bi-check-circle-fill prazzu-plans__check" aria-label="Incluído"></i></td></tr>
                    <tr><th scope="row">Uso das ferramentas</th><td class="text-center">Ilimitado</td><td class="text-center">Ilimitado</td></tr>
                    <tr><th scope="row">Recursos Plus</th><td class="text-center">Liberados no lançamento</td><td class="text-center">Incluídos na assinatura</td></tr>
                    <tr><th scope="row">Histórico e salvamento</th><td class="text-center text-body-secondary"><i class="bi bi-dash-lg" aria-label="Não incluído"></i></td><td class="text-center"><i class="bi bi-check-circle-fill prazzu-plans__check" aria-label="Incluído"></i></td></tr>
                    <tr><th scope="row">Simulações e comparações</th><td class="text-center text-body-secondary"><i class="bi bi-dash-lg" aria-label="Não incluído"></i></td><td class="text-center"><i class="bi bi-check-circle-fill prazzu-plans__check" aria-label="Incluído"></i></td></tr>
                    <tr><th scope="row">Projeções, alertas e automações</th><td class="text-center text-body-secondary"><i class="bi bi-dash-lg" aria-label="Não incluído"></i></td><td class="text-center"><i class="bi bi-check-circle-fill prazzu-plans__check" aria-label="Incluído"></i></td></tr>
                    <tr><th scope="row">Recursos Plus atuais e futuros</th><td class="text-center">Durante o lançamento</td><td class="text-center"><i class="bi bi-check-circle-fill prazzu-plans__check" aria-label="Incluído"></i></td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mb-5" aria-labelledby="faq-heading">
            <div class="text-center mb-4">
                <span class="prazzu-eyebrow">Dúvidas frequentes</span>
                <h2 id="faq-heading" class="h2">Antes de escolher seu plano</h2>
            </div>

            <div class="accordion mx-auto prazzu-plans__faq" id="plans-faq" style="max-width: 900px;">
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq-free" aria-expanded="true" aria-controls="faq-free">
                            A versão gratuita continuará completa?
                        </button>
                    </h3>
                    <div id="faq-free" class="accordion-collapse collapse show" data-bs-parent="#plans-faq">
                        <div class="accordion-body">Sim. As ferramentas gratuitas continuarão resolvendo o problema principal com resultados completos e confiáveis. O Plus adiciona produtividade, histórico, automações e recursos avançados.</div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-all-tools" aria-expanded="false" aria-controls="faq-all-tools">
                            Preciso assinar cada ferramenta separadamente?
                        </button>
                    </h3>
                    <div id="faq-all-tools" class="accordion-collapse collapse" data-bs-parent="#plans-faq">
                        <div class="accordion-body">Não. Uma única assinatura do Prazzu Plus libera os recursos Plus de todas as ferramentas da plataforma, inclusive das novas ferramentas que forem adicionadas ao plano.</div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-launch" aria-expanded="false" aria-controls="faq-launch">
                            Como funciona o lançamento gratuito?
                        </button>
                    </h3>
                    <div id="faq-launch" class="accordion-collapse collapse" data-bs-parent="#plans-faq">
                        <div class="accordion-body">Nesta fase, todos os recursos públicos Essenciais e Plus estão liberados gratuitamente e sem limites comerciais. O login é necessário somente para recursos vinculados à identidade, como salvar e recuperar histórico. A futura monetização será ativada por uma política central, sem reduzir a qualidade do Essencial.</div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-business" aria-expanded="false" aria-controls="faq-business">
                            Como funciona o plano para empresas?
                        </button>
                    </h3>
                    <div id="faq-business" class="accordion-collapse collapse" data-bs-parent="#plans-faq">
                        <div class="accordion-body">
                            A empresa contrata uma quantidade de acessos Plus e atribui cada vaga a um colaborador com conta própria. A empresa administra somente os vínculos e as licenças; históricos, cálculos, favoritos, resultados e preferências continuam individuais e privados.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-payment" aria-expanded="false" aria-controls="faq-payment">
                            Já posso assinar agora?
                        </button>
                    </h3>
                    <div id="faq-payment" class="accordion-collapse collapse" data-bs-parent="#plans-faq">
                        <div class="accordion-body">Ainda não. Esta página apresenta os planos e valores, mas o checkout e a cobrança recorrente serão conectados em uma etapa futura do projeto.</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="card shadow-sm overflow-hidden prazzu-plans__cta" aria-labelledby="plans-cta-heading">
            <div class="card-body p-4 p-lg-5 text-center">
                <span class="badge prazzu-plans__badge mb-3">Experimente primeiro</span>
                <h2 id="plans-cta-heading" class="h2 mb-3">Conheça a diferença antes de assinar</h2>
                <p class="text-body-secondary mx-auto mb-4" style="max-width: 700px;">
                    Aproveite o lançamento para usar gratuitamente as soluções Essenciais completas e todos os recursos Plus disponíveis.
                </p>
                <a class="btn prazzu-btn-primary btn-lg" href="{{ route('tools.index') }}">
                    <i class="bi bi-grid-3x3-gap me-1" aria-hidden="true"></i>Conhecer as ferramentas
                </a>
            </div>
        </section>
    </div>
@endsection
