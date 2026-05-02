<x-app-layout>
    <div class="space-y-24 pb-32 bg-white">
        {{-- High-Fidelity "Better" Hero Section --}}
        <section class="group relative overflow-hidden rounded-[4.5rem] bg-[#FFF9F2] px-10 py-24 md:px-24 md:py-32 lg:px-32 mx-4 mt-4">
            {{-- Background Effects --}}
            <div class="uco-hero-mesh opacity-90"></div>
            <div class="uco-noise-overlay"></div>
            
            {{-- Dynamic Floating Orbs with Subtle Motion --}}
            <div class="uco-float-orb uco-float-orb--one opacity-40 mix-blend-multiply transition-transform duration-[10s] group-hover:translate-x-12 group-hover:-translate-y-8"></div>
            <div class="uco-float-orb uco-float-orb--two opacity-30 mix-blend-multiply transition-transform duration-[12s] group-hover:-translate-x-16 group-hover:translate-y-12"></div>
            
            <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-start">
                {{-- Left Content --}}
                <div class="space-y-10">
                    <div class="inline-flex items-center rounded-full border border-uco-orange-200/50 bg-white/40 px-6 py-2 backdrop-blur-md shadow-sm">
                        <span class="flex h-2 w-2 rounded-full bg-uco-orange-500 mr-3 animate-pulse"></span>
                        <span class="text-[12px] font-black uppercase tracking-[0.25em] text-uco-orange-700">
                            UCO Premium Showcase
                        </span>
                    </div>

                    <div class="space-y-8">
                        <h1 class="text-5xl font-[900] text-slate-950 md:text-6xl lg:text-7xl tracking-[-0.04em] leading-[1.05] max-w-4xl">
                            Discover <br>
                            <span class="uco-text-gradient-orange">Innovative</span><br>
                            Businesses <span class="text-slate-950/80">from</span> <br>
                            <span class="whitespace-nowrap italic">UCO Community</span>
                        </h1>
                        <p class="max-w-lg text-lg font-medium leading-relaxed text-slate-600/80 tracking-tight">
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
                                    class="uco-glass-light block overflow-hidden rounded-[2.5rem] p-2 shadow-[0_20px_60px_rgba(0,0,0,0.03)] transition-all duration-700 hover:-translate-y-2 hover:shadow-2xl hover:border-uco-orange-200/50">
                                    
                                    <div class="space-y-3">
                                        {{-- Compact Showcase Image --}}
                                        <div class="relative aspect-video w-full overflow-hidden rounded-[2rem] bg-slate-100">
                                            @php
                                                $coverImage = $business->products->first()?->photo_url ?? null;
                                            @endphp
                                            
                                            @if ($coverImage)
                                                <img src="{{ $coverImage }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center bg-uco-orange-50/50">
                                                    <i class="bi bi-images text-xl text-uco-orange-200"></i>
                                                </div>
                                            @endif

                                            {{-- Logo Overlay --}}
                                            <div class="absolute bottom-2 left-3 h-8 w-8 overflow-hidden rounded-lg bg-white p-1 shadow-lg">
                                                <img src="{{ $business->logo_url }}" class="h-full w-full object-contain">
                                            </div>
                                        </div>

                                        {{-- Content --}}
                                        <div class="px-3 pb-2">
                                            <h4 class="text-[15px] font-[900] text-slate-900 truncate tracking-tight group-hover/card:text-uco-orange-600 transition-colors">
                                                {{ $business->name }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-1">
                                                <div class="h-4 w-4 overflow-hidden rounded-full border border-uco-orange-100">
                                                    <img src="{{ $business->user->profile_photo_url }}" class="h-full w-full object-cover">
                                                </div>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest truncate max-w-[80px]">
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

        {{-- Better Featured Grid Section --}}
        <section class="max-w-[95rem] mx-auto px-8 md:px-12 space-y-20">
            <div class="reveal-on-scroll flex flex-col md:flex-row md:items-end justify-between gap-10">
                <div class="space-y-4">
                    <h2 class="text-6xl font-[900] text-slate-950 tracking-tighter">Featured Ventures</h2>
                    <p class="text-xl font-medium text-slate-500 max-w-2xl leading-relaxed">
                        Hand-picked businesses that represent the highest standards of innovation and execution within our network.
                    </p>
                </div>
                <div class="flex items-center gap-4 text-xs font-black text-slate-300 uppercase tracking-[0.25em]">
                    <span class="w-12 h-[2px] bg-slate-100"></span>
                    Scroll for more
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($spotlightBusinesses as $index => $business)
                <div class="reveal-on-scroll group" style="transition-delay: {{ $index * 100 }}ms">
                    <a href="{{ route('businesses.show', $business) }}"
                        class="uco-glass-light block overflow-hidden rounded-[3rem] p-3 shadow-[0_40px_100px_rgba(0,0,0,0.04)] transition-all duration-700 hover:-translate-y-4 hover:shadow-3xl hover:border-uco-orange-200/50">
                        
                        {{-- Immersive Showcase Image --}}
                        <div class="relative aspect-[16/10] w-full overflow-hidden rounded-[2.5rem] bg-slate-100">
                            @php
                                $coverImage = $business->products->first()?->photo_url ?? null;
                            @endphp
                            
                            @if ($coverImage)
                                <img src="{{ $coverImage }}" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-uco-orange-50 to-uco-orange-100/30">
                                    <i class="bi bi-images text-5xl text-uco-orange-200/50"></i>
                                </div>
                            @endif

                            {{-- Business Logo Overlay --}}
                            <div class="absolute bottom-4 left-6 h-16 w-16 overflow-hidden rounded-2xl bg-white p-2.5 shadow-2xl transition-transform duration-700 group-hover:scale-110 group-hover:-translate-y-2">
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
                                    <h4 class="text-xl font-[900] text-slate-900 tracking-tight group-hover:text-uco-orange-600 transition-colors">
                                        {{ $business->name }}
                                    </h4>
                                </div>
                                <p class="text-sm text-slate-500 leading-relaxed line-clamp-3 font-medium opacity-80">
                                    {{ $business->description }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between border-t border-slate-100 pt-6">
                                {{-- Founder Profile --}}
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 overflow-hidden rounded-full border-2 border-uco-orange-100 shadow-sm">
                                        <img src="{{ $business->user->profile_photo_url }}" class="h-full w-full object-cover">
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Founder</span>
                                        <span class="text-[13px] font-bold text-slate-900">{{ $business->user->name ?? 'Founder' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5 text-slate-400">
                                    
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </section>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.reveal-on-scroll').forEach(el => observer.observe(el));
            
            // Subtle Mouse Tracking for Hero
            const hero = document.querySelector('section');
            hero.addEventListener('mousemove', (e) => {
                const orbs = document.querySelectorAll('.uco-float-orb');
                const x = (e.clientX / window.innerWidth - 0.5) * 40;
                const y = (e.clientY / window.innerHeight - 0.5) * 40;
                
                orbs[0].style.transform = `translate(${x}px, ${y}px)`;
                orbs[1].style.transform = `translate(${-x}px, ${-y}px)`;
            });
        });
    </script>
    @endpush
</x-app-layout>
