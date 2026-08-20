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
    $describedByIds = [];
    if ($errors->has($name)) {
        $describedByIds[] = $fieldId.'-error';
    }
    if ($help) {
        $describedByIds[] = $fieldId.'-help';
    }
    $describedBy = $describedByIds === [] ? null : implode(' ', $describedByIds);
@endphp

<label class="form-label" for="{{ $fieldId }}">{{ $label }}</label>
<select
    {{ $attributes->class(['form-select', 'is-invalid' => $errors->has($name)])->merge(['data-testid' => \App\Core\Quality\E2E\Support\TestId::field($name)]) }}
    id="{{ $fieldId }}"
    name="{{ $name }}"
    @if($errors->has($name)) aria-invalid="true" @endif
    @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
    @required($required)
>
    @if ($placeholder !== null)<option value="">{{ $placeholder }}</option>@endif
    @foreach ($options as $optionValue => $optionLabel)
        <option value="{{ $optionValue }}" @selected((string) $selectedValue === (string) $optionValue)>{{ $optionLabel }}</option>
    @endforeach
</select>
@error($name)<div id="{{ $fieldId }}-error" class="invalid-feedback">{{ $message }}</div>@enderror
@if ($help)<div id="{{ $fieldId }}-help" class="form-text">{{ $help }}</div>@endif
