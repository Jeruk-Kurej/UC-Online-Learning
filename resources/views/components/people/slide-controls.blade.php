{{-- --- Arrows and dots of a profile carousel (activeIndex / total in the parent x-data); $afterMove runs after each move --- --}}
@props(['layer', 'afterMove' => ''])

<template x-if="total > 1">
    <div>
        <button @click="activeIndex = (activeIndex - 1 + total) % total{{ $afterMove }}"
                @class([
                    $layer,
                    'absolute left-3 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200/60 bg-white/90 text-slate-800 opacity-0 shadow-sm backdrop-blur-sm transition duration-200 hover:bg-white group-hover:opacity-100',
                ])>
            <i class="bi bi-chevron-left text-sm"></i>
        </button>
        <button @click="activeIndex = (activeIndex + 1) % total{{ $afterMove }}"
                @class([
                    $layer,
                    'absolute right-3 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200/60 bg-white/90 text-slate-800 opacity-0 shadow-sm backdrop-blur-sm transition duration-200 hover:bg-white group-hover:opacity-100',
                ])>
            <i class="bi bi-chevron-right text-sm"></i>
        </button>
    </div>
</template>

<template x-if="total > 1">
    <div @class([
        $layer,
        'absolute bottom-3 left-1/2 flex -translate-x-1/2 gap-1.5',
    ])>
        <template x-for="(item, index) in total" :key="index">
            <button @click="activeIndex = index{{ $afterMove }}"
                    class="h-1.5 w-1.5 rounded-full transition-all duration-300"
                    :class="activeIndex === index ? 'bg-[#f7931e] w-4' : 'bg-slate-300'"></button>
        </template>
    </div>
</template>
