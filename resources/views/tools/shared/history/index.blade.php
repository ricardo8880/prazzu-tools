@extends('layouts.app')

@section('title', 'Histórico — '.$tool->name.' | Prazzu Tools')
@section('meta_description', 'Consulte seus cálculos salvos em '.$tool->name.'.')
@section('canonical_url', route($routePrefix.'.history.index'))

@section('content')
<div class="prazzu-page">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('tools.index') }}">Ferramentas</a></li>
            <li class="breadcrumb-item"><a href="{{ route($tool->routeName) }}">{{ $tool->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Histórico</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <span class="badge text-bg-primary mb-2">Prazzu Plus</span>
            <h1 class="h2 mb-1">Histórico</h1>
            <p class="text-body-secondary mb-0">Resultados criptografados e disponíveis somente para sua conta.</p>
        </div>
        <a class="btn btn-primary" href="{{ route($tool->routeName) }}">Novo cálculo</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success" role="status">{{ session('status') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if ($historyPage->items === [])
                <x-tools.empty-state title="Nenhum cálculo salvo" description="Salve um resultado na ferramenta para consultá-lo aqui." />
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Referência</th>
                                <th>Salvo em</th>
                                <th>Versão</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($historyPage->items as $entry)
                                <tr>
                                    <td>{{ $entry->referenceDate->format('d/m/Y') }}</td>
                                    <td>{{ $entry->createdAt->format('d/m/Y H:i') }}</td>
                                    <td><span class="badge text-bg-light">{{ $entry->ruleVersion }}</span></td>
                                    <td class="text-end">
                                        <x-tools.history-actions
                                            :show-url="route($routePrefix.'.history.show', ['run' => $entry->id])"
                                            :repeat-url="route($routePrefix.'.history.repeat', ['run' => $entry->id])"
                                            :delete-url="route($routePrefix.'.history.destroy', ['run' => $entry->id])"
                                            :pdf-url="route($routePrefix.'.history.export', ['run' => $entry->id, 'format' => 'pdf'])"
                                        />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @if ($historyPage->lastPage > 1)
        <nav class="d-flex justify-content-center gap-2 mt-4" aria-label="Paginação do histórico">
            @if ($historyPage->page > 1)
                <a class="btn btn-outline-secondary" href="{{ request()->fullUrlWithQuery(['page' => $historyPage->page - 1]) }}">Anterior</a>
            @endif
            <span class="btn disabled" aria-current="page">Página {{ $historyPage->page }} de {{ $historyPage->lastPage }}</span>
            @if ($historyPage->page < $historyPage->lastPage)
                <a class="btn btn-outline-secondary" href="{{ request()->fullUrlWithQuery(['page' => $historyPage->page + 1]) }}">Próxima</a>
            @endif
        </nav>
    @endif
</div>
@endsection
