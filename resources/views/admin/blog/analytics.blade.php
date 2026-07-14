@extends('layouts.app')
@section('title', 'Analytics do blog | Prazzu Tools')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div><span class="badge text-bg-primary mb-2">Administração</span><h1 class="h2 mb-1">Analytics do blog</h1><p class="text-body-secondary mb-0">Visitas aos artigos e cliques nas ferramentas relacionadas.</p></div>
        <a class="btn btn-outline-primary align-self-md-start" href="{{ route('admin.blog.posts.index') }}">Voltar às postagens</a>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-6"><div class="card h-100 border-0 shadow-sm"><div class="card-body"><span class="text-body-secondary">Visualizações de artigos</span><div class="display-6 fw-bold">{{ number_format((int) ($totals->views ?? 0), 0, ',', '.') }}</div></div></div></div>
        <div class="col-md-6"><div class="card h-100 border-0 shadow-sm"><div class="card-body"><span class="text-body-secondary">Cliques em ferramentas</span><div class="display-6 fw-bold">{{ number_format((int) ($totals->tool_clicks ?? 0), 0, ',', '.') }}</div></div></div></div>
    </div>
    <div class="row g-4">
        <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-header bg-transparent fw-semibold">Artigos mais acessados</div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Artigo</th><th class="text-end">Visitas</th></tr></thead><tbody>@forelse($posts as $item)<tr><td>{{ $item->subject_slug }}</td><td class="text-end">{{ $item->total }}</td></tr>@empty<tr><td colspan="2" class="text-center py-4 text-body-secondary">Sem dados ainda.</td></tr>@endforelse</tbody></table></div></div></div>
        <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-header bg-transparent fw-semibold">Ferramentas mais clicadas</div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Ferramenta</th><th class="text-end">Cliques</th></tr></thead><tbody>@forelse($tools as $item)<tr><td>{{ $item->tool_slug }}</td><td class="text-end">{{ $item->total }}</td></tr>@empty<tr><td colspan="2" class="text-center py-4 text-body-secondary">Sem dados ainda.</td></tr>@endforelse</tbody></table></div></div></div>
    </div>
</div>
@endsection
