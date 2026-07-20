@props([
    'name',
    'label',
    'options' => [],
    'value' => null,
    'placeholder' => null,
    'help' => null,
    'id' => null,
    'required' => false,
])

@php
    $fieldId = $id ?? str_replace(['[', ']', '.'], ['_', '', '_'], $name);
    $selectedValue = old($name, $value);
@endphp

<label class="form-label" for="{{ $fieldId }}">{{ $label }}</label>
<select
    {{ $attributes->class(['form-select', 'is-invalid' => $errors->has($name)]) }}
    id="{{ $fieldId }}"
    name="{{ $name }}"
    @required($required)
>
    @if ($placeholder !== null)<option value="">{{ $placeholder }}</option>@endif
    @foreach ($options as $optionValue => $optionLabel)
        <option value="{{ $optionValue }}" @selected((string) $selectedValue === (string) $optionValue)>{{ $optionLabel }}</option>
    @endforeach
</select>
@error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
@if ($help)<div class="form-text">{{ $help }}</div>@endif
