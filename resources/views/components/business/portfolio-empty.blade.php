{{-- --- Dashed placeholder on My Business when a section has nothing yet --- --}}
@props(['icon', 'title'])

<div class="reveal-on-scroll rounded-2xl border-2 border-dashed border-gray-200 bg-white p-16 text-center">
    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-soft-gray-50 text-soft-gray-300 shadow-inner">
        <i class="bi {{ $icon }} text-4xl"></i>
    </div>
    <h3 class="mb-2 text-xl font-bold text-gray-900">{{ $title }}</h3>
    <p class="mx-auto max-w-sm text-sm italic leading-relaxed text-gray-500">
        {{ $slot }}
    </p>
</div>
