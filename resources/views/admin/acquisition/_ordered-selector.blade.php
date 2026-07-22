@php
    $indexedItems = collect($items)->keyBy($valueKey);
@endphp
<div class="card border-0 shadow-sm mb-4" data-ordered-selector data-input-name="{{ $name }}">
    <div class="card-header bg-transparent">
        <div class="fw-semibold">{{ $title }}</div>
        <div class="small text-body-secondary">{{ $description }}</div>
    </div>
    <div class="card-body">
        <div class="input-group mb-3">
            <select class="form-select" data-selector-options>
                <option value="">Selecione um conteúdo</option>
                @foreach ($items as $item)
                    <option value="{{ $item[$valueKey] }}">{{ $item[$labelKey] }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-primary" type="button" data-selector-add>Adicionar</button>
        </div>
        <div class="list-group" data-selector-list>
            @foreach ($selected as $selectedValue)
                @if ($indexedItems->has($selectedValue))
                    <div class="list-group-item d-flex align-items-center gap-2" data-selector-item data-value="{{ $selectedValue }}">
                        <input type="hidden" name="{{ $name }}[]" value="{{ $selectedValue }}">
                        <span class="badge text-bg-light border" data-position>{{ $loop->iteration }}</span>
                        <span class="flex-grow-1" data-label>{{ $indexedItems[$selectedValue][$labelKey] }}</span>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-up aria-label="Mover para cima"><i class="bi bi-arrow-up"></i></button>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-down aria-label="Mover para baixo"><i class="bi bi-arrow-down"></i></button>
                        <button class="btn btn-sm btn-outline-danger" type="button" data-remove aria-label="Remover"><i class="bi bi-x-lg"></i></button>
                    </div>
                @endif
            @endforeach
        </div>
        <p class="small text-body-secondary mb-0 mt-3">Use as setas para definir a ordem. Não há limite fixo de conteúdos.</p>
    </div>
</div>
