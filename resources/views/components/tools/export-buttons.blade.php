@props(['pdfRoute', 'excelRoute', 'input' => []])
@php
$renderInputs = function (array $values, string $prefix = '') use (&$renderInputs): string {
    $html = '';
    foreach ($values as $key => $value) {
        $name = $prefix === '' ? (string) $key : $prefix.'['.$key.']';
        if (is_array($value)) {
            $html .= $renderInputs($value, $name);
            continue;
        }
        if ($value === null) {
            continue;
        }
        $normalized = is_bool($value) ? ($value ? '1' : '0') : (string) $value;
        $html .= '<input type="hidden" name="'.e($name).'" value="'.e($normalized).'">';
    }
    return $html;
};
@endphp
<div class="d-flex flex-wrap gap-2 mt-3" data-result-export-actions>
    <form method="POST" action="{{ $pdfRoute }}" data-file-download>
        @csrf
        {!! $renderInputs($input) !!}
        <button type="submit" class="btn btn-outline-danger">Exportar PDF</button>
    </form>
    <form method="POST" action="{{ $excelRoute }}" data-file-download>
        @csrf
        {!! $renderInputs($input) !!}
        <button type="submit" class="btn btn-outline-success">Baixar Excel</button>
    </form>
</div>
