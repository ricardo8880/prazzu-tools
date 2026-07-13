@extends('layouts.app')

@section('title', $page['title'].' — Prazzu Tools')
@section('meta_description', $page['description'])

@section('content')
    <div class="prazzu-page">
        <section class="prazzu-content-placeholder">
            <span class="prazzu-content-placeholder__icon"><i class="bi {{ $page['icon'] }}" aria-hidden="true"></i></span>
            <span class="prazzu-eyebrow">{{ $page['eyebrow'] }}</span>
            <h1>{{ $page['title'] }}</h1>
            <p>{{ $page['description'] }}</p>
            <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
                <a class="btn btn-primary prazzu-btn-primary" href="{{ route('tools.index') }}">Explorar ferramentas</a>
                <a class="btn prazzu-btn-outline" href="{{ route('home') }}">Voltar ao início</a>
            </div>
        </section>
    </div>
@endsection
