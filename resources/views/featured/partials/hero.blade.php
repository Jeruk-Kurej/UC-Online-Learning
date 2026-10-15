{{-- --- Home hero: headline, call to action and the spotlight grid of featured ventures --- --}}
<section class="group relative overflow-hidden uco-hero-section-bleed py-12 md:py-16 reveal-on-scroll">
    {{-- --- Background Effects with Gradient Mask --- --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none uco-hero-mask">
        <div class="uco-hero-mesh opacity-90"></div>
        <div class="uco-noise-overlay"></div>

        {{-- --- Dynamic Floating Orbs with Subtle Motion --- --}}
        <div
            class="uco-float-orb uco-float-orb--one opacity-40  transition-transform duration-[10s] group-hover:translate-x-12 group-hover:-translate-y-8">
        </div>
        <div
            class="uco-float-orb uco-float-orb--two opacity-30  transition-transform duration-[12s] group-hover:-translate-x-16 group-hover:translate-y-12">
        </div>
    </div>

    <div class="relative z-10 max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-8 items-start">
        {{-- --- Left Content --- --}}
        <div class="space-y-10 md:col-span-7 lg:col-span-7">

            <div class="space-y-8">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-[900] text-gray-950 tracking-[-0.04em] leading-[1.2] md:leading-[1.4] max-w-4xl"
                    x-data="{
                        words: ['Innovative', 'Sustainable', 'Transformative', 'Pioneering'],
                        currentWord: 0,
                        isAnimating: false
                    }" x-init="setInterval(() => {
                        isAnimating = true;
                        setTimeout(() => {
                            currentWord = (currentWord + 1) % words.length;
                            isAnimating = false;
                        }, 300);
                    }, 2300)">
                    Discover <span
                        class="uco-text-gradient-orange inline-block min-w-[130px] sm:min-w-[180px] md:min-w-[280px] pb-2"
                        :class="isAnimating ? 'word-rotate-exit' : 'word-rotate-enter'"
                        x-text="words[currentWord]"></span>
                    <br class="hidden md:inline">
                    Businesses from <span class="whitespace-nowrap italic">UCO Community</span>
                </h1>
                <p class="max-w-lg text-lg font-medium leading-relaxed text-gray-600/80 tracking-tight">
                    Explore a vibrant ecosystem of student-led ventures. Turning potential into market-ready impact.
                </p>
            </div>

            <div class="pt-6">
                <a href="{{ route('businesses.index') }}"
                    class="group/btn inline-flex items-center justify-center gap-4 w-full sm:w-auto rounded-[1.8rem] bg-uco-orange-600 px-8 sm:px-12 py-4 sm:py-5 text-base sm:text-lg font-black text-white shadow-[0_25px_60px_rgba(247,147,30,0.25)] transition-all hover:bg-uco-orange-700 hover:scale-[1.03] active:scale-95">
                    Explore Business
                    <i class="bi bi-arrow-right text-lg sm:text-xl transition-transform group-hover/btn:translate-x-2"></i>
                </a>
            </div>
        </div>

        {{-- --- Right Content: Immersive Spotlight Grid (2x2) --- --}}
        <div class="md:col-span-5 lg:col-span-5">
            <div class="grid grid-cols-2 gap-6">
                @foreach ($spotlightBusinesses->take(4) as $index => $business)
                    <div class="group/card relative {{ $index % 2 == 1 ? 'mt-8' : '' }}">
                        <a href="{{ route('businesses.show', $business) }}"
                            class="block overflow-hidden transition-all duration-700 hover:-translate-y-2">

                            <div class="space-y-3">
                                {{-- --- Compact Showcase Image --- --}}
                                <div
                                    class="relative aspect-video w-full overflow-hidden rounded-sm bg-white border border-gray-100 shadow-sm">
                                    @if ($business->products->first()?->photo_url)
                                        <x-premium-image :src="$business->products->first()->photo_url" class="h-full w-full" />
                                    @else
                                        <div
                                            class="uco-placeholder-mesh flex h-full w-full items-center justify-center">
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-0 blur-xl bg-uco-orange-200/20 rounded-full">
                                                </div>
                                                <i
                                                    class="bi bi-rocket-takeoff text-2xl text-uco-orange-300/60 relative z-10"></i>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- --- Logo Overlay --- --}}
                                    <div
                                        class="absolute bottom-2 left-3 h-8 w-8 overflow-hidden rounded-lg bg-white p-1 shadow-lg">
                                        @if ($business->logo_url)
                                            <img src="{{ $business->logo_url }}"
                                                class="h-full w-full object-contain">
                                        @else
                                            <div
                                                class="flex h-full w-full items-center justify-center text-[10px] font-black text-uco-orange-400">
                                                {{ strtoupper(substr($business->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- --- Content --- --}}
                                <div class="px-3 pb-2">
                                    <h4
                                        class="text-[15px] font-[900] text-gray-950 truncate tracking-tight group-hover/card:text-uco-orange-600 transition-colors">
                                        {{ $business->name }}
                                    </h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div
                                            class="h-4 w-4 overflow-hidden rounded-full border border-uco-orange-100">
                                            <img src="{{ $business->public_owner->profile_photo_url }}"
                                                class="h-full w-full object-contain bg-slate-50">
                                        </div>
                                        <span
                                            class="text-[9px] font-bold text-gray-400 uppercase tracking-widest truncate max-w-[80px]">
                                            {{ $business->public_owner->full_titled_name ?? 'Founder' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- --- Mobile-only bottom button --- --}}
        <div class="col-span-full pt-4 block md:hidden text-center">
            <a href="{{ route('businesses.index') }}"
                class="group/btn inline-flex items-center justify-center gap-6 rounded-[1.8rem] bg-uco-orange-600 px-12 py-5 text-lg font-black text-white shadow-[0_25px_60px_rgba(247,147,30,0.25)] transition-all hover:bg-uco-orange-700 hover:scale-[1.03] active:scale-95 w-full">
                Explore Business
                <i class="bi bi-arrow-right text-xl transition-transform group-hover/btn:translate-x-2"></i>
            </a>
        </div>
    </div>
</section>
