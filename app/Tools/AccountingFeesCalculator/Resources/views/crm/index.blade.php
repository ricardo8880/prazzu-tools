@extends('layouts.app')

@section('title', 'CRM de Honorários Contábeis — Prazzu Tools')
@section('meta_description', 'Organize prospects e clientes, propostas, contratos e honorários contábeis em um CRM simples.')

@section('content')
<div class="prazzu-page tool-page">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.calculadora-de-honorarios-contabeis.index') }}">Honorários contábeis</a></li>
            <li class="breadcrumb-item active" aria-current="page">CRM</li>
        </ol>
    </nav>

    <header class="prazzu-tool-intro">
        <span class="prazzu-icon-tile prazzu-icon-tile--purple"><i class="bi bi-people"></i></span>
        <div class="flex-grow-1">
            <span class="badge text-bg-primary mb-2">Lote 6</span>
            <h1>CRM de honorários</h1>
            <p>Acompanhe clientes, propostas, contratos e valores mensais em um só lugar.</p>
        </div>
        <a class="btn btn-primary align-self-start" href="{{ route('tools.calculadora-de-honorarios-contabeis.crm.create') }}">
            <i class="bi bi-person-plus me-1"></i>Novo cliente
        </a>
    </header>

    @include('tools-calculadora-de-honorarios-contabeis::partials.navigation')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    @php($labels = ['prospect' => 'Prospects', 'negotiation' => 'Em negociação', 'client' => 'Clientes', 'inactive' => 'Inativos'])
    @php($icons = ['prospect' => 'bi-person-lines-fill', 'negotiation' => 'bi-chat-dots', 'client' => 'bi-person-check', 'inactive' => 'bi-person-dash'])
    <div class="row g-3 mb-4">
        @foreach ($labels as $key => $label)
            <div class="col-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="fs-3 text-primary"><i class="bi {{ $icons[$key] }}"></i></span>
                        <div>
                            <div class="text-body-secondary small">{{ $label }}</div>
                            <div class="h3 mb-0">{{ (int) ($summary[$key] ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <section class="card border-0 shadow-sm">
        <div class="card-body">
            <form class="row g-2 align-items-end mb-4" method="get">
                <div class="col-12 col-lg-7">
                    <label class="form-label" for="search">Pesquisar</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="search" name="search" value="{{ $search }}" placeholder="Empresa, CNPJ, responsável ou e-mail">
                    </div>
                </div>
                <div class="col-12 col-md-8 col-lg-3">
                    <label class="form-label" for="status">Etapa</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todas as etapas</option>
                        @foreach ($labels as $key => $label)
                            <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4 col-lg-2 d-grid">
                    <button class="btn btn-outline-primary" type="submit">Filtrar</button>
                </div>
            </form>

            @if ($clients->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-people display-5 text-body-tertiary"></i>
                    <h2 class="h5 mt-3">Nenhum cadastro encontrado</h2>
                    <p class="text-body-secondary">Adicione o primeiro prospect ou altere os filtros da pesquisa.</p>
                    <a class="btn btn-primary" href="{{ route('tools.calculadora-de-honorarios-contabeis.crm.create') }}">Cadastrar cliente</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>Etapa</th>
                                <th>Honorário</th>
                                <th>Proposta</th>
                                <th>Contrato</th>
                                <th>Atualizado</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                                @php($pipelineLabels = ['prospect' => 'Prospect', 'negotiation' => 'Negociação', 'client' => 'Cliente', 'inactive' => 'Inativo'])
                                @php($proposalLabels = ['not_created' => 'Não criada', 'draft' => 'Rascunho', 'sent' => 'Enviada', 'accepted' => 'Aceita', 'rejected' => 'Recusada'])
                                @php($contractLabels = ['not_created' => 'Não criado', 'draft' => 'Rascunho', 'sent' => 'Enviado', 'signed' => 'Assinado', 'cancelled' => 'Cancelado'])
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $client->company_name }}</div>
                                        <div class="small text-body-secondary">{{ $client->contact_name }}{{ $client->document ? ' · '.$client->document : '' }}</div>
                                    </td>
                                    <td><span class="badge text-bg-light border">{{ $pipelineLabels[$client->pipeline_status] }}</span></td>
                                    <td class="fw-semibold text-nowrap">{{ $client->formattedMonthlyFee() }}</td>
                                    <td>{{ $proposalLabels[$client->proposal_status] }}</td>
                                    <td>{{ $contractLabels[$client->contract_status] }}</td>
                                    <td class="text-nowrap">{{ $client->updated_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-end text-nowrap">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('tools.calculadora-de-honorarios-contabeis.crm.edit', $client) }}" aria-label="Editar {{ $client->company_name }}"><i class="bi bi-pencil"></i></a>
                                        <form class="d-inline" method="post" action="{{ route('tools.calculadora-de-honorarios-contabeis.crm.delete', $client) }}" onsubmit="return confirm('Remover este cadastro do CRM?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Excluir {{ $client->company_name }}"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $clients->links() }}</div>
            @endif
        </div>
    </section>
</div>
@endsection
