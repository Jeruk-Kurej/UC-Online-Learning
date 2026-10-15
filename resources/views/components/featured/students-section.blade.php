<section class="{{ $look['spacing'] }} relative mx-auto max-w-[1600px] space-y-16 overflow-hidden px-8 md:px-12">
    @foreach ($look['decor'] as $decor)
        <div class="{{ $decor }}"></div>
    @endforeach

    <div class="reveal-on-scroll relative z-10 flex flex-col justify-between gap-10 md:flex-row md:items-end">
        <div class="relative space-y-4">
            <div class="uco-outline-bg-text {{ $look['outlineTone'] }} uco-parallax-text">{{ $look['outlineText'] }}</div>
            <h2 class="uco-section-title {{ $look['title'] }} relative z-10 text-5xl font-[900] tracking-tighter text-gray-950">
                Our <span class="{{ $look['gradient'] }}">{{ $look['word'] }}</span> Students
            </h2>
            <p class="relative z-10 mt-4 max-w-2xl text-xl font-medium leading-relaxed text-gray-500">
                {{ $look['lead'] }}
            </p>
        </div>
        <div class="flex items-center gap-4 text-xs font-black uppercase tracking-[0.25em] text-gray-300">
            <span class="h-[2px] w-12 bg-gray-100"></span>
            {{ $look['tag'] }}
        </div>
    </div>

    <div class="relative z-10 w-full" x-data="testimonyCarousel()" x-init="init()"
         @mouseenter="stopAutoScroll()" @mouseleave="startAutoScroll()">
        <div x-ref="track" @scroll.passive="updateScroll"
             class="scrollbar-none relative z-10 flex w-full snap-x snap-mandatory items-stretch gap-6 overflow-x-auto scroll-smooth pb-12 pt-4 md:grid md:snap-none md:grid-cols-2 md:gap-6 md:overflow-visible md:pb-0 md:pt-0 xl:grid-cols-3 [&::-webkit-scrollbar]:hidden">
            @forelse($students as $student)
                <div data-carousel-slide
                     class="flex h-auto w-[min(100%,18rem)] shrink-0 snap-start sm:w-[calc(50%-0.75rem)] md:w-full md:shrink md:grow-0">
                    <x-featured.student-card :student="$student" :type="$type" />
                </div>
            @empty
                <div
                     class="uco-placeholder-mesh relative col-span-full w-full rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center">
                    <div class="relative z-10 space-y-4">
                        <div
                             class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                            <i class="bi {{ $look['emptyIcon'] }}"></i>
                        </div>
                        <div class="space-y-1">
                            <p class="text-lg font-black text-gray-900">No Featured {{ $look['word'] }} Students</p>
                            <p class="text-sm font-medium text-gray-500">Featured {{ $look['emptyNoun'] }} profiles will
                                appear here once curated by admin.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <x-featured.carousel-dots mobile-only />
    </div>
</section>
