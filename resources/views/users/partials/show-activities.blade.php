{{-- --- Profile activities: autoplay photo carousel with caption, or the testimony card when there are no photos --- --}}
<div class="pt-6 border-t border-slate-100">
    @if(count($user->activity_gallery_urls) > 0)
        {{-- Sleek Carousel --}}
        <div x-data="autoplayCarousel({{ count($user->activity_gallery_urls) }})" class="relative w-full h-[200px] md:h-[240px] bg-slate-50 rounded-2xl overflow-hidden group">
            <!-- Slides -->
            <div class="w-full h-full relative flex items-center justify-center">
                @foreach($user->activity_gallery_urls as $index => $url)
                    <div x-show="activeIndex === {{ $index }}"
                         class="absolute inset-0 w-full h-full z-10"
                         @if($index !== 0) x-cloak @endif>
                        <div @click="lightboxUrl = '{{ $url }}'; showLightbox = true" 
                             class="w-full h-full cursor-zoom-in relative block transition-all duration-300 hover:scale-[1.02]">
                            <x-premium-image :src="$url" alt="Activity Image" class="w-full h-full" />
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Navigation Arrows -->
            <template x-if="total > 1">
                <div>
                    <button @click="activeIndex = (activeIndex - 1 + total) % total; resetAutoplay()" 
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-slate-800 flex items-center justify-center transition backdrop-blur-sm z-10 border border-slate-200/60 shadow-sm opacity-0 group-hover:opacity-100 duration-200">
                        <i class="bi bi-chevron-left text-sm"></i>
                    </button>
                    <button @click="activeIndex = (activeIndex + 1) % total; resetAutoplay()" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-slate-800 flex items-center justify-center transition backdrop-blur-sm z-10 border border-slate-200/60 shadow-sm opacity-0 group-hover:opacity-100 duration-200">
                        <i class="bi bi-chevron-right text-sm"></i>
                    </button>
                </div>
            </template>

            <!-- Dots Indicators -->
            <template x-if="total > 1">
                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                    <template x-for="(item, index) in total" :key="index">
                        <button @click="activeIndex = index; resetAutoplay()" 
                                class="w-1.5 h-1.5 rounded-full transition-all duration-300"
                                :class="activeIndex === index ? 'bg-[#f7931e] w-4' : 'bg-slate-300'"></button>
                    </template>
                </div>
            </template>

            <!-- Label -->
            <div class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-white/90 backdrop-blur-md text-[9px] font-black uppercase tracking-widest text-slate-700 border border-slate-200/60 shadow-sm z-10 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 bg-[#f7931e] rounded-full animate-pulse"></span>
                Activities
            </div>
        </div>
        @if($user->activities_caption)
            <div class="mt-3 px-1">
                <p class="text-xs text-slate-500 font-medium leading-relaxed italic">
                    "{{ $user->activities_caption }}"
                </p>
            </div>
        @endif
    @else
        {{-- Fallback Card: Premium testimonial styling --}}
        <div class="w-full h-[200px] md:h-[240px] flex flex-col justify-between p-6 bg-slate-50/50 rounded-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 transform translate-x-8 -translate-y-8 opacity-[0.05] pointer-events-none select-none">
                <i class="bi bi-quote text-[160px] text-[#f7931e]"></i>
            </div>
            <div class="relative z-10 space-y-4">
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-4 bg-[#f7931e] rounded-full"></span>
                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">Student Testimony</span>
                </div>
                <p class="text-xs text-slate-600 font-medium leading-relaxed italic line-clamp-4">
                    "{{ $user->testimony ?: 'Universitas Ciputra Online Learning helps me develop business capabilities and entrepreneurial mindset while managing my day-to-day operations.' }}"
                </p>
            </div>
            <div class="relative z-10 flex items-center gap-2 pt-3 border-t border-slate-200">
                <div class="w-6 h-6 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center text-[10px] font-black border border-orange-100">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <p class="text-[10px] font-bold text-slate-500">— {{ $user->name }}</p>
            </div>
        </div>
    @endif
</div>
