<x-app-layout>
    <div class="space-y-16 pb-6">
        {{-- High-Fidelity "Better" Hero Section --}}
        <section class="group relative overflow-hidden uco-hero-section-bleed py-12 md:py-16 reveal-on-scroll">
            {{-- Background Effects with Gradient Mask --}}
            <div class="absolute inset-0 overflow-hidden pointer-events-none" style="mask-image: linear-gradient(to bottom, black 50%, transparent 100%); -webkit-mask-image: linear-gradient(to bottom, black 50%, transparent 100%);">
                <div class="uco-hero-mesh opacity-90"></div>
                <div class="uco-noise-overlay"></div>

                {{-- Dynamic Floating Orbs with Subtle Motion --}}
                <div
                    class="uco-float-orb uco-float-orb--one opacity-40  transition-transform duration-[10s] group-hover:translate-x-12 group-hover:-translate-y-8">
                </div>
                <div
                    class="uco-float-orb uco-float-orb--two opacity-30  transition-transform duration-[12s] group-hover:-translate-x-16 group-hover:translate-y-12">
                </div>
            </div>

            <div class="relative z-10 max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-8 items-start">
                {{-- Left Content --}}
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

                    <div class="pt-6 hidden md:block">
                        <a href="{{ route('businesses.index') }}"
                            class="group/btn inline-flex items-center gap-6 rounded-[1.8rem] bg-uco-orange-600 px-12 py-5 text-lg font-black text-white shadow-[0_25px_60px_rgba(247,147,30,0.25)] transition-all hover:bg-uco-orange-700 hover:scale-[1.03] active:scale-95">
                            Explore Business
                            <i class="bi bi-arrow-right text-xl transition-transform group-hover/btn:translate-x-2"></i>
                        </a>
                    </div>
                </div>

                {{-- Right Content: Immersive Spotlight Grid (2x2) --}}
                <div class="md:col-span-5 lg:col-span-5">
                    <div class="grid grid-cols-2 gap-6">
                        @foreach ($spotlightBusinesses->take(4) as $index => $business)
                            <div class="group/card relative {{ $index % 2 == 1 ? 'mt-8' : '' }}">
                                <a href="{{ route('businesses.show', $business) }}"
                                    class="block overflow-hidden transition-all duration-700 hover:-translate-y-2">

                                    <div class="space-y-3">
                                        {{-- Compact Showcase Image --}}
                                        <div
                                            class="relative aspect-video w-full overflow-hidden rounded-sm bg-white border border-gray-100 shadow-sm">
                                            @php
                                                $coverImage = $business->products->first()?->photo_url ?? null;
                                            @endphp

                                            @if ($coverImage)
                                                <x-premium-image :src="$coverImage" class="h-full w-full" />
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

                                            {{-- Logo Overlay --}}
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

                                        {{-- Content --}}
                                        <div class="px-3 pb-2">
                                            <h4
                                                class="text-[15px] font-[900] text-gray-950 truncate tracking-tight group-hover/card:text-uco-orange-600 transition-colors">
                                                {{ $business->name }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-1">
                                                <div
                                                    class="h-4 w-4 overflow-hidden rounded-full border border-uco-orange-100">
                                                    <img src="{{ $business->user->profile_photo_url }}"
                                                        class="h-full w-full object-contain bg-slate-50">
                                                </div>
                                                <span
                                                    class="text-[9px] font-bold text-gray-400 uppercase tracking-widest truncate max-w-[80px]">
                                                    {{ $business->user->full_titled_name ?? 'Founder' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Mobile-only bottom button --}}
                <div class="col-span-full pt-4 block md:hidden text-center">
                    <a href="{{ route('businesses.index') }}"
                        class="group/btn inline-flex items-center justify-center gap-6 rounded-[1.8rem] bg-uco-orange-600 px-12 py-5 text-lg font-black text-white shadow-[0_25px_60px_rgba(247,147,30,0.25)] transition-all hover:bg-uco-orange-700 hover:scale-[1.03] active:scale-95 w-full">
                        Explore Business
                        <i class="bi bi-arrow-right text-xl transition-transform group-hover/btn:translate-x-2"></i>
                    </a>
                </div>
            </div>
        </section>

        {{-- Section 1: Featured Intrapreneur Students --}}
        <section class="max-w-[1600px] mx-auto px-8 md:px-12 pt-24 pb-12 space-y-16 relative overflow-hidden">
            <div class="uco-ambient-glow uco-ambient-glow--blue uco-floating-blob-slow -top-20 -left-20"></div>
            <div class="uco-floating-shape uco-floating-shape--plus-blue top-[15%] right-[10%]"></div>
            <div class="uco-floating-shape uco-floating-shape--ring-blue bottom-[25%] left-[5%]"></div>
            <div class="uco-floating-shape uco-floating-shape--dot top-[50%] right-[25%]"></div>

            <div class="reveal-on-scroll flex flex-col md:flex-row md:items-end justify-between gap-10 relative z-10">
                <div class="space-y-4 relative">
                    <div class="uco-outline-bg-text uco-outline-bg-text--blue uco-parallax-text">INTRA</div>
                    <h2
                        class="text-5xl font-[900] text-gray-950 tracking-tighter uco-section-title uco-section-title--blue relative z-10">
                        Our <span class="uco-text-gradient-blue">Intrapreneur</span> Students
                    </h2>
                    <p class="text-xl font-medium text-gray-500 max-w-2xl leading-relaxed mt-4 relative z-10">
                        Meet outstanding intrapreneurs driving innovation within corporate ecosystems.
                    </p>
                </div>
                <div class="flex items-center gap-4 text-xs font-black text-gray-300 uppercase tracking-[0.25em]">
                    <span class="w-12 h-[2px] bg-gray-100"></span>
                    Corporate Innovators
                </div>
            </div>

            <div class="relative z-10 w-full" x-data="testimonyCarousel()" x-init="init()"
                @mouseenter="stopAutoScroll()" @mouseleave="startAutoScroll()">
                <div x-ref="track" @scroll.passive="updateScroll"
                    class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth gap-6 pb-12 pt-4 items-stretch [&::-webkit-scrollbar]:hidden md:grid md:grid-cols-2 xl:grid-cols-3 md:overflow-visible md:snap-none md:gap-6 md:pb-0 md:pt-0 w-full relative z-10"
                    style="scrollbar-width: none; -ms-overflow-style: none;">
                    @forelse($topIntrapreneurs as $student)
                        <div data-carousel-slide
                            class="snap-start shrink-0 w-[min(100%,18rem)] sm:w-[calc(50%-0.75rem)] md:w-full md:shrink md:grow-0 flex h-auto">
                            @include('featured.partials.featured-student-card', [
                                'student' => $student,
                                'type' => 'intra',
                                'delay' => $loop->index * 40,
                            ])
                        </div>
                    @empty
                        <div
                            class="col-span-full uco-placeholder-mesh relative rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center w-full">
                            <div class="relative z-10 space-y-4">
                                <div
                                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                                    <i class="bi bi-people text-2xl text-blue-400"></i>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-lg font-black text-gray-900">No Featured Intrapreneur Students</p>
                                    <p class="text-sm font-medium text-gray-500">Featured intrapreneur profiles will
                                        appear here once curated by admin.</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Carousel Dots (Mobile Only) --}}
                <div class="md:hidden flex justify-center items-center gap-2 mt-4" x-show="totalSlides > 1" x-cloak>
                    <template x-for="slideIndex in dotIndices" :key="slideIndex">
                        <button @click="scrollTo(slideIndex)" class="h-2 rounded-full transition-all duration-300"
                            :class="activeSlide === slideIndex ? 'w-8 bg-uco-orange-500' : 'w-2 bg-gray-300 hover:bg-gray-400'">
                        </button>
                    </template>
                </div>
            </div>
        </section>

        {{-- Section 2: Featured Entrepreneur Students --}}
        <section class="max-w-[1600px] mx-auto px-8 md:px-12 py-12 space-y-16 relative overflow-hidden">
            <div class="uco-ambient-glow uco-ambient-glow--orange uco-floating-blob-slow -top-10 -right-20 opacity-60">
            </div>
            <div class="uco-floating-shape uco-floating-shape--plus top-[20%] left-[8%]"></div>
            <div class="uco-floating-shape uco-floating-shape--ring bottom-[20%] right-[10%]"></div>

            <div class="reveal-on-scroll flex flex-col md:flex-row md:items-end justify-between gap-10 relative z-10">
                <div class="space-y-4 relative">
                    <div class="uco-outline-bg-text uco-outline-bg-text--orange uco-parallax-text">ENTRE</div>
                    <h2 class="text-5xl font-[900] text-gray-950 tracking-tighter uco-section-title relative z-10">
                        Our <span class="uco-text-gradient-orange">Entrepreneur</span> Students
                    </h2>
                    <p class="text-xl font-medium text-gray-500 max-w-2xl leading-relaxed mt-4 relative z-10">
                        Discover student founders building ventures and startups across the UCO network.
                    </p>
                </div>
                <div class="flex items-center gap-4 text-xs font-black text-gray-300 uppercase tracking-[0.25em]">
                    <span class="w-12 h-[2px] bg-gray-100"></span>
                    Student Founders
                </div>
            </div>

            <div class="relative z-10 w-full" x-data="testimonyCarousel()" x-init="init()"
                @mouseenter="stopAutoScroll()" @mouseleave="startAutoScroll()">
                <div x-ref="track" @scroll.passive="updateScroll"
                    class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth gap-6 pb-12 pt-4 items-stretch [&::-webkit-scrollbar]:hidden md:grid md:grid-cols-2 xl:grid-cols-3 md:overflow-visible md:snap-none md:gap-6 md:pb-0 md:pt-0 w-full relative z-10"
                    style="scrollbar-width: none; -ms-overflow-style: none;">
                    @forelse($topEntrepreneurs as $student)
                        <div data-carousel-slide
                            class="snap-start shrink-0 w-[min(100%,18rem)] sm:w-[calc(50%-0.75rem)] md:w-full md:shrink md:grow-0 flex h-auto">
                            @include('featured.partials.featured-student-card', [
                                'student' => $student,
                                'type' => 'entre',
                                'delay' => $loop->index * 40,
                            ])
                        </div>
                    @empty
                        <div
                            class="col-span-full uco-placeholder-mesh relative rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center w-full">
                            <div class="relative z-10 space-y-4">
                                <div
                                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                                    <i class="bi bi-rocket-takeoff text-2xl text-uco-orange-400"></i>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-lg font-black text-gray-900">No Featured Entrepreneur Students</p>
                                    <p class="text-sm font-medium text-gray-500">Featured entrepreneur profiles will
                                        appear here once curated by admin.</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Carousel Dots (Mobile Only) --}}
                <div class="md:hidden flex justify-center items-center gap-2 mt-4" x-show="totalSlides > 1" x-cloak>
                    <template x-for="slideIndex in dotIndices" :key="slideIndex">
                        <button @click="scrollTo(slideIndex)" class="h-2 rounded-full transition-all duration-300"
                            :class="activeSlide === slideIndex ? 'w-8 bg-uco-orange-500' : 'w-2 bg-gray-300 hover:bg-gray-400'">
                        </button>
                    </template>
                </div>
            </div>
        </section>

        {{-- Section 3: Featured Ventures (Entrepreneurs) --}}
        <section class="max-w-[1600px] mx-auto px-8 md:px-12 py-12 space-y-16 relative overflow-hidden">
            {{-- Background decorative glows --}}
            <div class="uco-ambient-glow uco-ambient-glow--orange uco-floating-blob-slow -bottom-20 -right-20"></div>

            {{-- Floating micro-shapes (Ada Kehidupan) --}}
            <div class="uco-floating-shape uco-floating-shape--plus top-[20%] left-[8%]"></div>
            <div class="uco-floating-shape uco-floating-shape--ring bottom-[20%] right-[10%]"></div>
            <div class="uco-floating-shape uco-floating-shape--dot top-[60%] left-[25%]"></div>

            <div class="reveal-on-scroll flex flex-col md:flex-row md:items-end justify-between gap-10 relative z-10">
                <div class="space-y-4 relative">
                    {{-- Outline background text --}}
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
                    class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth gap-6 pb-12 pt-4 items-stretch [&::-webkit-scrollbar]:hidden md:grid md:grid-cols-2 md:overflow-visible md:snap-none md:gap-8 md:pb-0 md:pt-0 w-full relative z-10"
                    style="scrollbar-width: none; -ms-overflow-style: none;">
                    @forelse($spotlightBusinesses as $featuredBusiness)
                        @php
                            $student = $featuredBusiness->user;
                        @endphp
                        <div data-carousel-slide
                            class="snap-start shrink-0 w-[min(100%,18rem)] sm:w-[calc(50%-0.75rem)] md:w-full md:shrink md:grow-0 flex h-auto">
                            <a href="{{ route('businesses.show', $featuredBusiness) }}"
                                class="reveal-on-scroll block uco-premium-card uco-premium-card--orange group rounded-[2rem] border border-gray-100 bg-white p-7 shadow-[0_20px_60px_rgba(0,0,0,0.03)] transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:border-orange-100/70 w-full flex flex-col justify-between cursor-pointer"
                                style="transition-delay: {{ $loop->index * 40 }}ms">

                                {{-- Top Part: Venture Info & Description --}}
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

                                {{-- Middle Part: Founder Profile & Academic Specs --}}
                                <div class="mt-6 border-t border-gray-50 pt-5">
                                    <p class="text-[9px] font-black uppercase tracking-[0.25em] text-gray-400 mb-3">
                                        Owned By</p>

                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 overflow-hidden rounded-sm flex-shrink-0">
                                            @if ($student->profile_photo_url)
                                                <x-premium-image :src="$student->profile_photo_url" :alt="$student->full_titled_name" class="h-full w-full" />
                                            @else
                                                <div
                                                    class="flex h-full w-full items-center justify-center bg-gradient-to-br from-orange-50 to-orange-100/40 text-uco-orange-500 font-black text-sm">
                                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="text-sm font-black text-gray-950 truncate leading-tight">
                                                {{ $student->full_titled_name }}</h4>
                                            <p class="text-xs font-semibold text-gray-500 mt-0.5 truncate">
                                                {{ $student->major ?? 'General Studies' }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 mt-0.5">Join UC Online
                                                {{ $student->year_of_enrollment ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Bottom Part: Visit CTA --}}
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

                {{-- Carousel Dots (Mobile Only) --}}
                <div class="md:hidden flex justify-center items-center gap-2 mt-4" x-show="totalSlides > 1" x-cloak>
                    <template x-for="slideIndex in dotIndices" :key="slideIndex">
                        <button @click="scrollTo(slideIndex)" class="h-2 rounded-full transition-all duration-300"
                            :class="activeSlide === slideIndex ? 'w-8 bg-uco-orange-500' : 'w-2 bg-gray-300 hover:bg-gray-400'">
                        </button>
                    </template>
                </div>
            </div>
        </section>

        {{-- Section 3: Community Voices (Testimonies) --}}
        <section class="max-w-[1600px] mx-auto px-8 md:px-12 py-12 space-y-16 relative overflow-hidden">
            {{-- Background decorative glows --}}
            <div class="uco-ambient-glow uco-ambient-glow--purple uco-floating-blob-slow -top-20 -left-20"></div>

            {{-- Floating micro-shapes (Ada Kehidupan) --}}
            <div class="uco-floating-shape uco-floating-shape--plus-blue top-[15%] right-[12%]"></div>
            <div class="uco-floating-shape uco-floating-shape--ring-blue bottom-[30%] left-[6%]"></div>
            <div class="uco-floating-shape uco-floating-shape--dot top-[55%] right-[30%]"></div>

            <div class="reveal-on-scroll flex flex-col md:flex-row md:items-end justify-between gap-10 relative z-10">
                <div class="space-y-4 relative">
                    {{-- Outline background text --}}
                    <div class="uco-outline-bg-text uco-outline-bg-text--purple uco-parallax-text">VOICES</div>
                    <h2 class="text-5xl font-[900] text-gray-950 tracking-tighter uco-section-title relative z-10">
                        Community <span class="uco-text-gradient-orange">Voices</span></h2>
                    <p class="text-xl font-medium text-gray-500 max-w-2xl leading-relaxed mt-4 relative z-10">
                        Real stories, journeys, and experiences shared by UCO community.
                    </p>
                </div>
                <div class="flex items-center gap-4 text-xs font-black text-gray-300 uppercase tracking-[0.25em]">
                    <span class="w-12 h-[2px] bg-gray-100"></span>
                    Student Stories
                </div>
            </div>

            <div class="relative z-10 w-full" x-data="testimonyCarousel()" x-init="init()"
                @mouseenter="stopAutoScroll()" @mouseleave="startAutoScroll()">
                <div x-ref="track" @scroll.passive="updateScroll"
                    class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth gap-6 pb-12 pt-4 items-stretch [&::-webkit-scrollbar]:hidden"
                    style="scrollbar-width: none; -ms-overflow-style: none;">
                    @forelse($testimonies as $student)
                        <div data-carousel-slide
                            class="snap-start shrink-0 w-[min(100%,17rem)] sm:w-[calc(50%-0.75rem)] md:w-[calc((100%-3rem)/3)] lg:w-[calc((100%-4.5rem)/4)] flex h-auto">
                            <div class="w-full bg-white border border-gray-200/80 rounded-[20px] overflow-hidden transition-all duration-300 hover:-translate-y-2 flex flex-col relative reveal-on-scroll uco-premium-card uco-premium-card--orange cursor-pointer group"
                                data-name="{{ $student->full_titled_name }}"
                                data-status="{{ $student->current_status ?? 'Member' }} at UCO Community"
                                data-photo="{{ $student->profile_photo_url ?? '' }}"
                                data-testimony="{{ $student->testimony }}"
                                @click="openModal($el.dataset.name, $el.dataset.status, $el.dataset.photo, $el.dataset.testimony)"
                                style="transition-delay: {{ $loop->index * 40 }}ms">

                                {{-- Top Section: Image & Info --}}
                                <div class="relative h-[280px] w-full flex-shrink-0">
                                    @if ($student->profile_photo_url)
                                        <x-premium-image :src="$student->profile_photo_url" :alt="$student->full_titled_name" class="w-full h-full" />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-white text-4xl font-black"
                                            style="background: linear-gradient(135deg, #f7931e, #fdb913);">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                    @endif

                                    {{-- Overlay Gradient --}}
                                    <div
                                        style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.4) 40%, transparent 100%);">
                                    </div>

                                    {{-- Text Content on Image --}}
                                    <div
                                        style="position: absolute; bottom: 35px; left: 20px; right: 20px; color: white;">
                                        <h3
                                            style="font-size: 16px; font-weight: 900; margin-bottom: 2px; letter-spacing: -0.5px; line-height: 1.2;">
                                            {{ $student->full_titled_name }}</h3>
                                        <p
                                            style="color: #cbd5e1; font-size: 10px; font-weight: 600; margin-bottom: 0;">
                                            {{ $student->current_status ?? 'Member' }} at UCO Community
                                        </p>
                                    </div>
                                </div>

                                {{-- Bottom Section: Testimony content --}}
                                <div style="position: relative; padding: 30px 20px 25px 20px; text-align: center;"
                                    class="flex-grow flex items-center justify-center bg-white rounded-b-[20px] relative group/text">
                                    {{-- Quote Icon --}}
                                    <div
                                        class="absolute -top-5 left-1/2 -translate-x-1/2 w-10 h-10 bg-uco-orange-500 rounded-xl shadow-[0_10px_15px_-3px_rgba(247,147,30,0.3)] flex items-center justify-center text-white z-10 transition-transform duration-300 group-hover:scale-110">
                                        <i class="fa-solid fa-quote-left text-base"></i>
                                    </div>

                                    <p style="color: #334155; font-weight: 500; line-height: 1.6; font-size: 12px; font-style: italic; margin: 0; display: -webkit-box; -webkit-line-clamp: 6; -webkit-box-orient: vertical; overflow: hidden;"
                                        class="transition-colors duration-300 group-hover:text-slate-900">
                                        "{{ $student->testimony }}"
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="snap-start shrink-0 w-full col-span-full uco-placeholder-mesh relative rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center">
                            <div class="relative z-10 space-y-4">
                                <div
                                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                                    <i class="bi bi-chat-quote text-2xl text-uco-orange-400"></i>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-lg font-black text-gray-900">No Testimonies</p>
                                    <p class="text-sm font-medium text-gray-500">No student testimonies are featured at
                                        this moment.</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Carousel Dots --}}
                <div class="flex justify-center items-center gap-2 mt-4" x-show="totalSlides > 1" x-cloak>
                    <template x-for="slideIndex in dotIndices" :key="slideIndex">
                        <button @click="scrollTo(slideIndex)" class="h-2 rounded-full transition-all duration-300"
                            :class="activeSlide === slideIndex ? 'w-8 bg-uco-orange-500' : 'w-2 bg-gray-300 hover:bg-gray-400'">
                        </button>
                    </template>
                </div>

                <!-- Premium Glassmorphic Testimony Modal -->
                <div x-show="showModal"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
                    style="display: none;" x-cloak>
                    <!-- Backdrop with blur -->
                    <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" @click="closeModal()"
                        class="fixed inset-0 bg-slate-950/65 backdrop-blur-md"></div>

                    <!-- Modal Body Container -->
                    <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                        class="relative w-full max-w-3xl bg-white rounded-[2.5rem] overflow-hidden shadow-[0_30px_70px_rgba(0,0,0,0.25)] border border-slate-100/80 z-50 flex flex-col md:flex-row h-auto max-h-[85vh] md:h-[450px]">

                        <!-- Left Column: Portrait and Info -->
                        <div class="md:w-5/12 bg-slate-900 relative flex-shrink-0 min-h-[220px] md:min-h-full">
                            <!-- Image -->
                            <template x-if="modalPhoto">
                                <div class="w-full h-full absolute inset-0 flex items-center justify-center">
                                    <img :src="modalPhoto" :alt="modalName" class="max-w-full max-h-full object-contain rounded-sm" referrerpolicy="no-referrer">
                                </div>
                            </template>
                            <!-- Image Fallback Gradient -->
                            <template x-if="!modalPhoto">
                                <div
                                    class="w-full h-full absolute inset-0 flex items-center justify-center text-white text-7xl font-black bg-gradient-to-br from-[#f7931e] to-[#fdb913]">
                                    <span x-text="modalName.substring(0, 1).toUpperCase()"></span>
                                </div>
                            </template>

                            <!-- Premium Dark Bottom Overlay Gradient -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent">
                            </div>

                            <!-- Student identity at the bottom -->
                            <div class="absolute bottom-6 left-6 right-6 text-white z-10">
                                <h3 class="text-xl font-black tracking-tight leading-tight mb-1" x-text="modalName">
                                </h3>
                                <p class="text-xs text-orange-400 font-bold uppercase tracking-wider"
                                    x-text="modalStatus"></p>
                            </div>
                        </div>

                        <!-- Right Column: Scrollable Quote content -->
                        <div
                            class="md:w-7/12 p-8 md:p-10 flex flex-col justify-between overflow-hidden bg-white relative">
                            <!-- Close button -->
                            <button @click="closeModal()"
                                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors duration-200 shadow-sm z-30">
                                <i class="bi bi-x-lg text-[10px]"></i>
                            </button>

                            <!-- Background Quote Icon -->
                            <div
                                class="absolute -top-4 -right-4 opacity-[0.06] text-orange-500 pointer-events-none select-none">
                                <i class="bi bi-quote text-[150px]"></i>
                            </div>

                            <!-- Scrollable quote body -->
                            <div class="flex-grow overflow-y-auto pr-2 mt-4 max-h-[250px] md:max-h-[320px]">
                                <div class="flex gap-3 items-start">
                                    <span
                                        class="text-orange-500 text-2xl font-black select-none leading-none">&ldquo;</span>
                                    <p class="text-slate-700 font-medium italic leading-relaxed text-sm md:text-[15px] pt-1"
                                        x-text="modalText"></p>
                                    <span
                                        class="text-orange-500 text-2xl font-black select-none leading-none self-end">&rdquo;</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            function testimonyCarousel() {
                return {
                    activeSlide: 0,
                    totalSlides: 0,
                    slides: [],
                    dotIndices: [],
                    autoScrollInterval: null,
                    showModal: false,
                    modalName: '',
                    modalStatus: '',
                    modalPhoto: '',
                    modalText: '',
                    openModal(name, status, photo, text) {
                        this.modalName = name;
                        this.modalStatus = status;
                        this.modalPhoto = photo;
                        this.modalText = text;
                        this.showModal = true;
                        this.stopAutoScroll();
                    },
                    closeModal() {
                        this.showModal = false;
                        this.startAutoScroll();
                    },
                    init() {
                        this.$nextTick(() => {
                            this.refreshSlides();
                            this.updateScroll();
                            this.startAutoScroll();

                            if (this.$refs.track) {
                                this.$refs.track.addEventListener('scrollend', () => this.updateScroll());
                            }
                        });

                        window.addEventListener('resize', () => {
                            this.refreshSlides();
                            this.updateScroll();
                        });
                    },
                    refreshSlides() {
                        if (!this.$refs.track) return;
                        this.slides = Array.from(this.$refs.track.querySelectorAll('[data-carousel-slide]'));

                        const track = this.$refs.track;
                        const maxScrollLeft = track.scrollWidth - track.clientWidth;

                        const indices = [];
                        this.slides.forEach((slide, index) => {
                            const scrollLeftVal = this.getSlideScrollLeft(index);
                            if (scrollLeftVal < maxScrollLeft - 15) {
                                indices.push(index);
                            }
                        });

                        if (this.slides.length > 0) {
                            const lastIndex = this.slides.length - 1;
                            if (!indices.includes(lastIndex)) {
                                indices.push(lastIndex);
                            }
                        }

                        this.dotIndices = indices;
                        this.totalSlides = this.dotIndices.length;
                    },
                    startAutoScroll() {
                        this.stopAutoScroll();
                        if (this.totalSlides <= 1) return;
                        this.autoScrollInterval = setInterval(() => {
                            let currentIndex = this.dotIndices.indexOf(this.activeSlide);
                            let nextIndex = currentIndex + 1;
                            if (nextIndex >= this.totalSlides || nextIndex < 0) {
                                nextIndex = 0;
                            }
                            this.scrollTo(this.dotIndices[nextIndex]);
                        }, 4000);
                    },
                    stopAutoScroll() {
                        if (this.autoScrollInterval) {
                            clearInterval(this.autoScrollInterval);
                            this.autoScrollInterval = null;
                        }
                    },
                    getSlideScrollLeft(index) {
                        const track = this.$refs.track;
                        const slide = this.slides[index];
                        if (!track || !slide) return 0;
                        return slide.offsetLeft - track.offsetLeft;
                    },
                    updateScroll() {
                        if (!this.$refs.track || this.slides.length === 0 || this.dotIndices.length === 0) return;

                        const track = this.$refs.track;
                        const scrollLeft = track.scrollLeft;
                        const maxScrollLeft = track.scrollWidth - track.clientWidth;

                        let closestIndex = this.dotIndices[0];
                        let minDistance = Infinity;

                        this.dotIndices.forEach((slideIndex) => {
                            const slideStart = Math.min(this.getSlideScrollLeft(slideIndex), maxScrollLeft);
                            const distance = Math.abs(scrollLeft - slideStart);
                            if (distance < minDistance) {
                                minDistance = distance;
                                closestIndex = slideIndex;
                            }
                        });

                        this.activeSlide = closestIndex;
                    },
                    scrollTo(index) {
                        if (!this.slides[index] || !this.$refs.track) return;

                        const track = this.$refs.track;
                        const maxScrollLeft = track.scrollWidth - track.clientWidth;
                        const targetScroll = Math.min(this.getSlideScrollLeft(index), maxScrollLeft);

                        track.scrollTo({
                            left: targetScroll,
                            behavior: 'smooth'
                        });
                        this.activeSlide = index;
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                // GSAP Animations
                if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
                    gsap.registerPlugin(ScrollTrigger);

                    // Disable default transitions only for items we are going to animate to avoid FOUC
                    const animatedElements = document.querySelectorAll(
                        'section:nth-of-type(2) .reveal-on-scroll, section:nth-of-type(3) .reveal-on-scroll, section:nth-of-type(4) .reveal-on-scroll, section:nth-of-type(5) .reveal-on-scroll'
                    );
                    animatedElements.forEach(el => {
                        el.style.opacity = '0';
                        el.style.transform = 'translateY(40px)';
                        el.style.transition = 'none';
                    });

                    // Featured Intrapreneur Students
                    gsap.to("section:nth-of-type(2) .reveal-on-scroll", {
                        opacity: 1,
                        y: 0,
                        duration: 0.6,
                        stagger: 0.05,
                        ease: "power2.out",
                        scrollTrigger: {
                            trigger: "section:nth-of-type(2)",
                            start: "top 90%",
                            toggleActions: "play none none none"
                        }
                    });

                    // Featured Entrepreneur Students
                    gsap.to("section:nth-of-type(3) .reveal-on-scroll", {
                        opacity: 1,
                        y: 0,
                        duration: 0.6,
                        stagger: 0.05,
                        ease: "power2.out",
                        scrollTrigger: {
                            trigger: "section:nth-of-type(3)",
                            start: "top 90%",
                            toggleActions: "play none none none"
                        }
                    });

                    // Featured Ventures
                    gsap.to("section:nth-of-type(4) .reveal-on-scroll", {
                        opacity: 1,
                        y: 0,
                        duration: 0.6,
                        stagger: 0.05,
                        ease: "power2.out",
                        scrollTrigger: {
                            trigger: "section:nth-of-type(4)",
                            start: "top 90%",
                            toggleActions: "play none none none"
                        }
                    });

                    // Community Voices / Testimonies
                    gsap.to("section:nth-of-type(5) .reveal-on-scroll", {
                        opacity: 1,
                        y: 0,
                        duration: 0.6,
                        stagger: 0.05,
                        ease: "power2.out",
                        scrollTrigger: {
                            trigger: "section:nth-of-type(5)",
                            start: "top 90%",
                            toggleActions: "play none none none"
                        }
                    });

                    // Scroll-Driven Heading Parallax (Alternate horizontal movements)
                    gsap.utils.toArray('.uco-parallax-text').forEach((text, idx) => {
                        const movementX = idx % 2 === 0 ? 100 : -100;
                        gsap.to(text, {
                            x: movementX,
                            scrollTrigger: {
                                trigger: text,
                                start: "top bottom",
                                end: "bottom top",
                                scrub: 1.2
                            }
                        });
                    });
                }

                // Spotlight hover coordinates tracker
                document.querySelectorAll('.uco-premium-card').forEach(card => {
                    card.addEventListener('mousemove', e => {
                        const rect = card.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        card.style.setProperty('--mouse-x', `${x}px`);
                        card.style.setProperty('--mouse-y', `${y}px`);
                    });
                });

                // Magnetic buttons elastic tracking
                if (typeof gsap !== 'undefined') {
                    document.querySelectorAll('.uco-magnetic-btn').forEach(btn => {
                        btn.addEventListener('mousemove', e => {
                            const rect = btn.getBoundingClientRect();
                            const x = e.clientX - rect.left - (rect.width / 2);
                            const y = e.clientY - rect.top - (rect.height / 2);

                            gsap.to(btn, {
                                x: x * 0.35,
                                y: y * 0.35,
                                duration: 0.3,
                                ease: "power2.out"
                            });
                        });

                        btn.addEventListener('mouseleave', () => {
                            gsap.to(btn, {
                                x: 0,
                                y: 0,
                                duration: 0.65,
                                ease: "elastic.out(1.1, 0.4)"
                            });
                        });
                    });

                    // Mouse movements for background elements (parallax on mouse move)
                    window.addEventListener('mousemove', e => {
                        const mouseX = (e.clientX / window.innerWidth - 0.5) * 25;
                        const mouseY = (e.clientY / window.innerHeight - 0.5) * 25;

                        gsap.to('.uco-floating-shape', {
                            x: -mouseX,
                            y: -mouseY,
                            duration: 1.5,
                            ease: "power1.out",
                            overwrite: "auto"
                        });

                        const orbs = document.querySelectorAll('.uco-float-orb');
                        if (orbs.length >= 2) {
                            gsap.to(orbs[0], {
                                x: mouseX,
                                y: mouseY,
                                duration: 1.5,
                                ease: "power1.out",
                                overwrite: "auto"
                            });
                            gsap.to(orbs[1], {
                                x: -mouseX,
                                y: -mouseY,
                                duration: 1.5,
                                ease: "power1.out",
                                overwrite: "auto"
                            });
                        }
                    });
                } else {
                    // Classic JS fallbacks for hover/mousemove orbs if GSAP is unavailable
                    const hero = document.querySelector('section');
                    if (hero) {
                        hero.addEventListener('mousemove', (e) => {
                            const orbs = document.querySelectorAll('.uco-float-orb');
                            if (orbs.length >= 2) {
                                const x = (e.clientX / window.innerWidth - 0.5) * 40;
                                const y = (e.clientY / window.innerHeight - 0.5) * 40;
                                orbs[0].style.transform = `translate(${x}px, ${y}px)`;
                                orbs[1].style.transform = `translate(${-x}px, ${-y}px)`;
                            }
                        });
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>
