<x-app-layout>
    <div class="space-y-24 pb-32 bg-white">
        {{-- High-Fidelity "Better" Hero Section --}}
        <section class="group relative overflow-hidden rounded-[4.5rem] bg-[#FFF9F2] px-10 py-24 md:px-24 md:py-32 lg:px-32 mx-4 mt-4 reveal-on-scroll">
            {{-- Background Effects --}}
            <div class="uco-hero-mesh opacity-90"></div>
            <div class="uco-noise-overlay"></div>
            
            {{-- Dynamic Floating Orbs with Subtle Motion --}}
            <div class="uco-float-orb uco-float-orb--one opacity-40 mix-blend-multiply transition-transform duration-[10s] group-hover:translate-x-12 group-hover:-translate-y-8"></div>
            <div class="uco-float-orb uco-float-orb--two opacity-30 mix-blend-multiply transition-transform duration-[12s] group-hover:-translate-x-16 group-hover:translate-y-12"></div>
            
            <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-start">
                {{-- Left Content --}}
                <div class="space-y-10">

                    <div class="space-y-8">
                        <h1 class="text-4xl font-[900] text-gray-950 md:text-5xl lg:text-6xl tracking-[-0.04em] leading-[1.05] max-w-4xl flex flex-wrap items-baseline gap-4"
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
                            <span>Discover</span>
                            <span class="uco-text-gradient-orange inline-block min-w-[180px]" 
                                  :class="isAnimating ? 'word-rotate-exit' : 'word-rotate-enter'"
                                  x-text="words[currentWord]"></span>
                            <span>Businesses</span>
                            <span class="text-gray-950/80">from</span>
                            <span class="whitespace-nowrap italic">UCO Community</span>
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
                <div class="lg:pl-8">
                    <div class="grid grid-cols-2 gap-6">
                        @foreach ($spotlightBusinesses->take(4) as $index => $business)
                            <div class="group/card relative {{ $index % 2 == 1 ? 'mt-8' : '' }}">
                                <a href="{{ route('businesses.show', $business) }}"
                                    class="uco-glass-light block overflow-hidden rounded-xl p-2 shadow-[0_20px_60px_rgba(0,0,0,0.03)] transition-all duration-700 hover:-translate-y-2 hover:shadow-2xl hover:border-uco-orange-200/50">
                                    
                                    <div class="space-y-3">
                                        {{-- Compact Showcase Image --}}
                                        <div class="relative aspect-video w-full overflow-hidden rounded-[2rem] bg-gray-100">
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

        {{-- Section 1: Featured Students (Intrapreneurs) --}}
        <section class="max-w-[1600px] mx-auto px-8 md:px-12 py-24 space-y-16">
            <div class="reveal-on-scroll flex flex-col md:flex-row md:items-end justify-between gap-10">
                <div class="space-y-4">
                    <h2 class="text-5xl font-[900] text-gray-950 tracking-tighter">Featured Students</h2>
                    <p class="text-xl font-medium text-gray-500 max-w-2xl leading-relaxed">
                        Meet our outstanding intrapreneurs and professionals driving innovation within corporate ecosystems.
                    </p>
                </div>
                <div class="flex items-center gap-4 text-xs font-black text-gray-300 uppercase tracking-[0.25em]">
                    <span class="w-12 h-[2px] bg-gray-100"></span>
                    Corporate Innovators
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 justify-items-center">
                @forelse($topIntrapreneurs as $student)
                    <article class="reveal-on-scroll group rounded-[2rem] border border-gray-100 bg-white p-7 shadow-[0_20px_60px_rgba(0,0,0,0.03)] transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:border-blue-100/70 w-full max-w-[380px] flex flex-col justify-between" style="transition-delay: {{ $loop->index * 80 }}ms">
                        <div>
                            <div class="flex items-start gap-4">
                                <div class="h-20 w-20 overflow-hidden rounded-[1.5rem] border border-blue-100 bg-gray-50 shadow-sm flex-shrink-0">
                                    @if($student->profile_photo_url)
                                        <img src="{{ $student->profile_photo_url }}" alt="{{ $student->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100/40 text-blue-500 font-black text-2xl">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-blue-50 text-blue-600">
                                        Intrapreneur
                                    </span>
                                    <h3 class="mt-2 truncate text-lg font-[900] text-gray-950">{{ $student->name }}</h3>
                                    <p class="text-xs font-semibold text-gray-400 mt-0.5">Cohort {{ $student->year_of_enrollment ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="mt-6 border-t border-gray-50 pt-5">
                                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-gray-400 mb-2.5">Academic Profile</p>
                                <div class="grid grid-cols-2 gap-2 text-xs">
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

                        <div class="mt-6">
                            @php
                                $featuredCompany = $student->companies->first();
                            @endphp
                            @if($featuredCompany)
                                <div class="rounded-xl bg-slate-50 p-4 border border-slate-100/80">
                                    <p class="text-[9px] font-black uppercase tracking-[0.25em] text-slate-400">Career Highlight</p>
                                    <p class="mt-1 text-sm font-bold text-slate-900 truncate">{{ $featuredCompany->name }}</p>
                                    @if($featuredCompany->category)
                                        <span class="inline-block mt-1 text-[9px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">
                                            {{ $featuredCompany->category->name }}
                                        </span>
                                    @endif
                                    <p class="mt-2 text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ $featuredCompany->description }}</p>
                                </div>
                            @else
                                <div class="rounded-xl bg-slate-50/50 p-4 border border-dashed border-slate-200 text-center py-6">
                                    <i class="bi bi-briefcase text-slate-300 text-xl block mb-1"></i>
                                    <p class="text-[9px] text-slate-400 font-medium">No company info added yet</p>
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full uco-placeholder-mesh relative rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center w-full">
                        <div class="relative z-10 space-y-4">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                                <i class="bi bi-people text-2xl text-uco-orange-400"></i>
                            </div>
                            <div class="space-y-1">
                                <p class="text-lg font-black text-gray-900">No Featured Students</p>
                                <p class="text-sm font-medium text-gray-500">We're currently curating our top student intrapreneurs.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Section 2: Featured Ventures (Entrepreneurs) --}}
        <section class="max-w-[1600px] mx-auto px-8 md:px-12 py-12 space-y-16">
            <div class="reveal-on-scroll flex flex-col md:flex-row md:items-end justify-between gap-10">
                <div class="space-y-4">
                    <h2 class="text-5xl font-[900] text-gray-950 tracking-tighter">Featured Ventures</h2>
                    <p class="text-xl font-medium text-gray-500 max-w-2xl leading-relaxed">
                        Discover startup founders and student-led enterprises shaping the future of business.
                    </p>
                </div>
                <div class="flex items-center gap-4 text-xs font-black text-gray-300 uppercase tracking-[0.25em]">
                    <span class="w-12 h-[2px] bg-gray-100"></span>
                    Student Startups
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 justify-items-center">
                @forelse($topEntrepreneurs as $student)
                    <article class="reveal-on-scroll group rounded-[2rem] border border-gray-100 bg-white p-7 shadow-[0_20px_60px_rgba(0,0,0,0.03)] transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:border-orange-100/70 w-full max-w-[380px] flex flex-col justify-between" style="transition-delay: {{ $loop->index * 80 }}ms">
                        <div>
                            <div class="flex items-start gap-4">
                                <div class="h-20 w-20 overflow-hidden rounded-[1.5rem] border border-orange-100 bg-gray-50 shadow-sm flex-shrink-0">
                                    @if($student->profile_photo_url)
                                        <img src="{{ $student->profile_photo_url }}" alt="{{ $student->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-orange-50 to-orange-100/40 text-uco-orange-500 font-black text-2xl">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-orange-50 text-uco-orange-600">
                                        Entrepreneur
                                    </span>
                                    <h3 class="mt-2 truncate text-lg font-[900] text-gray-950">{{ $student->name }}</h3>
                                    <p class="text-xs font-semibold text-gray-400 mt-0.5">Cohort {{ $student->year_of_enrollment ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="mt-6 border-t border-gray-50 pt-5">
                                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-gray-400 mb-2.5">Academic Profile</p>
                                <div class="grid grid-cols-2 gap-2 text-xs">
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

                        <div class="mt-6">
                            @php
                                $featuredBusiness = $student->businesses->first();
                            @endphp
                            @if($featuredBusiness)
                                <div class="rounded-xl bg-orange-50/50 p-4 border border-orange-100/60">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 overflow-hidden rounded-lg bg-white p-1 shadow-sm border border-gray-100 flex-shrink-0 flex items-center justify-center">
                                            @if($featuredBusiness->logo_url)
                                                <img src="{{ $featuredBusiness->logo_url }}" class="h-full w-full object-contain">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-[10px] font-black text-uco-orange-400 bg-uco-orange-50">
                                                    {{ strtoupper(substr($featuredBusiness->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[9px] font-black uppercase tracking-[0.25em] text-gray-400">Venture Founded</p>
                                            <p class="text-sm font-bold text-gray-900 truncate">{{ $featuredBusiness->name }}</p>
                                        </div>
                                    </div>
                                    @if($featuredBusiness->category)
                                        <span class="inline-block mt-2 text-[9px] font-bold text-white bg-uco-orange-600 px-2 py-0.5 rounded-md">
                                            {{ $featuredBusiness->category->name }}
                                        </span>
                                    @endif
                                    <p class="mt-2 text-xs text-gray-600 line-clamp-2 leading-relaxed">{{ $featuredBusiness->description }}</p>
                                    <a href="{{ route('businesses.show', $featuredBusiness) }}" class="inline-flex items-center gap-1 mt-3 text-xs font-bold text-uco-orange-600 hover:text-uco-orange-700 transition">
                                        Visit Storefront <i class="bi bi-chevron-right text-[9px]"></i>
                                    </a>
                                </div>
                            @else
                                <div class="rounded-xl bg-orange-50/20 p-4 border border-dashed border-orange-200 text-center py-6">
                                    <i class="bi bi-shop text-orange-300 text-xl block mb-1"></i>
                                    <p class="text-[9px] text-orange-400 font-medium">No business registered yet</p>
                                </div>
                            @endif
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
        <section class="max-w-[1600px] mx-auto px-8 md:px-12 py-12 pb-24 space-y-16">
            <div class="reveal-on-scroll flex flex-col md:flex-row md:items-end justify-between gap-10">
                <div class="space-y-4">
                    <h2 class="text-5xl font-[900] text-gray-950 tracking-tighter">Community Voices</h2>
                    <p class="text-xl font-medium text-gray-500 max-w-2xl leading-relaxed">
                        Real stories, journeys, and experiences shared by members of the UCO community.
                    </p>
                </div>
                <div class="flex items-center gap-4 text-xs font-black text-gray-300 uppercase tracking-[0.25em]">
                    <span class="w-12 h-[2px] bg-gray-100"></span>
                    Student Stories
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 justify-items-center">
                @forelse($testimonies as $student)
                    <div class="w-full max-w-[380px] bg-white border border-gray-100 rounded-[24px] overflow-hidden shadow-[0_20px_25px_-5px_rgba(0,0,0,0.05),0_10px_10px_-5px_rgba(0,0,0,0.01)] transition-all duration-300 hover:shadow-[0_30px_50px_rgba(0,0,0,0.08)] hover:-translate-y-2 flex flex-col relative reveal-on-scroll" style="transition-delay: {{ $loop->index * 80 }}ms">
                        
                        {{-- Top Section: Image & Info --}}
                        <div class="relative h-[420px] w-full flex-shrink-0">
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
                            <div style="position: absolute; bottom: 38px; left: 25px; right: 25px; color: white;">
                                <h3 style="font-size: 20px; font-weight: 900; margin-bottom: 4px; letter-spacing: -0.5px; line-height: 1.2;">{{ $student->name }}</h3>
                                <p style="color: #cbd5e1; font-size: 12px; font-weight: 600; margin-bottom: 2px;">
                                    {{ $student->current_status ?? 'Member' }} at UCO Community
                                </p>
                            </div>
                        </div>

                        {{-- Bottom Section: Testimony content --}}
                        <div style="position: relative; padding: 45px 30px 35px 30px; text-align: center;" class="flex-grow flex items-center justify-center bg-white rounded-b-[24px]">
                            {{-- Quote Icon --}}
                            <div style="position: absolute; top: -25px; left: 50%; transform: translateX(-50%); width: 50px; height: 50px; background: #ff8a00; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(255, 138, 0, 0.3); display: flex; align-items: center; justify-center; color: white; font-size: 24px; z-index: 10;">
                                <i class="bi bi-quote"></i>
                            </div>

                            <p style="color: #334155; font-weight: 600; line-height: 1.7; font-size: 14px; font-style: italic; margin: 0;">
                                “{{ $student->testimony }}”
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full uco-placeholder-mesh relative rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center w-full">
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
        </section>  
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Subtle Mouse Tracking for Hero
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
        });
    </script>
    @endpush
</x-app-layout>
