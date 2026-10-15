{{-- --- Featured ventures carousel --- --}}
<section class="max-w-[1600px] mx-auto px-8 md:px-12 py-12 space-y-16 relative overflow-hidden">
    {{-- --- Background decorative glows --- --}}
    <div class="uco-ambient-glow uco-ambient-glow--orange uco-floating-blob-slow -bottom-20 -right-20"></div>

    {{-- --- Floating micro-shapes (Ada Kehidupan) --- --}}
    <div class="uco-floating-shape uco-floating-shape--plus top-[20%] left-[8%]"></div>
    <div class="uco-floating-shape uco-floating-shape--ring bottom-[20%] right-[10%]"></div>
    <div class="uco-floating-shape uco-floating-shape--dot top-[60%] left-[25%]"></div>

    <div class="reveal-on-scroll flex flex-col md:flex-row md:items-end justify-between gap-10 relative z-10">
        <div class="space-y-4 relative">
            {{-- --- Outline background text --- --}}
            <div class="uco-outline-bg-text uco-outline-bg-text--orange uco-parallax-text">VENTURES</div>
            <h2 class="text-5xl font-[900] text-gray-950 tracking-tighter uco-section-title relative z-10">
                Featured <span class="uco-text-gradient-orange">Ventures</span>
            </h2>
            <p class="text-xl font-medium text-gray-500 max-w-2xl leading-relaxed mt-4 relative z-10">
                Discover startup founders and student-led enterprises shaping the future of business.
            </p>
        </div>
        <div class="flex items-center gap-4 text-xs font-black text-gray-300 uppercase tracking-[0.25em]">
            <span class="w-12 h-[2px] bg-gray-100"></span>
            Student Startups
        </div>
    </div>

    <div class="relative z-10 w-full" x-data="testimonyCarousel()" x-init="init()"
        @mouseenter="stopAutoScroll()" @mouseleave="startAutoScroll()">
        <div x-ref="track" @scroll.passive="updateScroll"
            class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth gap-6 pb-12 pt-4 items-stretch [&::-webkit-scrollbar]:hidden md:grid md:grid-cols-2 md:overflow-visible md:snap-none md:gap-8 md:pb-0 md:pt-0 w-full relative z-10 scrollbar-none">
            @forelse($spotlightBusinesses as $featuredBusiness)
                <div data-carousel-slide
                    class="snap-start shrink-0 w-[min(100%,18rem)] sm:w-[calc(50%-0.75rem)] md:w-full md:shrink md:grow-0 flex h-auto">
                    <a href="{{ route('businesses.show', $featuredBusiness) }}"
                        class="reveal-on-scroll block uco-premium-card uco-premium-card--orange group rounded-[2rem] border border-gray-100 bg-white p-7 shadow-[0_20px_60px_rgba(0,0,0,0.03)] transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:border-orange-100/70 w-full flex flex-col justify-between cursor-pointer">

                        {{-- --- Top Part: Venture Info & Description --- --}}
                        <div>
                            <div class="flex items-start gap-4">
                                <div
                                    class="h-16 w-16 overflow-hidden rounded-[1.2rem] bg-white p-2 shadow-sm border border-gray-100 flex-shrink-0 flex items-center justify-center">
                                    @if ($featuredBusiness->logo_url)
                                        <img src="{{ $featuredBusiness->logo_url }}"
                                            class="h-full w-full object-contain">
                                    @else
                                        <div
                                            class="flex h-full w-full items-center justify-center text-2xl font-black text-uco-orange-500 bg-orange-50 rounded-[0.8rem]">
                                            {{ strtoupper(substr($featuredBusiness->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3
                                        class="text-xl font-[900] text-gray-950 leading-tight group-hover:text-uco-orange-600 transition-colors truncate">
                                        {{ $featuredBusiness->name }}</h3>
                                    @if ($featuredBusiness->category)
                                        <span
                                            class="inline-block text-[9px] font-black uppercase tracking-[0.15em] text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md mt-1.5">
                                            {{ $featuredBusiness->category->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <p class="mt-4 text-sm text-gray-500 leading-relaxed line-clamp-3">
                                {{ $featuredBusiness->description }}
                            </p>
                        </div>

                        {{-- --- Middle Part: Founder Profile & Academic Specs --- --}}
                        <div class="mt-6 border-t border-gray-50 pt-5">
                            <p class="text-[9px] font-black uppercase tracking-[0.25em] text-gray-400 mb-3">
                                Owned By</p>

                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 overflow-hidden rounded-sm flex-shrink-0">
                                    @if ($featuredBusiness->user->profile_photo_url)
                                        <x-premium-image :src="$featuredBusiness->user->profile_photo_url" :alt="$featuredBusiness->user->full_titled_name" class="h-full w-full" />
                                    @else
                                        <div
                                            class="flex h-full w-full items-center justify-center bg-gradient-to-br from-orange-50 to-orange-100/40 text-uco-orange-500 font-black text-sm">
                                            {{ strtoupper(substr($featuredBusiness->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-sm font-black text-gray-950 truncate leading-tight">
                                        {{ $featuredBusiness->user->full_titled_name }}</h4>
                                    <p class="text-xs font-semibold text-gray-500 mt-0.5 truncate">
                                        {{ $featuredBusiness->user->major ?? 'General Studies' }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 mt-0.5">Join UC Online
                                        {{ $featuredBusiness->user->year_of_enrollment ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- --- Bottom Part: Visit CTA --- --}}
                        <div class="mt-6 pt-4 border-t border-gray-50 flex items-center justify-between">
                            <span
                                class="inline-flex items-center gap-2 text-sm font-black text-uco-orange-600 group-hover:text-uco-orange-700 transition">
                                Visit Venture <i
                                    class="bi bi-arrow-right text-base transition-transform group-hover:translate-x-1"></i>
                            </span>
                        </div>
                    </a>
                </div>
            @empty
                <div
                    class="col-span-full uco-placeholder-mesh relative rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center w-full">
                    <div class="relative z-10 space-y-4">
                        <div
                            class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                            <i class="bi bi-rocket text-2xl text-uco-orange-400"></i>
                        </div>
                        <div class="space-y-1">
                            <p class="text-lg font-black text-gray-900">No Featured Ventures</p>
                            <p class="text-sm font-medium text-gray-500">We're currently curating our top
                                student ventures.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <x-featured.carousel-dots mobile-only />
    </div>
</section>
