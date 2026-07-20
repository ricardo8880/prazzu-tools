@props(['name', 'label', 'value' => '1', 'checked' => false, 'help' => null, 'id' => null])

@php($fieldId = $id ?? str_replace(['[', ']', '.'], ['_', '', '_'], $name))
<div class="form-check form-switch">
    <input
        {{ $attributes->class(['form-check-input']) }}
        type="checkbox"
        role="switch"
        id="{{ $fieldId }}"
        name="{{ $name }}"
        value="{{ $value }}"
        @checked(old($name, $checked))
    >
    <label class="form-check-label fw-semibold" for="{{ $fieldId }}">{{ $label }}</label>
</div>
@if ($help)<div class="form-text">{{ $help }}</div>@endif
