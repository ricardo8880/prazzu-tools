@extends('layouts.app')

@php($editing = $client->exists)
@section('title', ($editing ? 'Editar cliente' : 'Novo cliente').' — CRM de Honorários')

@section('content')
<div class="prazzu-page tool-page">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.calculadora-de-honorarios-contabeis.index') }}">Honorários contábeis</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.calculadora-de-honorarios-contabeis.crm.index') }}">CRM</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $editing ? 'Editar' : 'Novo cliente' }}</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">{{ $editing ? 'Editar cadastro' : 'Novo cliente ou prospect' }}</h1>
            <p class="text-body-secondary mb-0">Registre dados comerciais e acompanhe o avanço da negociação.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('tools.calculadora-de-honorarios-contabeis.crm.index') }}"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
    </div>

    <form method="post" action="{{ $editing ? route('tools.calculadora-de-honorarios-contabeis.crm.update', $client) : route('tools.calculadora-de-honorarios-contabeis.crm.store') }}">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body py-3"><h2 class="h5 mb-0">Dados do contato</h2></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-lg-8">
                        <label class="form-label" for="company_name">Empresa <span class="text-danger">*</span></label>
                        <input class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name', $client->company_name) }}" maxlength="150" required>
                        @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="form-label" for="document">CNPJ/CPF</label>
                        <input class="form-control @error('document') is-invalid @enderror" id="document" name="document" value="{{ old('document', $client->document) }}" maxlength="30">
                        @error('document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="form-label" for="contact_name">Responsável <span class="text-danger">*</span></label>
                        <input class="form-control @error('contact_name') is-invalid @enderror" id="contact_name" name="contact_name" value="{{ old('contact_name', $client->contact_name) }}" maxlength="120" required>
                        @error('contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <label class="form-label" for="email">E-mail</label>
                        <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $client->email) }}" maxlength="150">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <label class="form-label" for="phone">Telefone</label>
                        <input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $client->phone) }}" maxlength="30">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body py-3"><h2 class="h5 mb-0">Negociação e documentos</h2></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-xl-3">
                        <label class="form-label" for="monthly_fee">Honorário mensal <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input class="form-control @error('monthly_fee') is-invalid @enderror" id="monthly_fee" name="monthly_fee" value="{{ old('monthly_fee', $client->exists ? number_format($client->monthly_fee_cents / 100, 2, ',', '.') : '') }}" placeholder="1.500,00" inputmode="decimal" required>
                            @error('monthly_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <label class="form-label" for="pipeline_status">Etapa do CRM <span class="text-danger">*</span></label>
                        <select class="form-select @error('pipeline_status') is-invalid @enderror" id="pipeline_status" name="pipeline_status" required>
                            @foreach (['prospect' => 'Prospect', 'negotiation' => 'Em negociação', 'client' => 'Cliente', 'inactive' => 'Inativo'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('pipeline_status', $client->pipeline_status ?: 'prospect') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('pipeline_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <label class="form-label" for="proposal_status">Proposta <span class="text-danger">*</span></label>
                        <select class="form-select @error('proposal_status') is-invalid @enderror" id="proposal_status" name="proposal_status" required>
                            @foreach (['not_created' => 'Não criada', 'draft' => 'Rascunho', 'sent' => 'Enviada', 'accepted' => 'Aceita', 'rejected' => 'Recusada'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('proposal_status', $client->proposal_status ?: 'not_created') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('proposal_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <label class="form-label" for="contract_status">Contrato <span class="text-danger">*</span></label>
                        <select class="form-select @error('contract_status') is-invalid @enderror" id="contract_status" name="contract_status" required>
                            @foreach (['not_created' => 'Não criado', 'draft' => 'Rascunho', 'sent' => 'Enviado', 'signed' => 'Assinado', 'cancelled' => 'Cancelado'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('contract_status', $client->contract_status ?: 'not_created') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('contract_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="notes">Observações e histórico</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="5" maxlength="3000" placeholder="Registre contatos, objeções, próximos passos e condições combinadas.">{{ old('notes', $client->notes) }}</textarea>
                        <div class="form-text">As alterações do cadastro ficam registradas pelas datas de criação e atualização.</div>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('tools.calculadora-de-honorarios-contabeis.crm.index') }}">Cancelar</a>
            <button class="btn btn-primary" type="submit"><i class="bi bi-floppy me-1"></i>{{ $editing ? 'Salvar alterações' : 'Adicionar ao CRM' }}</button>
        </div>
    </form>
</div>
@endsection
