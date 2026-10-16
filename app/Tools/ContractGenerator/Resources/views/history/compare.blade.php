@extends('layouts.app')

@section('title', 'Version Comparison — Contract Generator')

@section('content')
<div class="container py-5">
    <x-tools.page title="Version comparison" description="Compare two saved versions from the same generator side by side." icon="files" slug="contract-generator">
        <div class="alert {{ $changed ? 'alert-warning' : 'alert-success' }}">{{ $changed ? 'The versions have content differences.' : 'The versions have identical text content.' }}</div>
        <div class="row g-4">
            <div class="col-12 col-lg-6">
                <h2 class="h5">Version A — {{ $left->createdAt->format('M j, Y g:i A') }}</h2>
                <pre class="border rounded bg-body-tertiary p-3 text-wrap overflow-auto">{{ implode("\n", $left_lines) }}</pre>
            </div>
            <div class="col-12 col-lg-6">
                <h2 class="h5">Version B — {{ $right->createdAt->format('M j, Y g:i A') }}</h2>
                <pre class="border rounded bg-body-tertiary p-3 text-wrap overflow-auto">{{ implode("\n", $right_lines) }}</pre>
            </div>
        </div>
        <a class="btn btn-outline-secondary mt-3" href="{{ route('tools.contract-generator.history.index') }}">Back to history</a>
    </x-tools.page>
</div>
@endsection
