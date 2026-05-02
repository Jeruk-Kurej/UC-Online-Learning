<x-app-layout>
    <div class="relative overflow-hidden bg-white">
        <style>
            @media (min-width: 1024px) {
                .hero-grid-container { 
                    display: grid !important; 
                    grid-template-columns: repeat(12, minmax(0, 1fr)) !important; 
                }
                .hero-left-content { grid-column: span 7 / span 7 !important; }
                .hero-grid-preview { 
                    display: block !important; 
                    grid-column: span 5 / span 5 !important;
                }
            }
            .hero-grid-container { display: block; }
            .hero-grid-preview { display: none; }
        </style>
        {{-- Hero Section --}}
        <section class="relative pt-20 pb-32 overflow-hidden">
            <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 items-center gap-12 hero-grid-container">
                    {{-- Left Content --}}
                    <div x-data="{
                            words: ['Innovative', 'Local', 'Student-Led', 'Alumni-Built'],
                            current: 0,
                            timer: null,
                            reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
                            startRotation() {
                                if (this.reducedMotion) return;
                                this.timer = setInterval(() => {
                                    this.current = (this.current + 1) % this.words.length;
                                }, 3400);
                            },
                            stopRotation() {
                                if (this.timer) clearInterval(this.timer);
                            }
                        }" x-init="startRotation();
                        window.addEventListener('beforeunload', () => stopRotation(), { once: true })" 
                        class="space-y-6 hero-left-content">
                        
                        <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-uco-orange-700">
                            UCO Business Showcase
                        </span>

                        <div class="space-y-3">
                            <h1 class="text-4xl font-extrabold text-gray-900 md:text-6xl tracking-tight">
                                <span class="block leading-tight">Discover</span>
                                <span class="relative mt-1 block h-[1.1em] min-w-[12ch] overflow-hidden leading-none text-uco-orange-600 md:min-w-[13ch]">
                                    <template x-for="(word, index) in words" :key="word">
                                        <span x-show="index === current"
                                            x-transition:enter="transition ease-out duration-420"
                                            x-transition:enter-start="opacity-0 translate-y-2 blur-[1px]"
                                            x-transition:enter-end="opacity-100 translate-y-0 blur-0"
                                            x-transition:leave="transition ease-in duration-300"
                                            x-transition:leave-start="opacity-100 translate-y-0 blur-0"
                                            x-transition:leave-end="opacity-0 -translate-y-2 blur-[1px]"
                                            class="absolute inset-0 leading-none" x-text="word"></span>
                                    </template>
                                </span>
                                <span class="mt-1 block leading-tight">from UCO Students & Alumni</span>
                            </h1>
                            <p class="max-w-2xl text-base leading-relaxed text-gray-600 md:text-lg">
                                Explore a vibrant ecosystem of product and service ventures built by our community — from
                                first launch to growing brands.
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('businesses.index') }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-uco-orange-500 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-uco-orange-200 transition hover:-translate-y-0.5 hover:bg-uco-orange-600">
                                Explore Directory
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Right Featured Preview --}}
                    <div class="lg:col-span-5 hero-grid-preview">
                        <div class="grid grid-cols-2 gap-4">
                            @foreach ($spotlightBusinesses as $business)
                                <a href="{{ route('businesses.show', $business) }}"
                                    class="group relative overflow-hidden rounded-[2rem] bg-gray-50 p-4 transition-all hover:bg-white hover:shadow-2xl hover:-translate-y-1.5 border border-transparent hover:border-uco-orange-100">
                                    <div class="aspect-square w-full overflow-hidden rounded-2xl bg-white shadow-inner mb-3 flex items-center justify-center p-4">
                                        @if ($business->logo_url)
                                            <img src="{{ $business->logo_url }}" class="max-h-full max-w-full object-contain transition duration-500 group-hover:scale-110">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-uco-orange-50 to-uco-yellow-50">
                                                <i class="bi bi-shop text-3xl text-uco-orange-500"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="px-1">
                                        <h4 class="truncate text-sm font-black text-gray-900 leading-none mb-1">{{ $business->name }}</h4>
                                        <p class="truncate text-[9px] font-black uppercase tracking-widest text-uco-orange-500/60">{{ $business->category->name ?? 'Business' }}</p>
                                    </div>
                                </a>
                            @endforeach
                            @if($spotlightBusinesses->count() < 4)
                                @for($i = 0; $i < (4 - $spotlightBusinesses->count()); $i++)
                                    <div class="rounded-[2rem] bg-gray-50/50 border border-dashed border-gray-200 flex items-center justify-center aspect-square">
                                        <i class="bi bi-plus-circle text-gray-200 text-2xl"></i>
                                    </div>
                                @endfor
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Top Entrepreneur Profiles --}}
        @if($topEntrepreneurs->count())
            <section class="py-24 bg-gray-50/50 border-y border-gray-100">
                <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-end justify-between mb-16">
                        <div>
                            <span class="text-xs font-bold text-uco-orange-500 uppercase tracking-widest">Student Entrepreneurs</span>
                            <h2 class="text-4xl font-black text-gray-900 tracking-tight mt-2">Featured Founders</h2>
                        </div>
                        <a href="{{ route('businesses.index') }}" class="text-uco-orange-500 font-bold hover:underline hidden md:block">Explore All →</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        @foreach($topEntrepreneurs as $student)
                            <div class="bg-white border border-gray-100 rounded-[2rem] overflow-hidden hover:shadow-2xl transition-all duration-500 group relative">
                                <div class="absolute top-0 right-0 p-8">
                                    <i class="bi bi-quote text-6xl text-uco-orange-500/10 group-hover:text-uco-orange-500/20 transition-colors"></i>
                                </div>
                                <div class="p-8 space-y-6 relative z-10">
                                    <div class="flex items-center gap-5">
                                        <div class="w-20 h-20 rounded-2xl overflow-hidden bg-gray-50 border-2 border-uco-orange-100 flex-shrink-0 shadow-inner">
                                            <img src="{{ $student->profile_photo_url }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="text-xl font-black text-gray-900 leading-tight truncate">{{ $student->name }}</h3>
                                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">{{ $student->major }}</p>
                                            <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase">
                                                Founder
                                            </span>
                                        </div>
                                    </div>
                                    @if($student->testimony)
                                        <p class="text-sm text-gray-600 italic leading-relaxed line-clamp-3">
                                            "{{ $student->testimony }}"
                                        </p>
                                    @endif
                                    @if($student->businesses->first())
                                        <a href="{{ route('businesses.show', $student->businesses->first()) }}" class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl hover:bg-uco-orange-500 hover:text-white transition-all group/biz shadow-sm">
                                            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0 shadow-inner">
                                                <img src="{{ $student->businesses->first()->logo_url }}" class="w-full h-full object-contain">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-black truncate">{{ $student->businesses->first()->name }}</p>
                                                <p class="text-[9px] uppercase font-bold opacity-60">{{ $student->businesses->first()->category->name ?? 'Business' }}</p>
                                            </div>
                                            <i class="bi bi-arrow-right group-hover/biz:translate-x-1 transition-transform"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- Top Intrapreneur Profiles --}}
        @if($topIntrapreneurs->count())
            <section class="py-24">
                <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-end justify-between mb-16">
                        <div>
                            <span class="text-xs font-bold text-blue-500 uppercase tracking-widest">Student Intrapreneurs</span>
                            <h2 class="text-4xl font-black text-gray-900 tracking-tight mt-2">Industry Professionals</h2>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                        @foreach($topIntrapreneurs as $student)
                            <div class="bg-white border border-gray-100 rounded-3xl p-6 hover:shadow-xl hover:border-blue-200 transition-all group text-center relative overflow-hidden">
                                <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-50 border-2 border-blue-50 mx-auto mb-4 shadow-inner">
                                    @if($student->profile_photo_url)
                                        <img src="{{ $student->profile_photo_url }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center"><i class="bi bi-person text-2xl text-gray-300"></i></div>
                                    @endif
                                </div>
                                <h4 class="font-black text-gray-900 truncate text-sm">{{ $student->name }}</h4>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-tighter mb-3">{{ $student->major }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[9px] font-black uppercase">
                                    Intrapreneur
                                </span>
                                @if($student->companies->first())
                                    <div class="mt-4 pt-4 border-t border-gray-50">
                                        <p class="text-[10px] text-gray-400 font-black uppercase truncate">{{ $student->companies->first()->name }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- Browse by Category --}}
        <!-- <section class="py-20 border-y border-gray-100 bg-gray-50/50">
            <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-12">
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight">Browse by Industry</h2>
                    <a href="{{ route('businesses.index') }}" class="text-uco-orange-500 font-bold hover:underline">View All</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                    @foreach($businessTypes as $type)
                        <a href="{{ route('businesses.index', ['category' => $type->id]) }}" class="group bg-white p-6 rounded-2xl border hover:border-uco-orange-300 hover:shadow-lg transition text-center space-y-3">
                            <div class="w-12 h-12 bg-gray-50 rounded-xl mx-auto flex items-center justify-center text-gray-400 group-hover:bg-uco-orange-50 group-hover:text-uco-orange-500 transition">
                                <i class="bi bi-tag-fill text-xl"></i>
                            </div>
                            <p class="text-xs font-bold text-gray-900 truncate">{{ $type->name }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section> -->

        {{-- Spotlight Businesses --}}
        @if($spotlightBusinesses->count())
            <section class="py-24">
                <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="mb-16">
                        <h2 class="text-4xl font-black text-gray-900 tracking-tight">Recent Ventures</h2>
                        <p class="text-gray-500 font-medium mt-2">Latest startups joining our community.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        @foreach($spotlightBusinesses as $business)
                            <div class="group bg-white border border-gray-100 rounded-[2rem] overflow-hidden hover:border-uco-orange-200 hover:shadow-2xl transition-all duration-500 flex flex-col">
                                <div class="aspect-[4/3] bg-gray-50 relative overflow-hidden flex-shrink-0">
                                    @if($business->logo_url)
                                        <img src="{{ $business->logo_url }}" class="w-full h-full object-contain p-8 group-hover:scale-110 transition-transform duration-700">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-200 bg-gradient-to-br from-gray-50 to-gray-100">
                                            <i class="bi bi-building text-6xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-8 flex flex-col flex-1">
                                    <h3 class="text-xl font-black text-gray-900 group-hover:text-uco-orange-500 transition line-clamp-2 leading-tight mb-3">{{ $business->name }}</h3>
                                    <p class="text-sm text-gray-500 line-clamp-2 font-medium leading-relaxed mb-4">{{ $business->description }}</p>
                                    <div class="mb-6">
                                        <span class="px-3 py-1 bg-gray-50 rounded-full text-[9px] font-black uppercase tracking-widest text-gray-400 border border-gray-100">
                                            {{ $business->category->name ?? 'Startup' }}
                                        </span>
                                    </div>
                                    <div class="mt-auto pt-6 border-t border-gray-50 flex items-center justify-between">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <div class="w-7 h-7 rounded-full bg-uco-orange-50 border border-uco-orange-100 flex items-center justify-center flex-shrink-0">
                                                <i class="bi bi-person-fill text-xs text-uco-orange-600"></i>
                                            </div>
                                            <span class="text-xs font-black text-gray-700 truncate">{{ $business->user->name ?? 'Founder' }}</span>
                                        </div>
                                        <a href="{{ route('businesses.show', $business) }}" class="text-gray-200 group-hover:text-uco-orange-500 transition-all hover:scale-110">
                                            <i class="bi bi-arrow-right-circle-fill text-3xl"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- Testimonials --}}
        @if($testimonies->count())
            <section class="py-24 bg-gray-900 text-white overflow-hidden relative">
                <div class="absolute top-0 left-0 w-full h-full opacity-10">
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[1000px] h-[1000px] bg-uco-orange-500 rounded-full blur-[120px]"></div>
                </div>
                <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="text-center mb-16 space-y-4">
                        <h2 class="text-4xl font-black tracking-tight">Student Voices</h2>
                        <p class="text-gray-400">How studying at UCO impacted their entrepreneurial journey.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        @foreach($testimonies as $user)
                            <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-3xl space-y-6 hover:bg-white/10 transition">
                                <i class="bi bi-quote text-4xl text-uco-orange-500"></i>
                                <p class="text-lg font-medium italic text-gray-200">"{{ Str::limit($user->testimony, 200) }}"</p>
                                <div class="flex items-center gap-4 pt-4 border-t border-white/10">
                                    <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-800">
                                        @if($user->profile_photo_url)
                                            <img src="{{ $user->profile_photo_url }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-600"><i class="bi bi-person-fill"></i></div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500 uppercase font-bold tracking-widest">{{ $user->major }}</p>
                                    </div>
                                </div>
                                @if($user->businesses->first())
                                    <div class="text-xs text-gray-500">
                                        <i class="bi bi-briefcase mr-1"></i> {{ $user->businesses->first()->name }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- CTA --}}
        <section class="py-32 px-6">
            <div class="max-w-5xl mx-auto bg-uco-orange-500 rounded-[3rem] p-12 md:p-20 text-center text-white relative overflow-hidden shadow-2xl">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-black/10 rounded-full blur-3xl -ml-32 -mb-32"></div>
                <h2 class="text-4xl md:text-5xl font-black mb-8 relative z-10">Are you a UCO student, and have a business?</h2>
                <p class="text-lg md:text-xl text-white/90 mb-12 max-w-2xl mx-auto relative z-10">
                    Join the directory and showcase your venture to the world.
                </p>
                <a href="{{ route('login') }}" class="relative z-10 px-10 py-5 bg-white text-uco-orange-500 font-black rounded-2xl hover:bg-gray-50 transition shadow-xl hover:-translate-y-1 inline-block">
                    Get Started
                </a>
            </div>
        </section>
    </div>
</x-app-layout>
