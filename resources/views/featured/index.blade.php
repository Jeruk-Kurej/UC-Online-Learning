<x-app-layout>
    <div class="space-y-16 pb-6">
        {{-- High-Fidelity "Better" Hero Section --}}
        <section class="group relative overflow-hidden rounded-[3.5rem] bg-[#FFF9F2] px-8 py-12 md:px-16 md:py-16 lg:px-16 mx-4 mt-0 reveal-on-scroll">
            {{-- Background Effects --}}
            <div class="uco-hero-mesh opacity-90"></div>
            <div class="uco-noise-overlay"></div>
            
            {{-- Dynamic Floating Orbs with Subtle Motion --}}
            <div class="uco-float-orb uco-float-orb--one opacity-40 mix-blend-multiply transition-transform duration-[10s] group-hover:translate-x-12 group-hover:-translate-y-8"></div>
            <div class="uco-float-orb uco-float-orb--two opacity-30 mix-blend-multiply transition-transform duration-[12s] group-hover:-translate-x-16 group-hover:translate-y-12"></div>
            
            <div class="relative z-10 grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-8 items-start">
                {{-- Left Content --}}
                <div class="space-y-10 md:col-span-7 lg:col-span-7">

                    <div class="space-y-8">
                        <h1 class="text-4xl font-[900] text-gray-950 md:text-5xl lg:text-6xl tracking-[-0.04em] leading-[1.35] md:leading-[1.4] max-w-4xl"
                            x-data="{ 
                                words: ['Innovative', 'Sustainable', 'Transformative', 'Pioneering'],
                                currentWord: 0,
                                isAnimating: false
                            }"
                            x-init="setInterval(() => { 
                                isAnimating = true;
                                setTimeout(() => {
                                    currentWord = (currentWord + 1) % words.length;
                                    isAnimating = false;
                                }, 300);
                            }, 2300)">
                            Discover <span class="uco-text-gradient-orange inline-block min-w-[180px] md:min-w-[280px]" 
                                  :class="isAnimating ? 'word-rotate-exit' : 'word-rotate-enter'"
                                  x-text="words[currentWord]"></span>
                            <br class="hidden md:inline">
                            Businesses from <span class="whitespace-nowrap italic">UCO Community</span>
                        </h1>
                        <p class="max-w-lg text-lg font-medium leading-relaxed text-gray-600/80 tracking-tight">
                            Explore a vibrant ecosystem of student-led ventures. We bridge the gap between academic theory and market-ready impact.
                        </p>
                    </div>

                    <div class="pt-6">
                        <a href="{{ route('businesses.index') }}"
                            class="group/btn inline-flex items-center gap-6 rounded-[1.8rem] bg-uco-orange-600 px-12 py-5 text-lg font-black text-white shadow-[0_25px_60px_rgba(247,147,30,0.25)] transition-all hover:bg-uco-orange-700 hover:scale-[1.03] active:scale-95">
                            Explore Businesses
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
                                        <div class="relative aspect-video w-full overflow-hidden rounded-[2rem] bg-gray-100 shadow-[0_15px_30px_rgba(0,0,0,0.06)]">
                                            @php
                                                $coverImage = $business->products->first()?->photo_url ?? null;
                                            @endphp
                                            
                                            @if ($coverImage)
                                                <img src="{{ $coverImage }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="uco-placeholder-mesh flex h-full w-full items-center justify-center">
                                                    <div class="relative">
                                                        <div class="absolute inset-0 blur-xl bg-uco-orange-200/20 rounded-full"></div>
                                                        <i class="bi bi-rocket-takeoff text-2xl text-uco-orange-300/60 relative z-10"></i>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Logo Overlay --}}
                                            <div class="absolute bottom-2 left-3 h-8 w-8 overflow-hidden rounded-lg bg-white p-1 shadow-lg">
                                                @if($business->logo_url)
                                                    <img src="{{ $business->logo_url }}" class="h-full w-full object-contain">
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center text-[10px] font-black text-uco-orange-400">
                                                        {{ strtoupper(substr($business->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Content --}}
                                        <div class="px-3 pb-2">
                                            <h4 class="text-[15px] font-[900] text-gray-900 truncate tracking-tight group-hover/card:text-uco-orange-600 transition-colors">
                                                {{ $business->name }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-1">
                                                <div class="h-4 w-4 overflow-hidden rounded-full border border-uco-orange-100">
                                                    <img src="{{ $business->user->profile_photo_url }}" class="h-full w-full object-cover">
                                                </div>
                                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest truncate max-w-[80px]">
                                                    {{ $business->user->name ?? 'Founder' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
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
                    <h2 class="text-5xl font-[900] text-gray-950 tracking-tighter uco-section-title uco-section-title--blue relative z-10">
                        Our <span class="uco-text-gradient-blue">Intrapreneur</span> Students
                        <span class="text-2xl font-semibold text-blue-500/80 ml-2 inline-block translate-y-[-2px]">({{ $topIntrapreneurs->count() }})</span>
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

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 w-full relative z-10">
                @forelse($topIntrapreneurs as $student)
                    @include('featured.partials.featured-student-card', ['student' => $student, 'type' => 'intra', 'delay' => $loop->index * 80])
                @empty
                    <div class="col-span-full uco-placeholder-mesh relative rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center w-full">
                        <div class="relative z-10 space-y-4">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                                <i class="bi bi-people text-2xl text-blue-400"></i>
                            </div>
                            <div class="space-y-1">
                                <p class="text-lg font-black text-gray-900">No Featured Intrapreneur Students</p>
                                <p class="text-sm font-medium text-gray-500">Featured intrapreneur profiles will appear here once curated by admin.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Section 2: Featured Entrepreneur Students --}}
        <section class="max-w-[1600px] mx-auto px-8 md:px-12 py-12 space-y-16 relative overflow-hidden">
            <div class="uco-ambient-glow uco-ambient-glow--orange uco-floating-blob-slow -top-10 -right-20 opacity-60"></div>
            <div class="uco-floating-shape uco-floating-shape--plus top-[20%] left-[8%]"></div>
            <div class="uco-floating-shape uco-floating-shape--ring bottom-[20%] right-[10%]"></div>

            <div class="reveal-on-scroll flex flex-col md:flex-row md:items-end justify-between gap-10 relative z-10">
                <div class="space-y-4 relative">
                    <div class="uco-outline-bg-text uco-outline-bg-text--orange uco-parallax-text">ENTRE</div>
                    <h2 class="text-5xl font-[900] text-gray-950 tracking-tighter uco-section-title relative z-10">
                        Our <span class="uco-text-gradient-orange">Entrepreneur</span> Students
                        <span class="text-2xl font-semibold text-uco-orange-500/80 ml-2 inline-block translate-y-[-2px]">({{ $topEntrepreneurs->count() }})</span>
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

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 w-full relative z-10">
                @forelse($topEntrepreneurs as $student)
                    @include('featured.partials.featured-student-card', ['student' => $student, 'type' => 'entre', 'delay' => $loop->index * 80])
                @empty
                    <div class="col-span-full uco-placeholder-mesh relative rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center w-full">
                        <div class="relative z-10 space-y-4">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                                <i class="bi bi-rocket-takeoff text-2xl text-uco-orange-400"></i>
                            </div>
                            <div class="space-y-1">
                                <p class="text-lg font-black text-gray-900">No Featured Entrepreneur Students</p>
                                <p class="text-sm font-medium text-gray-500">Featured entrepreneur profiles will appear here once curated by admin.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
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
                        <span class="text-2xl font-semibold text-uco-orange-500/80 ml-2 inline-block translate-y-[-2px]">({{ $spotlightBusinesses->count() }})</span>
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

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 w-full relative z-10">
                @forelse($spotlightBusinesses as $featuredBusiness)
                    @php
                        $student = $featuredBusiness->user;
                    @endphp
                    <article class="reveal-on-scroll uco-premium-card uco-premium-card--orange group rounded-[2.5rem] border border-gray-100 bg-white shadow-[0_20px_60px_rgba(0,0,0,0.03)] transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:border-orange-100/70 w-full flex flex-col md:flex-row overflow-hidden" style="transition-delay: {{ $loop->index * 80 }}ms">
                        
                        {{-- Left Column: Business Details --}}
                        <div class="flex-grow p-6 bg-gradient-to-br from-orange-50/10 to-white flex flex-col justify-between border-b md:border-b-0 md:border-r border-gray-100/80">
                            @if($featuredBusiness)
                                <div>
                                    <div class="flex items-center gap-4">
                                        <div class="h-16 w-16 overflow-hidden rounded-[1.2rem] bg-white p-2 shadow-sm border border-gray-100 flex-shrink-0 flex items-center justify-center">
                                            @if($featuredBusiness->logo_url)
                                                <img src="{{ $featuredBusiness->logo_url }}" class="h-full w-full object-contain">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-2xl font-black text-uco-orange-500 bg-orange-50 rounded-[0.8rem]">
                                                    {{ strtoupper(substr($featuredBusiness->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-[900] text-gray-950 mt-0.5 leading-tight">{{ $featuredBusiness->name }}</h3>
                                        </div>
                                    </div>
                                    

                                    
                                    <p class="mt-4 text-sm text-gray-500 leading-relaxed line-clamp-3">
                                        {{ $featuredBusiness->description }}
                                    </p>
                                </div>
                                
                                <div class="mt-6 pt-4 border-t border-gray-50">
                                    <a href="{{ route('businesses.show', $featuredBusiness) }}" class="inline-flex items-center gap-2 text-sm font-black text-uco-orange-600 hover:text-uco-orange-700 transition">
                                        Visit Venture <i class="bi bi-arrow-right text-base"></i>
                                    </a>
                                </div>
                            @else
                                <div class="h-full flex flex-col items-center justify-center text-center py-12">
                                    <div class="h-12 w-12 rounded-full bg-gray-50 border border-dashed border-gray-200 flex items-center justify-center mb-3">
                                        <i class="bi bi-shop text-gray-300 text-lg"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-400">No Venture Registered</p>
                                    <p class="text-xs text-gray-400 max-w-[200px] mt-1">This founder is currently developing their venture.</p>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Right Column: Founder Profile --}}
                        <div class="w-full md:w-[210px] p-6 flex flex-col justify-between bg-white flex-shrink-0">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-gray-400 mb-4">Owned By</p>
                                
                                <div class="flex flex-col items-start">
                                    <div class="h-14 w-14 overflow-hidden rounded-[1.2rem] border border-orange-100 bg-gray-50 shadow-sm mb-3">
                                        @if($student->profile_photo_url)
                                            <img src="{{ $student->profile_photo_url }}" alt="{{ $student->name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-orange-50 to-orange-100/40 text-uco-orange-500 font-black text-xl">
                                                {{ strtoupper(substr($student->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-[900] text-gray-950 leading-tight">{{ $student->name }}</h4>
                                        <p class="text-[10px] font-semibold text-gray-400 mt-1">Cohort {{ $student->year_of_enrollment ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                
                                <div class="mt-6 pt-4 border-t border-gray-50">
                                    <p class="text-[9px] font-black uppercase tracking-[0.25em] text-gray-400 mb-3">Academic Profile</p>
                                    <div class="space-y-3 text-xs">
                                        <div>
                                            <p class="text-gray-400 font-medium">Major</p>
                                            <p class="text-gray-700 font-bold truncate">{{ $student->major ?? 'General Studies' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 font-medium">Graduate Year</p>
                                            <p class="text-gray-700 font-bold">{{ $student->graduate_year ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full uco-placeholder-mesh relative rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center w-full">
                        <div class="relative z-10 space-y-4">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                                <i class="bi bi-rocket text-2xl text-uco-orange-400"></i>
                            </div>
                            <div class="space-y-1">
                                <p class="text-lg font-black text-gray-900">No Featured Ventures</p>
                                <p class="text-sm font-medium text-gray-500">We're currently curating our top student ventures.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
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
                    <h2 class="text-5xl font-[900] text-gray-950 tracking-tighter uco-section-title relative z-10">Community <span class="uco-text-gradient-orange">Voices</span></h2>
                    <p class="text-xl font-medium text-gray-500 max-w-2xl leading-relaxed mt-4 relative z-10">
                        Real stories, journeys, and experiences shared by members of the UCO community.
                    </p>
                </div>
                <div class="flex items-center gap-4 text-xs font-black text-gray-300 uppercase tracking-[0.25em]">
                    <span class="w-12 h-[2px] bg-gray-100"></span>
                    Student Stories
                </div>
            </div>

            <div class="relative z-10 w-full" x-data="testimonyCarousel()" x-init="init()" @mouseenter="stopAutoScroll()" @mouseleave="startAutoScroll()">
                <div x-ref="track" @scroll.passive="updateScroll" class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth gap-6 pb-12 pt-4 items-stretch [&::-webkit-scrollbar]:hidden" style="scrollbar-width: none; -ms-overflow-style: none;">
                    @forelse($testimonies as $student)
                        <div data-carousel-slide class="snap-start shrink-0 w-[min(100%,17rem)] sm:w-[calc(50%-0.75rem)] md:w-[calc((100%-3rem)/3)] lg:w-[calc((100%-4.5rem)/4)] flex h-auto">
                            <div class="w-full bg-white border border-gray-100 rounded-[20px] overflow-hidden shadow-[0_20px_25px_-5px_rgba(0,0,0,0.05),0_10px_10px_-5px_rgba(0,0,0,0.01)] transition-all duration-300 hover:shadow-[0_30px_50px_rgba(0,0,0,0.08)] hover:-translate-y-2 flex flex-col relative reveal-on-scroll uco-premium-card uco-premium-card--orange" style="transition-delay: {{ $loop->index * 80 }}ms">
                                
                                {{-- Top Section: Image & Info --}}
                                <div class="relative h-[280px] w-full flex-shrink-0">
                                    @if($student->profile_photo_url)
                                        <img src="{{ $student->profile_photo_url }}" 
                                             alt="{{ $student->name }}"
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-white text-4xl font-black"
                                             style="background: linear-gradient(135deg, #f7931e, #fdb913);">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    
                                    {{-- Overlay Gradient --}}
                                    <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.4) 40%, transparent 100%);"></div>
                                    
                                    {{-- Text Content on Image --}}
                                    <div style="position: absolute; bottom: 35px; left: 20px; right: 20px; color: white;">
                                        <h3 style="font-size: 16px; font-weight: 900; margin-bottom: 2px; letter-spacing: -0.5px; line-height: 1.2;">{{ $student->name }}</h3>
                                        <p style="color: #cbd5e1; font-size: 10px; font-weight: 600; margin-bottom: 0;">
                                            {{ $student->current_status ?? 'Member' }} at UCO Community
                                        </p>
                                    </div>
                                </div>

                                {{-- Bottom Section: Testimony content --}}
                                <div style="position: relative; padding: 30px 20px 25px 20px; text-align: center;" class="flex-grow flex items-center justify-center bg-white rounded-b-[20px]">
                                    {{-- Quote Icon --}}
                                    <div class="absolute -top-5 left-1/2 -translate-x-1/2 w-10 h-10 bg-uco-orange-500 rounded-xl shadow-[0_10px_15px_-3px_rgba(247,147,30,0.3)] flex items-center justify-center text-white z-10">
                                        <i class="fa-solid fa-quote-left text-base"></i>
                                    </div>

                                    <p style="color: #334155; font-weight: 500; line-height: 1.6; font-size: 12px; font-style: italic; margin: 0; display: -webkit-box; -webkit-line-clamp: 6; -webkit-box-orient: vertical; overflow: hidden;">
                                        "{{ $student->testimony }}"
                                    </p>
                                </div>
                            </div>
                        </div>
                @empty
                    <div class="snap-start shrink-0 w-full col-span-full uco-placeholder-mesh relative rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center">
                        <div class="relative z-10 space-y-4">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                                <i class="bi bi-chat-quote text-2xl text-uco-orange-400"></i>
                            </div>
                            <div class="space-y-1">
                                <p class="text-lg font-black text-gray-900">No Testimonies</p>
                                <p class="text-sm font-medium text-gray-500">No student testimonies are featured at this moment.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
                </div>
                
                {{-- Carousel Dots --}}
                <div class="flex justify-center items-center gap-2 mt-4" x-show="totalSlides > 1" x-cloak>
                    <template x-for="(_, index) in totalSlides" :key="index">
                        <button @click="scrollTo(index)"
                                class="h-2 rounded-full transition-all duration-300"
                                :class="activeSlide === index ? 'w-8 bg-uco-orange-500' : 'w-2 bg-gray-300 hover:bg-gray-400'">
                        </button>
                    </template>
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
                autoScrollInterval: null,
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
                    this.totalSlides = this.slides.length;
                },
                startAutoScroll() {
                    this.stopAutoScroll();
                    this.autoScrollInterval = setInterval(() => {
                        let nextSlide = this.activeSlide + 1;
                        if (nextSlide >= this.totalSlides) {
                            nextSlide = 0;
                        }
                        this.scrollTo(nextSlide);
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
                    if (!this.$refs.track || this.totalSlides === 0) return;

                    const track = this.$refs.track;
                    const scrollLeft = track.scrollLeft;
                    const maxScrollLeft = track.scrollWidth - track.clientWidth;

                    // If max rounded scroll position is reached, safely set the active slide to the last index.
                    if (Math.ceil(scrollLeft) >= maxScrollLeft - 5) {
                        this.activeSlide = this.totalSlides - 1;
                        return;
                    }

                    let closestIndex = 0;
                    let minDistance = Infinity;

                    this.slides.forEach((slide, index) => {
                        const slideStart = this.getSlideScrollLeft(index);
                        const distance = Math.abs(scrollLeft - slideStart);
                        if (distance < minDistance) {
                            minDistance = distance;
                            closestIndex = index;
                        }
                    });

                    this.activeSlide = closestIndex;
                },
                scrollTo(index) {
                    if (!this.slides[index] || !this.$refs.track) return;

                    const track = this.$refs.track;
                    track.scrollTo({
                        left: this.getSlideScrollLeft(index),
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
                    'section:nth-of-type(2) article, section:nth-of-type(3) article, section:nth-of-type(4) article, section:nth-of-type(5) .reveal-on-scroll'
                );
                animatedElements.forEach(el => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(40px)';
                    el.style.transition = 'none';
                });

                // Featured Intrapreneur Students
                gsap.to("section:nth-of-type(2) article", {
                    opacity: 1,
                    y: 0,
                    duration: 0.9,
                    stagger: 0.1,
                    ease: "power3.out",
                    scrollTrigger: {
                        trigger: "section:nth-of-type(2)",
                        start: "top 80%",
                        toggleActions: "play none none none"
                    }
                });

                // Featured Entrepreneur Students
                gsap.to("section:nth-of-type(3) article", {
                    opacity: 1,
                    y: 0,
                    duration: 0.9,
                    stagger: 0.1,
                    ease: "power3.out",
                    scrollTrigger: {
                        trigger: "section:nth-of-type(3)",
                        start: "top 80%",
                        toggleActions: "play none none none"
                    }
                });

                // Featured Ventures
                gsap.to("section:nth-of-type(4) article", {
                    opacity: 1,
                    y: 0,
                    duration: 0.9,
                    stagger: 0.1,
                    ease: "power3.out",
                    scrollTrigger: {
                        trigger: "section:nth-of-type(4)",
                        start: "top 80%",
                        toggleActions: "play none none none"
                    }
                });

                // Community Voices / Testimonies
                gsap.to("section:nth-of-type(5) .reveal-on-scroll", {
                    opacity: 1,
                    y: 0,
                    duration: 0.9,
                    stagger: 0.1,
                    ease: "power3.out",
                    scrollTrigger: {
                        trigger: "section:nth-of-type(5)",
                        start: "top 80%",
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
                        gsap.to(orbs[0], { x: mouseX, y: mouseY, duration: 1.5, ease: "power1.out", overwrite: "auto" });
                        gsap.to(orbs[1], { x: -mouseX, y: -mouseY, duration: 1.5, ease: "power1.out", overwrite: "auto" });
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
