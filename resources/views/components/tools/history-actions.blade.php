@props(['showUrl' => null, 'repeatUrl' => null, 'deleteUrl' => null, 'pdfUrl' => null, 'compact' => true])
@php($size = $compact ? ' btn-sm' : '')
<div {{ $attributes->class(['d-inline-flex flex-wrap justify-content-end gap-1']) }}>
    @if ($showUrl)<a class="btn{{ $size }} btn-outline-secondary" href="{{ $showUrl }}" aria-label="Ver detalhes"><i class="bi bi-eye"></i></a>@endif
    @if ($repeatUrl)<form method="post" action="{{ $repeatUrl }}">@csrf<button class="btn{{ $size }} btn-outline-primary" type="submit" aria-label="Repetir"><i class="bi bi-arrow-repeat"></i></button></form>@endif
    @if ($pdfUrl)<a class="btn{{ $size }} btn-outline-primary" target="_blank" rel="noopener" href="{{ $pdfUrl }}" aria-label="Exportar PDF"><i class="bi bi-file-earmark-pdf"></i></a>@endif
    @if ($deleteUrl)<form method="post" action="{{ $deleteUrl }}" onsubmit="return confirm('Excluir este registro?')">@csrf @method('DELETE')<button class="btn{{ $size }} btn-outline-danger" type="submit" aria-label="Excluir"><i class="bi bi-trash"></i></button></form>@endif
</div>
