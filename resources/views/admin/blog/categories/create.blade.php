@extends('layouts.app')

@section('title', 'Nova categoria do blog | Prazzu Tools')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <span class="badge text-bg-primary mb-2">Administração</span>
        <h1 class="h2 mb-1">Nova categoria</h1>
        <p class="text-body-secondary mb-0">Cadastre um assunto editorial para organizar as postagens.</p>
    </div>
    <form method="post" action="{{ route('admin.blog.categories.store') }}">
        @csrf
        @include('admin.blog.categories._form')
    </form>
</div>
@endsection
