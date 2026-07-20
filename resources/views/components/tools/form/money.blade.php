@props(['name', 'label', 'value' => null, 'help' => null, 'id' => null, 'required' => false])

<x-tools.form.input
    :name="$name"
    :label="$label"
    :value="$value"
    :help="$help"
    :id="$id"
    :required="$required"
    prefix="R$"
    inputmode="decimal"
    {{ $attributes }}
/>
