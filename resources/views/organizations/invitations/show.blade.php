@extends('layouts.app')

@section('title', 'Convite empresarial — Prazzu Tools')
@section('meta_description', 'Aceite um convite empresarial do Prazzu Tools.')
@section('meta_robots', 'noindex,nofollow')

@section('content')
    <div class="prazzu-page">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-6">
                <section class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <span class="badge text-bg-primary mb-3">Convite empresarial</span>
                        <h1 class="h3 mb-2">{{ $invitation->organization->name }} convidou você</h1>
                        <p class="text-body-secondary">O vínculo concede acesso empresarial ao Prazzu Plus sem compartilhar seu histórico, favoritos, resultados ou preferências.</p>

                        @error('invitation')<div class="alert alert-danger">{{ $message }}</div>@enderror

                        <dl class="row border rounded p-3 mx-0 mb-4">
                            <dt class="col-sm-4">Empresa</dt><dd class="col-sm-8">{{ $invitation->organization->name }}</dd>
                            <dt class="col-sm-4">Validade</dt><dd class="col-sm-8 mb-0">{{ $invitation->expires_at->format('d/m/Y H:i') }}</dd>
                        </dl>

                        @if(!$invitation->canBeAccepted())
                            <div class="alert alert-warning mb-0">Este convite não está mais disponível.</div>
                        @elseif(auth()->guest())
                            <div class="d-grid gap-2 d-sm-flex">
                                <a class="btn btn-primary" href="{{ route('login') }}">Entrar para aceitar</a>
                                <a class="btn btn-outline-primary" href="{{ route('register') }}">Criar conta pessoal</a>
                            </div>
                        @else
                            <p class="small text-body-secondary">Você está entrando com {{ auth()->user()->email }}. Confirme para vincular esta conta à empresa.</p>
                            <form method="POST" action="{{ route('organizations.invitations.accept', $invitation->token) }}">
                                @csrf
                                <button class="btn btn-primary" type="submit">Aceitar convite</button>
                            </form>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
