@extends('layouts.app')

@section('title', 'Perfis do Emissor de Recibos — Prazzu Tools')
@section('meta_description', 'Gerencie pagadores e recebedores reutilizáveis no Emissor de Recibos.')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center"><div class="col-xl-9">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <div><h1 class="h2 mb-1">Perfis de pagadores e recebedores</h1><p class="text-body-secondary mb-0">Salve dados usados com frequência para preencher novos recibos.</p></div>
            <a class="btn btn-outline-secondary" href="{{ route('tools.emissor-de-recibos.index') }}">Voltar ao emissor</a>
        </div>

        @if(session('profile_message'))<div class="alert alert-success">{{ session('profile_message') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

        <div class="card shadow-sm mb-4"><div class="card-body p-4">
            <h2 class="h4 mb-3">Salvar novo perfil</h2>
            <form method="POST" action="{{ route('tools.emissor-de-recibos.profiles.store') }}" class="row g-3">@csrf
                <div class="col-md-3"><label class="form-label" for="party_type">Uso</label><select class="form-select" id="party_type" name="party_type" required><option value="payer">Pagador</option><option value="payee" @selected(old('party_type') === 'payee')>Recebedor</option></select></div>
                <div class="col-md-4"><label class="form-label" for="label">Apelido</label><input class="form-control" id="label" name="label" value="{{ old('label') }}" maxlength="80" placeholder="Ex.: Cliente mensal" required></div>
                <div class="col-md-5"><label class="form-label" for="name">Nome ou razão social</label><input class="form-control" id="name" name="name" value="{{ old('name') }}" maxlength="160" required></div>
                <div class="col-md-3"><label class="form-label" for="document_type">Documento</label><select class="form-select" id="document_type" name="document_type"><option value="">Sem documento</option><option value="cpf">CPF</option><option value="cnpj">CNPJ</option></select></div>
                <div class="col-md-5"><label class="form-label" for="document">Número</label><input class="form-control" id="document" name="document" value="{{ old('document') }}" maxlength="18"></div>
                <div class="col-md-4 d-flex align-items-end"><button class="btn btn-primary w-100" type="submit">Salvar perfil</button></div>
            </form>
        </div></div>

        <div class="row g-3">
            @forelse($profiles as $profile)
                <div class="col-md-6"><div class="card h-100"><div class="card-body">
                    <div class="d-flex justify-content-between gap-2"><div><span class="badge text-bg-light mb-2">{{ $profile->party_type === 'payer' ? 'Pagador' : 'Recebedor' }}</span><h2 class="h5 mb-1">{{ $profile->label }}</h2><div>{{ $profile->name }}</div>@if($profile->document)<div class="small text-body-secondary mt-1">{{ strtoupper($profile->document_type) }} {{ $profile->document }}</div>@endif</div></div>
                    <div class="d-flex gap-2 mt-3"><form method="POST" action="{{ route('tools.emissor-de-recibos.profiles.use', $profile->id) }}">@csrf<button class="btn btn-sm btn-outline-primary" type="submit">Usar no recibo</button></form><form method="POST" action="{{ route('tools.emissor-de-recibos.profiles.destroy', $profile->id) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button></form></div>
                </div></div></div>
            @empty
                <div class="col-12"><div class="alert alert-light border">Nenhum perfil salvo.</div></div>
            @endforelse
        </div>
    </div></div>
</div>
@endsection
