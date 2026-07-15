@extends('layouts.app')

@section('title', 'Proposta Comercial de Serviços Contábeis — Prazzu Tools')
@section('meta_description', 'Proposta comercial de serviços contábeis pronta para apresentação e impressão.')

@section('content')
<div class="prazzu-page tool-page" data-tool="calculadora-de-honorarios-contabeis-proposta">
    <div class="d-print-none mb-3 d-flex flex-column flex-sm-row justify-content-between gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('tools.calculadora-de-honorarios-contabeis.index') }}">
            <i class="bi bi-arrow-left me-1"></i>Voltar à calculadora
        </a>
        <button class="btn btn-primary" type="button" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Imprimir proposta
        </button>
    </div>

    <article class="card border-0 shadow-sm">
        <div class="card-body p-4 p-lg-5">
            <header class="border-bottom pb-4 mb-4">
                <div class="row g-4 align-items-start">
                    <div class="col-12 col-md">
                        <span class="text-uppercase small fw-semibold text-primary">Proposta comercial</span>
                        <h1 class="h2 mt-2 mb-1">Serviços contábeis</h1>
                        <p class="text-body-secondary mb-0">Preparada por {{ $proposal->accountingFirm }}</p>
                    </div>
                    <div class="col-12 col-md-auto text-md-end">
                        <div class="small text-body-secondary">Emissão</div>
                        <strong>{{ $proposal->issuedAt->format('d/m/Y') }}</strong>
                        <div class="small text-body-secondary mt-2">Válida até</div>
                        <strong>{{ $proposal->validUntil->format('d/m/Y') }}</strong>
                    </div>
                </div>
            </header>

            <section class="mb-5" aria-labelledby="proposal-client-title">
                <h2 id="proposal-client-title" class="h5">Apresentada para</h2>
                <div class="border rounded p-3 bg-body-tertiary">
                    <div class="fw-bold fs-5">{{ $proposal->clientCompany }}</div>
                    @if ($proposal->clientDocument)
                        <div class="text-body-secondary">Documento: {{ $proposal->clientDocument }}</div>
                    @endif
                    <div class="text-body-secondary">Aos cuidados de {{ $proposal->contactName }}</div>
                </div>
            </section>

            <section class="mb-5" aria-labelledby="proposal-intro-title">
                <h2 id="proposal-intro-title" class="h5">Objetivo</h2>
                <p class="mb-0">Apresentamos esta proposta para execução dos serviços contábeis recorrentes da empresa, com atendimento orientado à regularidade das obrigações, organização das informações e suporte à gestão.</p>
            </section>

            <section class="mb-5" aria-labelledby="proposal-services-title">
                <h2 id="proposal-services-title" class="h5">Serviços incluídos</h2>
                <div class="row g-2">
                    @foreach ($proposal->services as $service)
                        <div class="col-12 col-md-6">
                            <div class="border rounded p-3 h-100 d-flex gap-2 align-items-start">
                                <i class="bi bi-check-circle-fill text-success" aria-hidden="true"></i>
                                <span>{{ $service }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="mb-5" aria-labelledby="proposal-investment-title">
                <h2 id="proposal-investment-title" class="h5">Investimento</h2>
                <div class="row g-3">
                    <div class="col-12 col-md-7">
                        <div class="card border-primary h-100">
                            <div class="card-body">
                                <div class="text-body-secondary">Honorário mensal</div>
                                <div class="display-6 fw-bold text-primary">{{ $proposal->monthlyFee->formatPtBr() }}</div>
                                <div class="small text-body-secondary mt-2">Vencimento todo dia {{ $proposal->dueDay }}.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-5">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-body-secondary">Implantação</div>
                                <div class="h3 fw-bold mb-0">{{ $proposal->setupFee->formatPtBr() }}</div>
                                <div class="small text-body-secondary mt-2">Cobrança única para início e organização cadastral.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            @if ($proposal->notes)
                <section class="mb-5" aria-labelledby="proposal-notes-title">
                    <h2 id="proposal-notes-title" class="h5">Observações comerciais</h2>
                    <div class="alert alert-light border mb-0">{!! nl2br(e($proposal->notes)) !!}</div>
                </section>
            @endif

            <section class="mb-5" aria-labelledby="proposal-conditions-title">
                <h2 id="proposal-conditions-title" class="h5">Condições gerais</h2>
                <ul class="mb-0">
                    <li>Esta proposta possui validade de {{ $proposal->validityDays }} dias.</li>
                    <li>Demandas extraordinárias e serviços fora do escopo serão previamente orçados.</li>
                    <li>O início dos serviços depende do aceite e do fornecimento das informações necessárias.</li>
                    <li>Os termos definitivos serão formalizados em contrato de prestação de serviços.</li>
                </ul>
            </section>

            <footer class="border-top pt-4">
                <div class="row g-5">
                    <div class="col-12 col-md-6">
                        <div class="border-top pt-2 mt-5 text-center">{{ $proposal->accountingFirm }}</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="border-top pt-2 mt-5 text-center">{{ $proposal->clientCompany }}</div>
                    </div>
                </div>
            </footer>
        </div>
    </article>
</div>
@endsection
