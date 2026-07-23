@php
    $issuedAt = \Carbon\CarbonImmutable::parse($receipt['issued_at']);
    $payerDocument = $receipt['payer']['document'] ?? null;
    $payeeDocument = $receipt['payee']['document'] ?? null;
@endphp

<section aria-labelledby="receipt-document-title" style="padding-top: 42px;">
    <div style="display:flex;justify-content:space-between;gap:24px;align-items:flex-start;border-bottom:1px solid #adb5bd;padding-bottom:14px;margin-bottom:34px;">
        <div>
            <p class="print-muted" style="text-transform:uppercase;letter-spacing:.08em;">Recibo</p>
            <h2 id="receipt-document-title" style="border:0;margin:0;padding:0;font-size:22px;">Nº {{ $receipt['number'] }}</h2>
        </div>
        <div class="print-right">
            <span class="print-muted">Valor</span>
            <strong style="display:block;font-size:24px;white-space:nowrap;">{{ $receipt['amount'] }}</strong>
        </div>
    </div>

    <p style="font-size:16px;line-height:1.9;text-align:justify;">
        Recebi de <strong>{{ $receipt['payer']['name'] }}</strong>
        @if ($payerDocument)
            ({{ strtoupper((string) $receipt['payer']['document_type']) }} {{ $payerDocument }})
        @endif,
        a importância de <strong>{{ $receipt['amount_in_words'] }}</strong>, referente a
        <strong>{{ $receipt['description'] }}</strong>.
    </p>

    <p style="font-size:15px;margin-top:24px;">Para maior clareza, firmo o presente recibo.</p>

    <p style="margin-top:54px;font-size:14px;">
        {{ $receipt['city'] ? $receipt['city'].', ' : '' }}{{ $issuedAt->translatedFormat('d \\d\\e F \\d\\e Y') }}.
    </p>

    <div style="width:62%;margin:72px auto 0;text-align:center;border-top:1px solid #212529;padding-top:8px;">
        <strong style="font-size:15px;">{{ $receipt['payee']['name'] }}</strong>
        @if ($payeeDocument)
            <br>{{ strtoupper((string) $receipt['payee']['document_type']) }} {{ $payeeDocument }}
        @endif
    </div>

    <div class="print-footer" style="margin-top:72px;">
        Identificador do recibo: {{ $receipt['identifier'] }}
    </div>
</section>
