{{-- --- Input label --- --}}
@props(['value'])

<label {{ $attributes->class(['field-label']) }}>
    {{ $value ?? $slot }}
</label>
