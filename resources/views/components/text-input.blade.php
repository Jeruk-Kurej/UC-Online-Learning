{{-- --- Text input --- --}}
@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->class(['field-input']) }}>
