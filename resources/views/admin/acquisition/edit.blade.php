@extends('layouts.app')

@section('title', 'Editar contexto de aquisição | Prazzu Tools')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <span class="badge text-bg-primary mb-2">Administração</span>
        <h1 class="h2 mb-1">Editar contexto de aquisição</h1>
        <p class="text-body-secondary mb-0">Atualize os conteúdos e controle se a jornada está ativa ou inativa.</p>
    </div>

    <form method="post" action="{{ route('admin.acquisition.contexts.update', $context['id']) }}">
        @csrf
        @method('PUT')
        @include('admin.acquisition._form')
    </form>
</div>
@endsection
