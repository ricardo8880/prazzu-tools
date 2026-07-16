@extends('layouts.app')

@section('title', 'Criar nova senha — Prazzu Tools')
@section('meta_description', 'Crie uma nova senha para sua conta Prazzu Tools.')
@section('meta_robots', 'noindex,nofollow')

@section('content')
    <div class="prazzu-page">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7 col-xl-5">
                <section class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <span class="badge text-bg-primary mb-3">Nova senha</span>
                        <h1 class="h3 mb-4">Redefina sua senha</h1>

                        <form method="POST" action="{{ route('password.store') }}" novalidate>
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="mb-3">
                                <label class="form-label" for="email">E-mail</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="email" required autofocus>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="password">Nova senha</label>
                                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" required>
                                <div class="form-text">Use pelo menos 8 caracteres, com letras e números.</div>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="password_confirmation">Confirmar nova senha</label>
                                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                            </div>
                            <button class="btn btn-primary w-100" type="submit">Salvar nova senha</button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
