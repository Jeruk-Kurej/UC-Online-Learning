{{-- --- Dropdown in a list's filter bar; glass is the skin for a dark band or a photo; the width comes from the page --- --}}
@props(['glass' => false])

<select {{ $attributes->class(['field-input cursor-pointer', 'field-glass' => $glass]) }}>
    {{ $slot }}
</select>
