@extends('layouts.app')

@section('title', 'Suggest a tool — Prazzu Tools')
@section('meta_description', 'Send a suggestion for a new tool to the Prazzu platform.')

@section('content')
    <div class="prazzu-page">
        <header class="prazzu-page-hero">
            <span class="prazzu-page-hero__icon"><i class="bi bi-lightbulb" aria-hidden="true"></i></span>
            <div>
                <span class="prazzu-eyebrow">Ajude a construir a plataforma</span>
                <h1>Suggest a tool</h1>
                <p>Tell us what problem you need to solve. Your suggestion will help define the next tools we build.</p>
            </div>
        </header>

        <form class="prazzu-form-panel" action="{{ route('tools.suggest.store') }}" method="post">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="suggest-name">Tool name</label>
                    <input class="form-control prazzu-form-control @error('name') is-invalid @enderror" id="suggest-name" name="name" value="{{ old('name') }}" required maxlength="120">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="suggest-email">Your email</label>
                    <input class="form-control prazzu-form-control @error('email') is-invalid @enderror" id="suggest-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="suggest-problem">Qual problema ela deve resolver?</label>
                    <textarea class="form-control prazzu-form-control @error('problem') is-invalid @enderror" id="suggest-problem" name="problem" rows="7" required minlength="20" maxlength="2000">{{ old('problem') }}</textarea>
                    @error('problem')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary prazzu-btn-primary" type="submit"><i class="bi bi-send me-2"></i>Send suggestion</button>
                </div>
            </div>
        </form>
    </div>
@endsection
