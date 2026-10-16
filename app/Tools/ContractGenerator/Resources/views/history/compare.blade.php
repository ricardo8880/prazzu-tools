@extends('layouts.app')

@section('title', 'Comparação de Versões — Contract Generator')

@section('content')
<div class="container py-5">
    <x-tools.page title="Version comparison" description="Compare lado a lado duas versões persistidas do mesmo gerador." icon="files" slug="contract-generator">
        <div class="alert {{ $changed ? 'alert-warning' : 'alert-success' }}">{{ $changed ? 'As versões possuem diferenças de conteúdo.' : 'As versões possuem o mesmo conteúdo textual.' }}</div>
        <div class="row g-4">
            <div class="col-12 col-lg-6">
                <h2 class="h5">Versão A — {{ $left->createdAt->format('d/m/Y H:i') }}</h2>
                <pre class="border rounded bg-body-tertiary p-3 text-wrap overflow-auto">{{ implode("\n", $left_lines) }}</pre>
            </div>
            <div class="col-12 col-lg-6">
                <h2 class="h5">Versão B — {{ $right->createdAt->format('d/m/Y H:i') }}</h2>
                <pre class="border rounded bg-body-tertiary p-3 text-wrap overflow-auto">{{ implode("\n", $right_lines) }}</pre>
            </div>
        </div>
        <a class="btn btn-outline-secondary mt-3" href="{{ route('tools.contract-generator.history.index') }}">Voltar ao histórico</a>
    </x-tools.page>
</div>
@endsection
