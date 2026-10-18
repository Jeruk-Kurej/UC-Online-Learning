{{-- --- Placeholder card for an inbox tab with nothing in it --- --}}
@props(['icon', 'title'])

<div class="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-600">
        <i class="bi {{ $icon }} text-2xl"></i>
    </div>
    <h3 class="mb-1 text-base font-bold text-gray-800">{{ $title }}</h3>
    <p class="text-sm text-gray-600">{{ $slot }}</p>
</div>
