@extends('layouts.app')

@section('title', 'Editar postagem | Prazzu Tools')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <span class="badge text-bg-primary mb-2">Administração</span>
            <h1 class="h2 mb-1">Editar postagem</h1>
            <p class="text-body-secondary mb-0">{{ $post->title }}</p>
        </div>
        <div class="d-flex gap-2 align-self-md-start">
            <a class="btn btn-outline-secondary" href="{{ route('admin.blog.posts.preview', $post) }}" target="_blank"><i class="bi bi-eye me-1"></i> Pré-visualizar</a>
            @if ($post->isPubliclyAvailable())
                <a class="btn btn-outline-primary" href="{{ route('blog.show', $post->slug) }}" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i> Ver publicação</a>
            @endif
        </div>
    </div>
    <form data-blog-post-form data-blog-draft-key="{{ $post->getKey() }}" data-blog-base-url="{{ url('/blog') }}" method="post" action="{{ route('admin.blog.posts.update', $post) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.blog._form')
    </form>
</div>
@endsection
