@props(['name', 'label', 'value' => null, 'help' => null, 'id' => null, 'required' => false, 'placeholder' => 'e.g. 5,000.00'])

<x-tools.form.input
    :name="$name"
    :label="$label"
    :value="$value"
    :help="$help"
    :id="$id"
    :required="$required"
    :placeholder="$placeholder"
    prefix="$"
    inputmode="decimal"
    {{ $attributes }}
/>
