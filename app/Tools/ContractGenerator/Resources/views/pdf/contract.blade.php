@php
    $lines = preg_split('/\R/u', str_replace("\r\n", "\n", trim($content))) ?: [];
    $firstTextLine = true;
@endphp

<section aria-label="Contrato" style="padding-top:34px;font-family:Arial,DejaVu Sans,sans-serif;color:#212529;">
    @foreach ($lines as $line)
        @php($line = rtrim($line))

        @if ($line === '')
            <div style="height:10px;"></div>
        @elseif ($firstTextLine)
            <div style="margin:0 0 18px;text-align:center;font-size:14px;line-height:1.35;font-weight:700;">
                {{ $line }}
            </div>
            @php($firstTextLine = false)
        @else
            <div style="margin:0 0 7px;text-align:left;font-size:12px;line-height:1.5;">
                {{ $line }}
            </div>
        @endif
    @endforeach
</section>
