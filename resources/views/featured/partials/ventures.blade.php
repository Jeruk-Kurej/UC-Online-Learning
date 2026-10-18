{{-- --- Featured ventures carousel --- --}}
<section class="relative mx-auto max-w-[1600px] space-y-16 overflow-hidden px-8 py-12 md:px-12">
    {{-- --- Background decorative glows --- --}}
    <div class="uco-ambient-glow uco-ambient-glow--orange uco-floating-blob-slow -bottom-20 -right-20"></div>

    {{-- --- Floating micro-shapes (Ada Kehidupan) --- --}}
    <div class="uco-floating-shape uco-floating-shape--plus left-[8%] top-[20%]"></div>
    <div class="uco-floating-shape uco-floating-shape--ring bottom-[20%] right-[10%]"></div>
    <div class="uco-floating-shape uco-floating-shape--dot left-[25%] top-[60%]"></div>

    <div class="reveal-on-scroll relative z-10 flex flex-col justify-between gap-10 md:flex-row md:items-end">
        <div class="relative space-y-4">
            {{-- --- Outline background text --- --}}
            <div class="uco-outline-bg-text uco-outline-bg-text--orange uco-parallax-text">VENTURES</div>
            <h2 class="uco-section-title relative z-10 text-5xl font-bold text-gray-950">
                Featured <span class="uco-text-gradient-orange">Ventures</span>
            </h2>
            <p class="relative z-10 mt-4 max-w-2xl text-xl font-medium leading-relaxed text-gray-600">
                Discover startup founders and student-led enterprises shaping the future of business.
            </p>
        </div>
        <div class="flex items-center gap-4 text-xs font-bold text-gray-600">
            <span class="h-[2px] w-12 bg-gray-100"></span>
            Student Startups
        </div>
    </div>

    <div class="relative z-10 w-full" x-data="testimonyCarousel()" x-init="init()"
         @mouseenter="stopAutoScroll()" @mouseleave="startAutoScroll()">
        <div x-ref="track" @scroll.passive="updateScroll"
             class="scrollbar-none relative z-10 flex w-full snap-x snap-mandatory items-stretch gap-6 overflow-x-auto scroll-smooth pb-12 pt-4 md:grid md:snap-none md:grid-cols-2 md:gap-8 md:overflow-visible md:pb-0 md:pt-0 [&::-webkit-scrollbar]:hidden">
            @forelse($spotlightBusinesses as $featuredBusiness)
                <div data-carousel-slide
                     class="flex h-auto w-[min(100%,18rem)] shrink-0 snap-start sm:w-[calc(50%-0.75rem)] md:w-full md:shrink md:grow-0">
                    <a href="{{ route('businesses.show', $featuredBusiness) }}"
                       class="reveal-on-scroll uco-premium-card uco-premium-card--orange group block flex w-full cursor-pointer flex-col justify-between rounded-[2rem] border border-gray-100 bg-white p-7 shadow-[0_20px_60px_rgba(0,0,0,0.03)] transition-all duration-500 hover:-translate-y-2 hover:border-orange-100/70 hover:shadow-2xl">

                        {{-- --- Top Part: Venture Info & Description --- --}}
                        <div>
                            <div class="flex items-start gap-4">
                                <div
                                     class="flex h-16 w-16 flex-shrink-0 items-center justify-center overflow-hidden rounded-[1.2rem] border border-gray-100 bg-white p-2 shadow-sm">
                                    @if ($featuredBusiness->logo_url)
                                        <img src="{{ $featuredBusiness->logo_url }}"
                                             class="h-full w-full object-contain">
                                    @else
                                        <div
                                             class="flex h-full w-full items-center justify-center rounded-[0.8rem] bg-orange-50 text-2xl font-bold text-uco-orange-500">
                                            {{ $featuredBusiness->initial }}
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3
                                        class="truncate text-xl font-bold leading-tight text-gray-950 transition-colors group-hover:text-uco-orange-600">
                                        {{ $featuredBusiness->name }}</h3>
                                    @if ($featuredBusiness->category)
                                        <span
                                              class="mt-1.5 inline-block rounded-md bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-600">
                                            {{ $featuredBusiness->category->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <p class="mt-4 line-clamp-3 text-sm leading-relaxed text-gray-600">
                                {{ $featuredBusiness->description }}
                            </p>
                        </div>

                        {{-- --- Middle Part: Founder Profile & Academic Specs --- --}}
                        <div class="mt-6 border-t border-gray-50 pt-5">
                            <p class="mb-3 text-xs font-bold text-gray-600">
                                Owned By</p>

                            <div class="flex items-center gap-3">
                                <div
                                     class="h-10 w-10 flex-shrink-0 overflow-hidden rounded-sm">
                                    @if ($featuredBusiness->user->profile_photo_url)
                                        <x-premium-image :src="$featuredBusiness->user->profile_photo_url" :alt="$featuredBusiness->user->full_titled_name" class="h-full w-full" />
                                    @else
                                        <div
                                             class="flex h-full w-full items-center justify-center bg-gradient-to-br from-orange-50 to-orange-100/40 text-sm font-bold text-uco-orange-500">
                                            {{ $featuredBusiness->user->initial }}
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="truncate text-sm font-bold leading-tight text-gray-950">
                                        {{ $featuredBusiness->user->full_titled_name }}</h4>
                                    <p class="mt-0.5 truncate text-xs font-semibold text-gray-600">
                                        {{ $featuredBusiness->user->major ?? 'General Studies' }}</p>
                                    <p class="mt-0.5 text-xs font-bold text-gray-600">Join UC Online
                                        {{ $featuredBusiness->user->year_of_enrollment ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- --- Bottom Part: Visit CTA --- --}}
                        <div class="mt-6 flex items-center justify-between border-t border-gray-50 pt-4">
                            <span
                                  class="inline-flex items-center gap-2 text-sm font-bold text-uco-orange-600 transition group-hover:text-uco-orange-700">
                                Visit Venture <i
                                   class="bi bi-arrow-right text-base transition-transform group-hover:translate-x-1"></i>
                            </span>
                        </div>
                    </a>
                </div>
            @empty
                <x-featured.empty-state icon="bi-rocket text-2xl text-uco-orange-400" title="No Featured Ventures">
                    We're currently curating our top student ventures.
                </x-featured.empty-state>
            @endforelse
        </div>

        <x-featured.carousel-dots mobile-only />
    </div>
</section>
