{{-- --- Dashed placeholder a Featured section shows while the admin has featured nothing for it --- --}}
@props(['icon', 'title'])

<div
     class="uco-placeholder-mesh relative col-span-full w-full rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center">
    <div class="relative z-10 space-y-4">
        <div
             class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
            <i class="bi {{ $icon }}"></i>
        </div>
        <div class="space-y-1">
            <p class="text-lg font-bold text-gray-900">{{ $title }}</p>
            <p class="text-sm font-medium text-gray-600">{{ $slot }}</p>
        </div>
    </div>
</div>
