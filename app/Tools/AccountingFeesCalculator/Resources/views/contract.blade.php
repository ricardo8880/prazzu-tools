@extends('layouts.app')

@section('title', 'Contrato de Prestação de Serviços Contábeis — Prazzu Tools')
@section('meta_description', 'Modelo de contrato de prestação de serviços contábeis pronto para revisão e impressão.')

@section('content')
<div class="prazzu-page tool-page" data-tool="calculadora-de-honorarios-contabeis-contrato">
    <div class="d-print-none mb-3 d-flex flex-column flex-sm-row justify-content-between gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('tools.calculadora-de-honorarios-contabeis.index') }}">
            <i class="bi bi-arrow-left me-1"></i>Voltar à calculadora
        </a>
        <button class="btn btn-primary" type="button" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Imprimir contrato
        </button>
    </div>

    <div class="alert alert-warning d-print-none" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i>
        Este é um modelo de apoio. Revise os dados e submeta o texto à validação jurídica antes da assinatura.
    </div>

    <article class="card border-0 shadow-sm">
        <div class="card-body p-4 p-lg-5">
            <header class="text-center border-bottom pb-4 mb-4">
                <span class="text-uppercase small fw-semibold text-primary">Instrumento particular</span>
                <h1 class="h2 mt-2 mb-0">Contrato de Prestação de Serviços Contábeis</h1>
            </header>

            <p><strong>CONTRATADA:</strong> {{ $contract->accountingFirm }}@if($contract->accountingFirmDocument), inscrita sob o documento {{ $contract->accountingFirmDocument }}@endif, neste ato representada por {{ $contract->accountingRepresentative }}.</p>
            <p><strong>CONTRATANTE:</strong> {{ $contract->clientCompany }}@if($contract->clientDocument), inscrita sob o documento {{ $contract->clientDocument }}@endif, neste ato representada por {{ $contract->clientRepresentative }}.</p>
            <p>As partes acima identificadas celebram o presente contrato, mediante as cláusulas e condições seguintes.</p>

            <section class="mb-4">
                <h2 class="h5">Cláusula 1ª — Objeto</h2>
                <p>A CONTRATADA prestará à CONTRATANTE os seguintes serviços contábeis recorrentes:</p>
                <ul>
                    @foreach ($contract->services as $service)
                        <li>{{ $service }}</li>
                    @endforeach
                </ul>
                <p class="mb-0">Atividades extraordinárias ou não relacionadas acima dependerão de orçamento e aceite específicos.</p>
            </section>

            <section class="mb-4">
                <h2 class="h5">Cláusula 2ª — Obrigações das partes</h2>
                <p>A CONTRATADA executará os serviços com diligência técnica, manterá a CONTRATANTE informada sobre pendências relevantes e observará os prazos aplicáveis, desde que receba tempestivamente os documentos e informações necessários.</p>
                <p class="mb-0">A CONTRATANTE fornecerá informações verdadeiras e completas, entregará documentos nos prazos combinados, comunicará alterações cadastrais e será responsável pelas decisões tomadas com base em dados incompletos ou enviados fora do prazo.</p>
            </section>

            <section class="mb-4">
                <h2 class="h5">Cláusula 3ª — Honorários e pagamento</h2>
                <p>Os honorários mensais serão de <strong>{{ $contract->monthlyFee->formatPtBr() }}</strong>, com vencimento no dia <strong>{{ $contract->dueDay }}</strong> de cada mês.</p>
                <p class="mb-0">No atraso, poderá incidir multa de <strong>{{ $contract->lateFeePercent }}%</strong>, além de atualização e juros previstos na cobrança ou na legislação aplicável.</p>
            </section>

            <section class="mb-4">
                <h2 class="h5">Cláusula 4ª — Reajuste</h2>
                <p class="mb-0">Os honorários serão reajustados a cada 12 meses pela variação acumulada do índice <strong>{{ $contract->adjustmentIndex }}</strong>, ou por índice que legalmente o substitua, respeitada negociação formal entre as partes.</p>
            </section>

            <section class="mb-4">
                <h2 class="h5">Cláusula 5ª — Vigência e rescisão</h2>
                <p>O contrato vigorará por {{ $contract->durationMonths }} meses, de <strong>{{ $contract->startsAt->format('d/m/Y') }}</strong> até <strong>{{ $contract->endsAt->format('d/m/Y') }}</strong>.</p>
                <p class="mb-0">Após esse período, poderá ser renovado por acordo das partes. A rescisão deverá ser comunicada por escrito com antecedência mínima de <strong>{{ $contract->terminationNoticeDays }} dias</strong>, sem prejuízo da quitação dos valores pendentes.</p>
            </section>

            @if ($contract->includesConfidentiality)
                <section class="mb-4">
                    <h2 class="h5">Cláusula 6ª — Confidencialidade</h2>
                    <p class="mb-0">As partes manterão sigilo sobre informações técnicas, comerciais, financeiras e cadastrais acessadas em razão deste contrato, salvo autorização expressa ou obrigação legal.</p>
                </section>
            @endif

            @if ($contract->includesLgpd)
                <section class="mb-4">
                    <h2 class="h5">Cláusula {{ $contract->includesConfidentiality ? '7ª' : '6ª' }} — Proteção de dados</h2>
                    <p class="mb-0">As partes comprometem-se a tratar dados pessoais apenas para execução dos serviços, cumprimento de obrigações legais e exercício regular de direitos, adotando medidas razoáveis de segurança e cooperação em incidentes.</p>
                </section>
            @endif

            @if ($contract->additionalTerms)
                <section class="mb-4">
                    <h2 class="h5">Condições adicionais</h2>
                    <div class="border rounded p-3 bg-body-tertiary">{!! nl2br(e($contract->additionalTerms)) !!}</div>
                </section>
            @endif

            <section class="mb-5">
                <h2 class="h5">Disposições finais</h2>
                <p class="mb-0">As partes buscarão solução consensual para divergências decorrentes deste instrumento. O foro e demais condições jurídicas devem ser definidos após revisão profissional do modelo.</p>
            </section>

            <p class="text-center mb-5">Emitido em {{ $contract->issuedAt->format('d/m/Y') }}.</p>

            <footer class="row g-5">
                <div class="col-12 col-md-6"><div class="border-top pt-2 mt-5 text-center">{{ $contract->accountingFirm }}<br><small>CONTRATADA</small></div></div>
                <div class="col-12 col-md-6"><div class="border-top pt-2 mt-5 text-center">{{ $contract->clientCompany }}<br><small>CONTRATANTE</small></div></div>
            </footer>
        </div>
    </article>
</div>
@endsection
