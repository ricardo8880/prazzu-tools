@extends('layouts.app')

@section('title', 'Batch receipt generation')

@section('content')
<div class="container py-5" style="max-width: 920px;">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div><span class="badge text-bg-primary mb-2">Prazzu Plus</span><h1 class="h2 mb-2">Batch receipt generation</h1><p class="text-body-secondary mb-0">Upload up to 100 receipts in CSV format and generate one print-ready or PDF document.</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('tools.receipt-generator.index') }}">Back to receipt generator</a>
    </div>

    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
        <form method="post" action="{{ route('tools.receipt-generator.batch.issue') }}" enctype="multipart/form-data">
            @csrf
            <label class="form-label fw-semibold" for="file">CSV file</label>
            <input class="form-control" id="file" type="file" name="file" accept=".csv,.txt,text/csv" required>
            <div class="form-text">Maximum 2 MB and 100 rows. Accepted delimiters: semicolon, comma, or tab.</div>
            <button class="btn btn-primary mt-3" type="submit"><i class="bi bi-files me-1"></i> Generate receipts</button>
        </form>
    </div></div>

    <div class="card border-0 shadow-sm"><div class="card-body p-4">
        <h2 class="h5">Required headers</h2>
        <code class="d-block text-wrap">number;payer_name;payer_document_type;payer_document;payee_name;payee_document_type;payee_document;amount;description;issued_at;city</code>
        <p class="small text-body-secondary mt-3 mb-1">ID type and city may be left blank. ID types: <code>ein</code> or <code>id</code>. Date: <code>YYYY-MM-DD</code>. Amount: <code>1,000.00</code>.</p>
        <a class="small" download="receipt-template.csv" href="data:text/csv;charset=utf-8,number%3Bpayer_name%3Bpayer_document_type%3Bpayer_document%3Bpayee_name%3Bpayee_document_type%3Bpayee_document%3Bamount%3Bdescription%3Bissued_at%3Bcity%0AR-001%3BExample%20Client%3Bein%3B12-3456789%3BExample%20Provider%3Bein%3B98-7654321%3B250.00%3BProfessional%20services%3B2026-07-23%3BNew%20York">Download CSV template</a>
    </div></div>
</div>
@endsection
