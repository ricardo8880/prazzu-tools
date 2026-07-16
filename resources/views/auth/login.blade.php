@extends('layouts.app')

@section('title', 'Entrar — Prazzu Tools')
@section('meta_description', 'Acesse sua conta gratuita para salvar e recuperar resultados no Prazzu Tools.')
@section('meta_robots', 'noindex,follow')

@section('content')
    <div class="prazzu-page">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7 col-xl-5">
                <section class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <span class="badge text-bg-primary mb-3">Conta gratuita</span>
                        <h1 class="h3 mb-2">Entre para recuperar seus resultados</h1>
                        <p class="text-body-secondary mb-4">
                            As ferramentas continuam completas e sem limite mesmo sem login. Sua conta serve para salvar e acessar seus dados depois.
                        </p>

                        @if (session('auth_notice'))
                            <div class="alert alert-info" role="status">{{ session('auth_notice') }}</div>
                        @endif

                        <form method="POST" action="{{ route('login.store') }}" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label class="form-label" for="email">E-mail</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password">Senha</label>
                                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="current-password" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check mb-0">
                                <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))>
                                    <label class="form-check-label" for="remember">Manter conectada</label>
                                </div>
                                <a href="{{ route('password.request') }}">Esqueci minha senha</a>
                            </div>

                            <button class="btn btn-primary w-100" type="submit">Entrar</button>
                        </form>

                        <p class="text-center text-body-secondary mt-4 mb-0">
                            Ainda não possui conta?
                            <a href="{{ route('register') }}">Crie gratuitamente para salvar seus resultados</a>.
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
