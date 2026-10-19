{{-- --- Dropdown in a list's filter bar; the width comes from the page --- --}}
<select {{ $attributes->class(['field-input cursor-pointer']) }}>
    {{ $slot }}
</select>
