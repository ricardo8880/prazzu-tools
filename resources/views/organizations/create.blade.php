@extends('layouts.app')

@section('title', 'Cadastrar empresa — Prazzu Tools')
@section('meta_description', 'Cadastre uma empresa para administrar acessos Prazzu Plus.')
@section('meta_robots', 'noindex,nofollow')

@section('content')
    <div class="prazzu-page">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-6">
                <section class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <span class="badge text-bg-primary mb-3">Conta empresarial</span>
                        <h1 class="h3 mb-2">Cadastrar empresa</h1>
                        <p class="text-body-secondary mb-4">
                            A empresa administrará apenas os acessos Prazzu Plus. Cada colaborador continuará com login, histórico, favoritos e resultados próprios.
                        </p>

                        <form method="POST" action="{{ route('organizations.store') }}" novalidate>
                            @csrf
                            <div class="mb-4">
                                <label class="form-label" for="name">Nome da empresa</label>
                                <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name') }}" maxlength="160" required autofocus>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="alert alert-info" role="note">
                                <i class="bi bi-shield-check me-2" aria-hidden="true"></i>
                                Sua conta será definida como responsável pela empresa. Nenhum dado pessoal de colaboradores será compartilhado.
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <button class="btn btn-primary" type="submit">Cadastrar empresa</button>
                                <a class="btn btn-outline-secondary" href="{{ route('account.show') }}">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
