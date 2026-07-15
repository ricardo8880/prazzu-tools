@extends('layouts.app')

@section('title', 'Nova postagem | Prazzu Tools')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <span class="badge text-bg-primary mb-2">Administração</span>
        <h1 class="h2 mb-1">Nova postagem</h1>
        <p class="text-body-secondary mb-0">Cadastre um novo conteúdo para o blog.</p>
    </div>
    <form data-blog-post-form data-blog-draft-key="new" data-blog-base-url="{{ url('/blog') }}" method="post" action="{{ route('admin.blog.posts.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.blog._form')
    </form>
</div>
@endsection
