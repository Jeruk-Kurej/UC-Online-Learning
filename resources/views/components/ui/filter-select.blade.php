{{-- --- Dropdown in a list's filter bar; sizes and padding come from the page --- --}}
<select {{ $attributes->class(['rounded-md border-gray-300 bg-white py-2 shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500']) }}>
    {{ $slot }}
</select>
