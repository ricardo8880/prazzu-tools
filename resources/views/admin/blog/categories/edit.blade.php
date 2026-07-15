@extends('layouts.app')

@section('title', 'Editar categoria do blog | Prazzu Tools')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <span class="badge text-bg-primary mb-2">Administração</span>
        <h1 class="h2 mb-1">Editar categoria</h1>
        <p class="text-body-secondary mb-0">{{ $category->name }}</p>
    </div>
    <form method="post" action="{{ route('admin.blog.categories.update', $category) }}">
        @csrf
        @method('PUT')
        @include('admin.blog.categories._form')
    </form>
</div>
@endsection
