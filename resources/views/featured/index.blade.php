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

        {{-- Featured Students & Testimonies --}}
        <section class="max-w-[1600px] mx-auto px-8 md:px-12 py-24 space-y-16">
            <div class="reveal-on-scroll flex flex-col md:flex-row md:items-end justify-between gap-10">
                <div class="space-y-4">
                    <h2 class="text-5xl font-[900] text-gray-950 tracking-tighter">Featured Profiles</h2>
                    <p class="text-xl font-medium text-gray-500 max-w-2xl leading-relaxed">
                        The shining stars of the UCO community, hand-picked for their impact and vision.
                    </p>
                </div>
                <div class="flex items-center gap-4 text-xs font-black text-gray-300 uppercase tracking-[0.25em]">
                    <span class="w-12 h-[2px] bg-gray-100"></span>
                    Community spotlight
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                @forelse($topProfiles as $profile)
                    @php
                        $featuredBusiness = $profile->businesses->first() ?? $profile->memberOfBusinesses->first();
                    @endphp
                    <article class="reveal-on-scroll group rounded-xl border border-gray-100 bg-white p-6 shadow-[0_20px_60px_rgba(0,0,0,0.04)] transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl" style="transition-delay: {{ $loop->index * 80 }}ms">
                        <div class="flex items-start gap-4">
                            <div class="h-16 w-16 overflow-hidden rounded-lg border border-uco-orange-100 bg-gray-50 shadow-sm flex-shrink-0">
                                @if($profile->profile_photo_url)
                                    <img src="{{ $profile->profile_photo_url }}" alt="{{ $profile->name }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-uco-orange-50 to-uco-orange-100/40 text-uco-orange-500 font-black text-lg">
                                        {{ strtoupper(substr($profile->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-uco-orange-600">Featured Profile</p>
                                <h3 class="mt-1 truncate text-xl font-[900] text-gray-950">{{ $profile->name }}</h3>
                                <p class="mt-1 text-sm font-medium text-gray-500">{{ $profile->major ?? $profile->role }}</p>
                            </div>
                        </div>

                        <div class="mt-5 space-y-4">
                            @if($profile->testimony)
                                <p class="text-sm leading-relaxed text-gray-600 line-clamp-4">
                                    “{{ $profile->testimony }}”
                                </p>
                            @endif

                            @if($featuredBusiness)
                                <div class="rounded-lg bg-gray-50 p-4 border border-gray-100">
                                    <p class="text-[10px] font-black uppercase tracking-[0.25em] text-gray-400">Business Highlight</p>
                                    <p class="mt-1 text-sm font-bold text-gray-900">{{ $featuredBusiness->name }}</p>
                                    <p class="mt-1 text-xs text-gray-500 line-clamp-2">{{ $featuredBusiness->description }}</p>
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full uco-placeholder-mesh relative rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center">
                        <div class="relative z-10 space-y-4">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                                <i class="bi bi-people text-2xl text-uco-orange-400"></i>
                            </div>
                            <div class="space-y-1">
                                <p class="text-lg font-black text-gray-900">No Featured Profiles</p>
                                <p class="text-sm font-medium text-gray-500">We're currently curating our top community members.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Community Testimonies Header --}}
            <div class="reveal-on-scroll pt-8 pb-4">
                <div class="flex items-center gap-4">
                    <h3 class="text-2xl font-[900] text-gray-950 tracking-tight">UC People Voices</h3>
                    <div class="h-[1px] flex-1 bg-gray-100"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($testimonies as $testimony)
                    <article class="reveal-on-scroll rounded-[2rem] border border-gray-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl" style="transition-delay: {{ $loop->index * 60 }}ms">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 overflow-hidden rounded-full border border-uco-orange-100 bg-gray-50 flex-shrink-0">
                                @if($testimony->profile_photo_url)
                                    <img src="{{ $testimony->profile_photo_url }}" alt="{{ $testimony->name }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-uco-orange-50 to-uco-orange-100/40 text-uco-orange-500 font-black">
                                        {{ strtoupper(substr($testimony->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="truncate text-sm font-black text-gray-900">{{ $testimony->name }}</h4>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-400">Community Testimony</p>
                            </div>
                        </div>

                        <p class="mt-4 text-sm leading-relaxed text-gray-600 line-clamp-5">“{{ $testimony->testimony }}”</p>
                    </article>
                @empty
                    <div class="col-span-full uco-placeholder-mesh relative rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center">
                        <div class="relative z-10 space-y-4">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-white shadow-sm">
                                <i class="bi bi-chat-quote text-xl text-uco-orange-400"></i>
                            </div>
                            <div class="space-y-1">
                                <p class="text-base font-black text-gray-900">No Testimonies Yet</p>
                                <p class="text-xs font-medium text-gray-500">Testimonies from our community will appear here soon.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Better Featured Grid Section --}}
        <section class="max-w-[1600px] mx-auto px-8 md:px-12 space-y-20">
            <div class="reveal-on-scroll flex flex-col md:flex-row md:items-end justify-between gap-10">
                <div class="space-y-4">
                    <h2 class="text-5xl font-[900] text-gray-950 tracking-tighter">Featured Entrepreneur</h2>
                    <p class="text-xl font-medium text-gray-500 max-w-2xl leading-relaxed">
                        Hand-picked businesses that represent the highest standards of innovation and execution within our network.
                    </p>
                </div>
                <div class="flex items-center gap-4 text-xs font-black text-gray-300 uppercase tracking-[0.25em]">
                    <span class="w-12 h-[2px] bg-gray-100"></span>
                    Scroll for more
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($spotlightBusinesses as $index => $business)
                <div class="reveal-on-scroll group" style="transition-delay: {{ $index * 100 }}ms">
                    <a href="{{ route('businesses.show', $business) }}"
                        class="uco-glass-light block overflow-hidden rounded-[3rem] p-3 shadow-[0_40px_100px_rgba(0,0,0,0.04)] transition-all duration-700 hover:-translate-y-4 hover:shadow-3xl hover:border-uco-orange-200/50">
                        
                        {{-- Immersive Showcase Image --}}
                        <div class="relative aspect-[16/10] w-full overflow-hidden rounded-xl bg-gray-100">
                            @php
                                $coverImage = $business->products->first()?->photo_url ?? null;
                            @endphp
                            
                            @if ($coverImage)
                                <img src="{{ $coverImage }}" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110">
                            @else
                                <div class="uco-placeholder-mesh flex h-full w-full items-center justify-center">
                                    <div class="relative">
                                        <div class="absolute inset-0 blur-2xl bg-uco-orange-300/20 rounded-full"></div>
                                        <i class="bi bi-lightning-charge text-5xl text-uco-orange-200/40 relative z-10"></i>
                                    </div>
                                </div>
                            @endif

                            {{-- Business Logo Overlay --}}
                            <div class="absolute bottom-4 left-6 h-16 w-16 overflow-hidden rounded-lg bg-white p-2.5 shadow-2xl transition-transform duration-700 group-hover:scale-110 group-hover:-translate-y-2">
                                <img src="{{ $business->logo_url }}" class="h-full w-full object-contain">
                            </div>

                            {{-- Category Tag --}}
                            <div class="absolute top-4 right-6 rounded-full bg-white/90 px-4 py-1 backdrop-blur-md shadow-sm border border-white/50">
                                <span class="text-[9px] font-black uppercase tracking-widest text-uco-orange-600">
                                    {{ $business->category->name ?? 'Venture' }}
                                </span>
                            </div>
                        </div>
                        
                        {{-- Content & Founder --}}
                        <div class="p-6 space-y-6">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xl font-[900] text-gray-900 tracking-tight group-hover:text-uco-orange-600 transition-colors">
                                        {{ $business->name }}
                                    </h4>
                                </div>
                                <p class="text-sm text-gray-500 leading-relaxed line-clamp-3 font-medium opacity-80">
                                    {{ $business->description }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between border-t border-gray-100 pt-6">
                                {{-- Founder Profile --}}
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 overflow-hidden rounded-full border-2 border-uco-orange-100 shadow-sm">
                                        @if($business->user->profile_photo_url)
                                            <img src="{{ $business->user->profile_photo_url }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-uco-orange-50 to-uco-orange-100/40 text-[11px] font-black text-uco-orange-500">
                                                {{ strtoupper(substr($business->user->name ?? 'F', 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Founder</span>
                                        <span class="text-[13px] font-bold text-gray-900">{{ $business->user->name ?? 'Founder' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5 text-gray-400">
                                    
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @empty
                    <div class="col-span-full uco-placeholder-mesh relative rounded-[3rem] border border-dashed border-gray-200 px-6 py-24 text-center">
                        <div class="relative z-10 space-y-6">
                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-white shadow-sm">
                                <i class="bi bi-rocket-takeoff text-3xl text-uco-orange-400 animate-pulse"></i>
                            </div>
                            <div class="space-y-2">
                                <p class="text-2xl font-[900] text-gray-950 tracking-tight">Venture Pipeline Empty</p>
                                <p class="text-base font-medium text-gray-500 max-w-md mx-auto">Our next generation of featured preneurs is currently under review. Check back soon for new arrivals.</p>
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
