<section class="max-w-[1600px] mx-auto px-8 md:px-12 {{ $look['spacing'] }} space-y-16 relative overflow-hidden">
    @foreach($look['decor'] as $decor)
        <div class="{{ $decor }}"></div>
    @endforeach

    <div class="reveal-on-scroll flex flex-col md:flex-row md:items-end justify-between gap-10 relative z-10">
        <div class="space-y-4 relative">
            <div class="uco-outline-bg-text {{ $look['outlineTone'] }} uco-parallax-text">{{ $look['outlineText'] }}</div>
            <h2 class="text-5xl font-[900] text-gray-950 tracking-tighter uco-section-title {{ $look['title'] }} relative z-10">
                Our <span class="{{ $look['gradient'] }}">{{ $look['word'] }}</span> Students
            </h2>
            <p class="text-xl font-medium text-gray-500 max-w-2xl leading-relaxed mt-4 relative z-10">
                {{ $look['lead'] }}
            </p>
        </div>
        <div class="flex items-center gap-4 text-xs font-black text-gray-300 uppercase tracking-[0.25em]">
            <span class="w-12 h-[2px] bg-gray-100"></span>
            {{ $look['tag'] }}
        </div>
    </div>

    <div class="relative z-10 w-full" x-data="testimonyCarousel()" x-init="init()"
        @mouseenter="stopAutoScroll()" @mouseleave="startAutoScroll()">
        <div x-ref="track" @scroll.passive="updateScroll"
            class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth gap-6 pb-12 pt-4 items-stretch [&::-webkit-scrollbar]:hidden md:grid md:grid-cols-2 xl:grid-cols-3 md:overflow-visible md:snap-none md:gap-6 md:pb-0 md:pt-0 w-full relative z-10 scrollbar-none">
            @forelse($students as $student)
                <div data-carousel-slide
                    class="snap-start shrink-0 w-[min(100%,18rem)] sm:w-[calc(50%-0.75rem)] md:w-full md:shrink md:grow-0 flex h-auto">
                    <x-featured.student-card :student="$student" :type="$type" />
                </div>
            @empty
                <div
                    class="col-span-full uco-placeholder-mesh relative rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center w-full">
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
