{{-- --- A value the user can see but not change --- --}}
@props(['label', 'value'])

<div {{ $attributes }}>
    <p class="field-label">{{ $label }}</p>
    <p class="flex min-h-[2.75rem] items-center rounded-md border border-line bg-gray-100 px-3 text-base text-gray-800">{{ $value ?: 'Not set' }}</p>
</div>
