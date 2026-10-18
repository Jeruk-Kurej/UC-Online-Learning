@props(['title'])

<div {{ $attributes->class(['rounded-lg border border-dashed border-gray-300 bg-white px-6 py-10 text-center']) }}>
    <p class="text-lg font-semibold text-gray-900">{{ $title }}</p>
    <p class="mx-auto mt-1 max-w-md text-base text-gray-600">{{ $slot }}</p>
    @isset($action)
        <div class="mt-5">{{ $action }}</div>
    @endisset
</div>
