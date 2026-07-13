@extends('layouts.app')

@section('title', 'Sugerir ferramenta — Prazzu Tools')
@section('meta_description', 'Envie uma sugestão de nova ferramenta contábil para a plataforma Prazzu.')

@section('content')
    <div class="prazzu-page">
        <header class="prazzu-page-hero">
            <span class="prazzu-page-hero__icon"><i class="bi bi-lightbulb" aria-hidden="true"></i></span>
            <div>
                <span class="prazzu-eyebrow">Ajude a construir a plataforma</span>
                <h1>Sugira uma ferramenta</h1>
                <p>Conte qual problema contábil você precisa resolver. A sugestão ajudará a definir as próximas ferramentas.</p>
            </div>
        </header>

        <form class="prazzu-form-panel" action="{{ route('tools.suggest.store') }}" method="post">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="suggest-name">Nome da ferramenta</label>
                    <input class="form-control prazzu-form-control @error('name') is-invalid @enderror" id="suggest-name" name="name" value="{{ old('name') }}" required maxlength="120">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="suggest-email">Seu e-mail</label>
                    <input class="form-control prazzu-form-control @error('email') is-invalid @enderror" id="suggest-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="suggest-problem">Qual problema ela deve resolver?</label>
                    <textarea class="form-control prazzu-form-control @error('problem') is-invalid @enderror" id="suggest-problem" name="problem" rows="7" required minlength="20" maxlength="2000">{{ old('problem') }}</textarea>
                    @error('problem')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary prazzu-btn-primary" type="submit"><i class="bi bi-send me-2"></i>Enviar sugestão</button>
                </div>
            </div>
        </form>
    </div>
@endsection
