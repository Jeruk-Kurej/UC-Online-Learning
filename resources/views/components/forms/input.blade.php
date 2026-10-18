{{-- --- Labelled text-like input with its old value, hint and message; extra attributes go on the input --- --}}
@props(['name', 'label', 'value' => null, 'type' => 'text', 'required' => false, 'hint' => null, 'wrapperClass' => ''])

<x-forms.field :name="$name" :label="$label" :required="$required" :class="$wrapperClass">
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}" @required($required) {{ $attributes->class(['field-input', '!border-red-700' => $errors->has($name)]) }}>
    @if ($hint)
        <p class="field-hint">{{ $hint }}</p>
    @endif
</x-forms.field>
