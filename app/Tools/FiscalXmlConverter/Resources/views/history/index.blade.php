<x-tools.page :tool="app(\App\Tools\FiscalXmlConverter\Tool::class)->manifest()">
    <h1 class="h3 mb-4">Histórico do Conversor Fiscal de XML</h1>
    @if(session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif
    <div class="card"><div class="card-body">
        @forelse($runs as $run)
            <div class="border-bottom py-3 d-flex flex-wrap justify-content-between gap-3">
                <div><strong>{{ ($run->input['mode'] ?? 'single') === 'batch' ? 'Processamento em lote' : 'Documento individual' }}</strong><br><small class="text-muted">{{ $run->createdAt->format('d/m/Y H:i') }}</small></div>
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('tools.conversor-fiscal-xml.history.repeat', $run->id) }}">@csrf<button class="btn btn-sm btn-outline-primary">Reabrir</button></form>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.conversor-fiscal-xml.history.export', [$run->id, 'csv']) }}">CSV</a>
                    <form method="POST" action="{{ route('tools.conversor-fiscal-xml.history.destroy', $run->id) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Excluir</button></form>
                </div>
            </div>
        @empty
            <p class="mb-0 text-muted">Nenhum processamento salvo.</p>
        @endforelse
    </div></div>
    <div class="mt-3">{{ $runs->links() }}</div>
</x-tools.page>
