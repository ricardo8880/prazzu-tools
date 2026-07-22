@extends('layouts.app')

@section('title', 'Novo contexto de aquisição | Prazzu Tools')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <span class="badge text-bg-primary mb-2">Administração</span>
        <h1 class="h2 mb-1">Novo contexto de aquisição</h1>
        <p class="text-body-secondary mb-0">Cadastre uma palavra-chave e os conteúdos que serão usados pela Home contextual.</p>
    </div>

    <form method="post" action="{{ route('admin.acquisition.contexts.store') }}">
        @csrf
        @include('admin.acquisition._form')
    </form>
</div>
@endsection
