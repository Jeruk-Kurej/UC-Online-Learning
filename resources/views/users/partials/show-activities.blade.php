{{-- --- Profile activities: autoplay photo carousel with caption, or the testimony card when there are no photos --- --}}
<div class="border-t border-slate-100 pt-6">
    @if (count($user->activity_gallery_urls) > 0)
        {{-- --- Sleek Carousel --- --}}
        <div x-data="autoplayCarousel({{ count($user->activity_gallery_urls) }})" class="group relative h-[200px] w-full overflow-hidden rounded-2xl bg-slate-50 md:h-[240px]">
            {{-- --- Slides --- --}}
            <div class="relative flex h-full w-full items-center justify-center">
                @foreach ($user->activity_gallery_urls as $index => $url)
                    <div x-show="activeIndex === {{ $index }}"
                         class="absolute inset-0 z-10 h-full w-full"
                         @if ($index !== 0) x-cloak @endif>
                        <div @click="lightboxUrl = '{{ $url }}'; showLightbox = true"
                             class="relative block h-full w-full cursor-zoom-in transition-all duration-300 hover:scale-[1.02]">
                            <x-premium-image :src="$url" alt="Activity Image" class="h-full w-full" />
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- --- Navigation Arrows --- --}}
            <template x-if="total > 1">
                <div>
                    <button @click="activeIndex = (activeIndex - 1 + total) % total; resetAutoplay()"
                            class="absolute left-3 top-1/2 z-10 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200/60 bg-white/90 text-slate-800 opacity-0 shadow-sm backdrop-blur-sm transition duration-200 hover:bg-white group-hover:opacity-100">
                        <i class="bi bi-chevron-left text-sm"></i>
                    </button>
                    <button @click="activeIndex = (activeIndex + 1) % total; resetAutoplay()"
                            class="absolute right-3 top-1/2 z-10 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200/60 bg-white/90 text-slate-800 opacity-0 shadow-sm backdrop-blur-sm transition duration-200 hover:bg-white group-hover:opacity-100">
                        <i class="bi bi-chevron-right text-sm"></i>
                    </button>
                </div>
            </template>

            {{-- --- Dots Indicators --- --}}
            <template x-if="total > 1">
                <div class="absolute bottom-3 left-1/2 z-10 flex -translate-x-1/2 gap-1.5">
                    <template x-for="(item, index) in total" :key="index">
                        <button @click="activeIndex = index; resetAutoplay()"
                                class="h-1.5 w-1.5 rounded-full transition-all duration-300"
                                :class="activeIndex === index ? 'bg-[#f7931e] w-4' : 'bg-slate-300'"></button>
                    </template>
                </div>
            </template>

            {{-- --- Label --- --}}
            <div class="absolute left-3 top-3 z-10 flex items-center gap-1.5 rounded-md border border-slate-200/60 bg-white/90 px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-slate-700 shadow-sm backdrop-blur-md">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-[#f7931e]"></span>
                Activities
            </div>
        </div>
        @if ($user->activities_caption)
            <div class="mt-3 px-1">
                <p class="text-xs font-medium italic leading-relaxed text-slate-500">
                    "{{ $user->activities_caption }}"
                </p>
            </div>
        @endif
    @else
        {{-- --- Fallback Card: Premium testimonial styling --- --}}
        <div class="relative flex h-[200px] w-full flex-col justify-between overflow-hidden rounded-2xl bg-slate-50/50 p-6 md:h-[240px]">
            <div class="pointer-events-none absolute right-0 top-0 -translate-y-8 translate-x-8 transform select-none opacity-[0.05]">
                <i class="bi bi-quote text-[160px] text-[#f7931e]"></i>
            </div>
            <div class="relative z-10 space-y-4">
                <div class="flex items-center gap-1.5">
                    <span class="h-4 w-1.5 rounded-full bg-[#f7931e]"></span>
                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">Student Testimony</span>
                </div>
                <p class="line-clamp-4 text-xs font-medium italic leading-relaxed text-slate-600">
                    "{{ $user->testimony ?: 'Universitas Ciputra Online Learning helps me develop business capabilities and entrepreneurial mindset while managing my day-to-day operations.' }}"
                </p>
            </div>
            <div class="relative z-10 flex items-center gap-2 border-t border-slate-200 pt-3">
                <div class="flex h-6 w-6 items-center justify-center rounded-full border border-orange-100 bg-orange-50 text-[10px] font-black text-orange-600">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <p class="text-[10px] font-bold text-slate-500">— {{ $user->name }}</p>
            </div>
        </div>
    @endif
</div>
