@extends('layouts.app')

@section('title', 'Minha conta — Prazzu Tools')
@section('meta_description', 'Gerencie sua conta local do Prazzu Tools.')
@section('meta_robots', 'noindex,nofollow')

@section('content')
    <div class="prazzu-page">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-9">
                <section class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-4">
                            <div>
                                <span class="badge text-bg-success mb-3">Conta ativa</span>
                                <h1 class="h3 mb-2">Olá, {{ auth()->user()->name }}</h1>
                                <p class="text-body-secondary mb-0">{{ auth()->user()->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Sair
                                </button>
                            </form>
                        </div>

                        <hr class="my-4">

                        @if (auth()->user()->hasVerifiedEmail())
                            <div class="alert alert-success d-flex align-items-center gap-2" role="status">
                                <i class="bi bi-patch-check-fill" aria-hidden="true"></i>
                                <span>Seu e-mail está confirmado.</span>
                            </div>
                        @else
                            <div class="alert alert-warning d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" role="alert">
                                <span>Confirme seu e-mail para proteger a recuperação dos dados salvos.</span>
                                <a class="btn btn-sm btn-warning" href="{{ route('verification.notice') }}">Confirmar e-mail</a>
                            </div>
                        @endif

                        <div class="row g-4 mb-4">
                            <div class="col-12 col-lg-7">
                                <div class="card h-100 border">
                                    <div class="card-body">
                                        <h2 class="h5">Alterar senha</h2>
                                        <form method="POST" action="{{ route('password.update') }}" novalidate>
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label class="form-label" for="current_password">Senha atual</label>
                                                <input class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                                                @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="password">Nova senha</label>
                                                <input class="form-control @error('password', 'updatePassword') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" required>
                                                @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="password_confirmation">Confirmar nova senha</label>
                                                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                                            </div>
                                            <button class="btn btn-primary" type="submit">Atualizar senha</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border mb-4">
                            <div class="card-body">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                    <div>
                                        <h2 class="h5 mb-1">Acessos empresariais</h2>
                                        <p class="text-body-secondary mb-0">Cadastre uma empresa ou acesse empresas às quais sua conta pertence.</p>
                                    </div>
                                    <a class="btn btn-outline-primary" href="{{ route('organizations.create') }}">Cadastrar empresa</a>
                                </div>

                                @php($memberships = auth()->user()->organizationMemberships()->with('organization')->where('status', 'active')->get())
                                @if($memberships->isNotEmpty())
                                    <div class="list-group list-group-flush mt-3">
                                        @foreach($memberships as $membership)
                                            <a class="list-group-item list-group-item-action px-0 d-flex justify-content-between align-items-center" href="{{ route('organizations.show', $membership->organization) }}">
                                                <span>{{ $membership->organization->name }}</span>
                                                <i class="bi bi-chevron-right" aria-hidden="true"></i>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="alert alert-info mb-0" role="note">
                            <strong>Conta Prazzu unificada:</strong>
                            sua conta é local neste momento. A estrutura já está preparada para receber futuramente o identificador da conta única Prazzu sem usar seu e-mail como chave de integração.
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
