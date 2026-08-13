@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'help' => null,
    'prefix' => null,
    'suffix' => null,
    'id' => null,
    'required' => false,
    'placeholder' => null,
])

@php
    $fieldId = $id ?? str_replace(['[', ']', '.'], ['_', '', '_'], $name);
    $describedBy = $help ? $fieldId.'-help' : null;
    $resolvedPlaceholder = $placeholder;

    if ($resolvedPlaceholder === null && $value === null && $type === 'number') {
        $resolvedPlaceholder = $suffix === '%' ? 'Ex.: 5' : ($suffix === 'horas' ? 'Ex.: 220' : ($suffix === 'dias' ? 'Ex.: 30' : 'Ex.: 10'));
    }
@endphp

<label class="form-label" for="{{ $fieldId }}">{{ $label }}</label>
@if ($prefix || $suffix)
    <div class="input-group">
        @if ($prefix)<span class="input-group-text">{{ $prefix }}</span>@endif
        <input
            {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)])->merge(['data-testid' => \App\Core\Quality\E2E\Support\TestId::field($name)]) }}
            type="{{ $type }}"
            id="{{ $fieldId }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            @if($resolvedPlaceholder !== null) placeholder="{{ $resolvedPlaceholder }}" @endif
            @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @required($required)
        >
        @if ($suffix)<span class="input-group-text">{{ $suffix }}</span>@endif
    </div>
@else
    <input
        {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)])->merge(['data-testid' => \App\Core\Quality\E2E\Support\TestId::field($name)]) }}
        type="{{ $type }}"
        id="{{ $fieldId }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        @if($resolvedPlaceholder !== null) placeholder="{{ $resolvedPlaceholder }}" @endif
        @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
        @required($required)
    >
@endif
@error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
@if ($help)<div id="{{ $fieldId }}-help" class="form-text">{{ $help }}</div>@endif
