@extends('layouts.app')

@section('title', 'Criar conta — Prazzu Tools')
@section('meta_description', 'Crie uma conta gratuita para salvar e recuperar seus resultados no Prazzu Tools.')
@section('meta_robots', 'noindex,follow')

@section('content')
    @php
        $registrationSource = request()->query('source');
        $registrationAttribution = in_array($registrationSource, ['result_continuity', 'tool_favorite'], true)
            ? array_filter([
                'source' => $registrationSource,
                'tool' => is_string(request()->query('tool')) ? request()->query('tool') : null,
            ])
            : [];
        $isFavoriteIntent = $registrationSource === 'tool_favorite';
    @endphp
    <div class="prazzu-page">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7 col-xl-5">
                <section class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <span class="badge text-bg-primary mb-3">Grátis</span>
                        <h1 class="h3 mb-2">{{ $isFavoriteIntent ? 'Salve esta ferramenta para encontrar depois' : 'Crie sua conta para continuar seu trabalho' }}</h1>
                        <p class="text-body-secondary mb-3">
                            @if($isFavoriteIntent)
                                A conta gratuita mantém seus favoritos disponíveis para você voltar quando precisar, inclusive em outro dispositivo.
                            @else
                                Use sua conta para recuperar resultados, favoritar ferramentas, repetir cálculos e continuar depois, inclusive em outro dispositivo.
                            @endif
                        </p>
                        <p class="small text-body-secondary mb-4">
                            O cadastro é opcional e não libera cálculos: as ferramentas continuam completas para todos.
                        </p>

                        <form method="POST" action="{{ route('register.store', $registrationAttribution) }}" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label class="form-label" for="name">Nome</label>
                                <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name') }}" maxlength="120" autocomplete="name" required autofocus>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="email">E-mail</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password">Senha</label>
                                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" required>
                                <div class="form-text">Use pelo menos 8 caracteres, com letras e números.</div>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label" for="password_confirmation">Confirmar senha</label>
                                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                            </div>

                            <button class="btn btn-primary w-100" type="submit">Criar conta gratuita</button>
                        </form>

                        <p class="text-center text-body-secondary mt-4 mb-0">
                            Já possui uma conta? <a href="{{ route('login', $registrationAttribution) }}">Entrar</a>.
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
