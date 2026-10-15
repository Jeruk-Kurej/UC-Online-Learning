{{-- --- Page header card: badge, title, subtitle and an optional row of actions --- --}}
@props(['badge', 'title', 'subtitle', 'stackUntil' => 'lg'])

<section class="relative overflow-hidden rounded-xl border border-gray-200 bg-white px-6 py-6 shadow-sm md:px-8 mb-8 reveal-on-scroll">
    <div @class([
        'relative z-10 flex flex-col gap-4',
        'lg:flex-row lg:items-end lg:justify-between' => $stackUntil === 'lg',
        'md:flex-row md:items-end md:justify-between' => $stackUntil === 'md',
    ])>
        <div class="space-y-1">
            <span class="inline-flex items-center rounded-md border border-uco-orange-200 bg-uco-orange-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-uco-orange-700">
                {{ $badge }}
            </span>
            <h1 class="text-3xl font-extrabold text-soft-gray-900 md:text-4xl">{{ $title }}</h1>
            <p class="text-sm text-soft-gray-600 mt-1">{{ $subtitle }}</p>
        </div>
        @isset($actions)

            <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full lg:w-auto">
                {{ $actions }}
            </div>
        @endisset
    </div>
</section>
