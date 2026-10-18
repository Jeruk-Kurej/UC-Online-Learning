@props(['valueId' => null])

<div {{ $attributes->class(['card !p-4']) }}>
    <p class="text-sm text-gray-600">{{ $label }}</p>
    <p class="mt-1 font-serif text-3xl font-semibold text-brand-900" @if ($valueId) id="{{ $valueId }}" @endif>{{ $slot }}</p>
</div>
