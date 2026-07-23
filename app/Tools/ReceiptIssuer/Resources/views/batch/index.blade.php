@extends('layouts.app')

@section('title', 'Geração de recibos em lote')

@section('content')
<div class="container py-5" style="max-width: 920px;">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div><span class="badge text-bg-primary mb-2">Prazzu Plus</span><h1 class="h2 mb-2">Geração de recibos em lote</h1><p class="text-body-secondary mb-0">Envie até 100 recibos em CSV e gere um único documento pronto para impressão ou PDF.</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('tools.emissor-de-recibos.index') }}">Voltar ao emissor</a>
    </div>

    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
        <form method="post" action="{{ route('tools.emissor-de-recibos.batch.issue') }}" enctype="multipart/form-data">
            @csrf
            <label class="form-label fw-semibold" for="file">Arquivo CSV</label>
            <input class="form-control" id="file" type="file" name="file" accept=".csv,.txt,text/csv" required>
            <div class="form-text">Máximo de 2 MB e 100 linhas. Separadores aceitos: ponto e vírgula, vírgula ou tabulação.</div>
            <button class="btn btn-primary mt-3" type="submit"><i class="bi bi-files me-1"></i> Gerar recibos</button>
        </form>
    </div></div>

    <div class="card border-0 shadow-sm"><div class="card-body p-4">
        <h2 class="h5">Cabeçalhos obrigatórios</h2>
        <code class="d-block text-wrap">number;payer_name;payer_document_type;payer_document;payee_name;payee_document_type;payee_document;amount;description;issued_at;city</code>
        <p class="small text-body-secondary mt-3 mb-1">Documentos e cidade podem ficar vazios. Tipos de documento: <code>cpf</code> ou <code>cnpj</code>. Data: <code>AAAA-MM-DD</code>. Valor: <code>1.000,00</code>.</p>
        <a class="small" download="modelo-recibos.csv" href="data:text/csv;charset=utf-8,number%3Bpayer_name%3Bpayer_document_type%3Bpayer_document%3Bpayee_name%3Bpayee_document_type%3Bpayee_document%3Bamount%3Bdescription%3Bissued_at%3Bcity%0AR-001%3BCliente%20Exemplo%3Bcpf%3B52998224725%3BPrestador%20Exemplo%3Bcpf%3B11144477735%3B250%2C00%3BServi%C3%A7os%20prestados%3B2026-07-23%3BS%C3%A3o%20Paulo">Baixar modelo CSV</a>
    </div></div>
</div>
@endsection
