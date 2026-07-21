@extends('layouts.app')

@section('title', 'Conversor Fiscal de XML — Prazzu Tools')
@section('meta_description', 'Extraia dados estruturados de NF-e e NFC-e com produtos, NCM, CFOP, impostos, totais e alertas de consistência.')

@section('content')
<div class="prazzu-page tool-page" data-tool="conversor-fiscal-xml">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.index') }}">Ferramentas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Conversor Fiscal de XML</li>
        </ol>
    </nav>

    <x-tools.intro icon="file-earmark-code" tone="green" title="Conversor Fiscal de XML" description="Extraia dados estruturados de NF-e e NFC-e com produtos, NCM, CFOP, impostos, totais e alertas de consistência." badge="Grátis" />

    <x-tool-feature-tiers slug="conversor-fiscal-xml" />
    <x-tools.validation-summary />

    @if (session('conversion_success'))
        <div class="alert alert-success">XML processado com sucesso. Confira abaixo os dados extraídos.</div>
    @endif

    <x-tools.form-panel title="Enviar NF-e ou NFC-e">
        <form method="POST" action="{{ route('tools.conversor-fiscal-xml.calculate') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="xml_file" class="form-label">Arquivo XML fiscal</label>
                <input class="form-control @error('xml_file') is-invalid @enderror" id="xml_file" name="xml_file" type="file" accept=".xml,text/xml,application/xml" required>
                <div class="form-text">NF-e modelo 55 ou NFC-e modelo 65, com até 10 MB. O arquivo não é armazenado.</div>
                @error('xml_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button class="btn btn-primary" type="submit">Ler XML</button>
        </form>
    </x-tools.form-panel>

    @if ($result)
        @php($totals = $result['totals'] ?? [])
        <x-tools.result-panel title="Documento fiscal">
            <div class="row g-3 mb-4">
                <div class="col-md-3"><strong>Modelo</strong><div>{{ $result['model'] }}</div></div>
                <div class="col-md-3"><strong>Número</strong><div>{{ $result['number'] ?: 'Não informado' }}</div></div>
                <div class="col-md-3"><strong>Série</strong><div>{{ $result['series'] ?: 'Não informada' }}</div></div>
                <div class="col-md-3"><strong>Emissão</strong><div>{{ $result['issued_at'] ?: 'Não informada' }}</div></div>
                <div class="col-12"><strong>Chave de acesso</strong><div class="text-break">{{ $result['access_key'] ?: 'Não localizada' }}</div></div>
            </div>

            @if (!empty($result['warnings']))
                <div class="alert alert-warning"><strong>Alertas:</strong><ul class="mb-0">@foreach($result['warnings'] as $warning)<li>{{ $warning }}</li>@endforeach</ul></div>
            @endif

            <div class="row g-3 mb-4">
                @foreach (['issuer' => 'Emitente', 'recipient' => 'Destinatário'] as $key => $label)
                    <div class="col-md-6"><div class="card h-100"><div class="card-body">
                        <h3 class="h6">{{ $label }}</h3>
                        <div>{{ data_get($result, "$key.name") ?: 'Não informado' }}</div>
                        <div>CPF/CNPJ: {{ data_get($result, "$key.tax_id") ?: 'Não informado' }}</div>
                        <div>IE: {{ data_get($result, "$key.state_registration") ?: 'Não informada' }}</div>
                    </div></div></div>
                @endforeach
            </div>

            <h3 class="h5">Totais</h3>
            <div class="row g-3 mb-4">
                @foreach (['products'=>'Produtos','freight'=>'Frete','discount'=>'Desconto','icms'=>'ICMS','ipi'=>'IPI','pis'=>'PIS','cofins'=>'Cofins','document'=>'Total da nota'] as $key => $label)
                    <div class="col-6 col-md-3"><div class="border rounded p-3 h-100"><small class="text-muted">{{ $label }}</small><div class="fw-semibold">R$ {{ number_format((float)($totals[$key] ?? 0), 2, ',', '.') }}</div></div></div>
                @endforeach
            </div>

            <h3 class="h5">Itens ({{ count($result['items'] ?? []) }})</h3>
            <div class="table-responsive"><table class="table table-sm align-middle">
                <thead><tr><th>#</th><th>Produto</th><th>NCM</th><th>CFOP</th><th>Qtd.</th><th>Unitário</th><th>Total</th><th>Tributos</th></tr></thead>
                <tbody>@foreach($result['items'] ?? [] as $item)<tr>
                    <td>{{ $item['number'] }}</td><td><strong>{{ $item['description'] }}</strong><br><small>{{ $item['code'] }}</small></td>
                    <td>{{ $item['ncm'] ?: '—' }}</td><td>{{ $item['cfop'] ?: '—' }}</td><td>{{ $item['quantity'] }} {{ $item['unit'] }}</td>
                    <td>R$ {{ number_format((float)$item['unit_value'], 2, ',', '.') }}</td><td>R$ {{ number_format((float)$item['total_value'], 2, ',', '.') }}</td>
                    <td><small>ICMS {{ $item['taxes']['icms'] }} · IPI {{ $item['taxes']['ipi'] }} · PIS {{ $item['taxes']['pis'] }} · Cofins {{ $item['taxes']['cofins'] }}</small></td>
                </tr>@endforeach</tbody>
            </table></div>
        </x-tools.result-panel>
    @endif

    <div class="alert alert-info mt-4">A leitura organiza os dados existentes no XML e não substitui a validação fiscal, a escrituração ou a conferência com os portais oficiais.</div>

    @if (session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif

    <x-tools.form-panel title="Processar XMLs em lote (Plus)">
        <form method="POST" action="{{ route('tools.conversor-fiscal-xml.batch') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="xml_files" class="form-label">Arquivos XML</label>
                <input class="form-control @error('xml_files') is-invalid @enderror" id="xml_files" name="xml_files[]" type="file" accept=".xml,text/xml,application/xml" multiple required>
                <div class="form-text">De 2 a 50 XMLs, com até 10 MB cada. Os arquivos originais não são armazenados.</div>
                @error('xml_files')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button class="btn btn-outline-primary" type="submit">Processar lote</button>
        </form>
    </x-tools.form-panel>

    @if (session('batch_success'))<div class="alert alert-success">Lote processado com sucesso.</div>@endif
    @php($batch = session('fiscal_xml_batch_result'))
    @if ($batch)
        <x-tools.result-panel title="Resumo do lote">
            <div class="row g-3 mb-3">
                @foreach(['received'=>'Recebidos','processed'=>'Processados','failed'=>'Falhas','items'=>'Itens'] as $key=>$label)
                    <div class="col-6 col-md-3"><div class="border rounded p-3"><small>{{ $label }}</small><div class="h5 mb-0">{{ data_get($batch, "summary.$key") }}</div></div></div>
                @endforeach
            </div>
            <p class="fw-semibold">Total dos documentos: R$ {{ number_format((float)data_get($batch, 'summary.document_total', 0), 2, ',', '.') }}</p>
            @if(!empty($batch['errors']))<div class="alert alert-warning"><ul class="mb-0">@foreach($batch['errors'] as $error)<li>{{ $error['file'] }}: {{ $error['message'] }}</li>@endforeach</ul></div>@endif
            <div class="d-flex flex-wrap gap-2">
                @foreach(['csv'=>'CSV','xlsx'=>'Excel','json'=>'JSON'] as $format=>$label)<a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.conversor-fiscal-xml.export', $format) }}">Exportar {{ $label }}</a>@endforeach
            </div>
        </x-tools.result-panel>
    @elseif ($result)
        <div class="d-flex flex-wrap gap-2 mb-3">@foreach(['csv'=>'CSV','xlsx'=>'Excel','json'=>'JSON'] as $format=>$label)<a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.conversor-fiscal-xml.export', $format) }}">Exportar {{ $label }}</a>@endforeach</div>
    @endif

    @auth
        <p><a href="{{ route('tools.conversor-fiscal-xml.history.index') }}">Ver histórico de processamentos</a></p>
    @endauth

</div>
@endsection
