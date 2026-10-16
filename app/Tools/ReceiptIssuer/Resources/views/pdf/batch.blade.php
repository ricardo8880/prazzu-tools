@if($receipts === [])
    <div class="alert alert-warning">No valid receipts were found in the file.</div>
@endif

@foreach($receipts as $receipt)
    <div style="{{ !$loop->first ? 'break-before:page;page-break-before:always;' : '' }}">
        @include('tools-receipt-generator::pdf.receipt', ['receipt' => $receipt])
    </div>
@endforeach

@if($errors !== [])
<section style="break-before:page;page-break-before:always;padding-top:32px;">
    <h2>Rows not generated</h2>
    <p class="print-muted">Correct these rows in the CSV and upload it again.</p>
    <table style="width:100%;border-collapse:collapse;">
        <thead><tr><th style="text-align:left;border-bottom:1px solid #adb5bd;padding:8px;">Row</th><th style="text-align:left;border-bottom:1px solid #adb5bd;padding:8px;">Reason</th></tr></thead>
        <tbody>@foreach($errors as $error)<tr><td style="padding:8px;border-bottom:1px solid #dee2e6;">{{ $error['line'] }}</td><td style="padding:8px;border-bottom:1px solid #dee2e6;">{{ $error['message'] }}</td></tr>@endforeach</tbody>
    </table>
</section>
@endif
