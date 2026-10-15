{{-- --- Home hero: headline, call to action and the spotlight grid of featured ventures --- --}}
<section class="uco-hero-section-bleed reveal-on-scroll group relative overflow-hidden py-12 md:py-16">
    {{-- --- Background Effects with Gradient Mask --- --}}
    <div class="uco-hero-mask pointer-events-none absolute inset-0 overflow-hidden">
        <div class="uco-hero-mesh opacity-90"></div>
        <div class="uco-noise-overlay"></div>

        {{-- --- Dynamic Floating Orbs with Subtle Motion --- --}}
        <div
             class="uco-float-orb uco-float-orb--one opacity-40 transition-transform duration-[10s] group-hover:-translate-y-8 group-hover:translate-x-12">
        </div>
        <div
             class="uco-float-orb uco-float-orb--two opacity-30 transition-transform duration-[12s] group-hover:-translate-x-16 group-hover:translate-y-12">
        </div>
    </div>

    <div class="relative z-10 mx-auto grid max-w-[1600px] grid-cols-1 items-start gap-8 px-4 sm:px-6 md:grid-cols-12 lg:gap-8 lg:px-8">
        {{-- --- Left Content --- --}}
        <div class="space-y-10 md:col-span-7 lg:col-span-7">

            <div class="space-y-8">
                <h1 class="max-w-4xl text-3xl font-[900] leading-[1.2] tracking-[-0.04em] text-gray-950 sm:text-4xl md:text-5xl md:leading-[1.4] lg:text-6xl"
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
                          class="uco-text-gradient-orange inline-block min-w-[130px] pb-2 sm:min-w-[180px] md:min-w-[280px]"
                          :class="isAnimating ? 'word-rotate-exit' : 'word-rotate-enter'"
                          x-text="words[currentWord]"></span>
                    <br class="hidden md:inline">
                    Businesses from <span class="whitespace-nowrap italic">UCO Community</span>
                </h1>
                <p class="max-w-lg text-lg font-medium leading-relaxed tracking-tight text-gray-600/80">
                    Explore a vibrant ecosystem of student-led ventures. Turning potential into market-ready impact.
                </p>
            </div>

            <div class="pt-6">
                <a href="{{ route('businesses.index') }}"
                   class="group/btn inline-flex w-full items-center justify-center gap-4 rounded-[1.8rem] bg-uco-orange-600 px-8 py-4 text-base font-black text-white shadow-[0_25px_60px_rgba(247,147,30,0.25)] transition-all hover:scale-[1.03] hover:bg-uco-orange-700 active:scale-95 sm:w-auto sm:px-12 sm:py-5 sm:text-lg">
                    Explore Business
                    <i class="bi bi-arrow-right text-lg transition-transform group-hover/btn:translate-x-2 sm:text-xl"></i>
                </a>
            </div>
        </div>

        {{-- --- Right Content: Immersive Spotlight Grid (2x2) --- --}}
        <div class="md:col-span-5 lg:col-span-5">
            <div class="grid grid-cols-2 gap-6">
                @foreach ($spotlightBusinesses->take(4) as $index => $business)
                    <div class="group/card {{ $index % 2 == 1 ? 'mt-8' : '' }} relative">
                        <a href="{{ route('businesses.show', $business) }}"
                           class="block overflow-hidden transition-all duration-700 hover:-translate-y-2">

                            <div class="space-y-3">
                                {{-- --- Compact Showcase Image --- --}}
                                <div
                                     class="relative aspect-video w-full overflow-hidden rounded-sm border border-gray-100 bg-white shadow-sm">
                                    @if ($business->products->first()?->photo_url)
                                        <x-premium-image :src="$business->products->first()->photo_url" class="h-full w-full" />
                                    @else
                                        <div
                                             class="uco-placeholder-mesh flex h-full w-full items-center justify-center">
                                            <div class="relative">
                                                <div
                                                     class="absolute inset-0 rounded-full bg-uco-orange-200/20 blur-xl">
                                                </div>
                                                <i
                                                   class="bi bi-rocket-takeoff relative z-10 text-2xl text-uco-orange-300/60"></i>
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
                                                {{ $business->initial }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- --- Content --- --}}
                                <div class="px-3 pb-2">
                                    <h4
                                        class="truncate text-[15px] font-[900] tracking-tight text-gray-950 transition-colors group-hover/card:text-uco-orange-600">
                                        {{ $business->name }}
                                    </h4>
                                    <div class="mt-1 flex items-center gap-2">
                                        <div
                                             class="h-4 w-4 overflow-hidden rounded-full border border-uco-orange-100">
                                            <img src="{{ $business->public_owner->profile_photo_url }}"
                                                 class="h-full w-full bg-slate-50 object-contain">
                                        </div>
                                        <span
                                              class="max-w-[80px] truncate text-[9px] font-bold uppercase tracking-widest text-gray-400">
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
        <div class="col-span-full block pt-4 text-center md:hidden">
            <a href="{{ route('businesses.index') }}"
               class="group/btn inline-flex w-full items-center justify-center gap-6 rounded-[1.8rem] bg-uco-orange-600 px-12 py-5 text-lg font-black text-white shadow-[0_25px_60px_rgba(247,147,30,0.25)] transition-all hover:scale-[1.03] hover:bg-uco-orange-700 active:scale-95">
                Explore Business
                <i class="bi bi-arrow-right text-xl transition-transform group-hover/btn:translate-x-2"></i>
            </a>
        </div>
    </div>
</section>
