<x-app-layout>
    @section('title', 'About Us')

    @php
        $content = is_array($page?->content_json) ? $page->content_json : (json_decode($page?->content_json ?? '[]', true) ?: []);
        
        $sections = $content['sections'] ?? [];

        // Fallback default sections if page has no sections saved yet
        if (empty($sections)) {
            $sections = [
                [
                    'type' => 'hero',
                    'badge' => $content['hero']['badge'] ?? 'About UC Online Learning',
                    'title' => $content['hero']['title'] ?? 'Building the Future of Student & Alumni Entrepreneurship',
                    'subtitle' => $content['hero']['subtitle'] ?? 'Connecting founders, intrapreneurs, and corporate innovators across Universitas Ciputra.',
                ],
                [
                    'type' => 'feature_cards',
                    'badge' => $content['pillars']['badge'] ?? 'Pillars of Excellence',
                    'title' => $content['pillars']['title'] ?? 'Built for Sustainable Impact',
                    'subtitle' => $content['pillars']['subtitle'] ?? 'Designed to support founders and intrapreneurs at every phase of their growth journey.',
                    'cards' => $content['pillars']['cards'] ?? [
                        ['title' => 'Rapid Launch', 'description' => 'We provide the tools and network needed to transform academic theories into viable market products within weeks, not years.', 'icon' => 'bi-rocket-takeoff'],
                        ['title' => 'Global Network', 'description' => 'Connect with a diverse community of alumni mentors, industry experts, and fellow entrepreneurs across all major industries.', 'icon' => 'bi-people'],
                        ['title' => 'Scalable Growth', 'description' => 'From local startups to multinational enterprises, our platform supports scaling businesses at every stage of their lifecycle.', 'icon' => 'bi-graph-up-arrow'],
                    ]
                ],
                [
                    'type' => 'stats_grid',
                    'title' => $content['stats']['title'] ?? 'Driving Community Impact',
                    'items' => $content['stats']['items'] ?? [
                        ['number' => '500+', 'label' => 'Active Ventures'],
                        ['number' => '1200+', 'label' => 'Graduated Founders'],
                        ['number' => '24', 'label' => 'Industry Categories'],
                        ['number' => '15+', 'label' => 'Years of Heritage'],
                    ]
                ],
                [
                    'type' => 'cta_banner',
                    'heading' => $content['cta']['heading'] ?? 'Ready to build your legacy?',
                    'subtitle' => $content['cta']['subtitle'] ?? 'Join the UCO community today and gain access to a world of entrepreneurial opportunities.',
                    'primary_btn_text' => $content['cta']['primary_btn_text'] ?? 'Get Started Now',
                    'secondary_btn_text' => $content['cta']['secondary_btn_text'] ?? 'Explore Directory',
                ]
            ];
        }
    @endphp

    <div class="relative overflow-hidden font-sans bg-white">
        @if(auth()->check() && auth()->user()->isAdmin())
            <div class="flex justify-end max-w-[1600px] mx-auto px-6 pt-4 relative z-20">
                <a href="{{ route('pages.edit', 'about') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800 text-xs font-extrabold shadow-md transition-all">
                    <i class="bi bi-pencil-square"></i> Edit About Page (CMS)
                </a>
            </div>
        @endif

        {{-- Dynamic Renderer for Sections --}}
        @foreach($sections as $sec)
            @php $type = $sec['type'] ?? ''; @endphp

            {{-- SECTION TYPE: HERO --}}
            @if($type === 'hero')
                <section class="relative pt-12 pb-16 px-6 overflow-hidden bg-gradient-to-b from-orange-50/60 to-white">
                    <div class="uco-hero-mesh"></div>
                    <div class="max-w-[1600px] mx-auto text-center relative z-10 reveal-on-scroll">
                        @if(!empty($sec['badge']))
                            <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-xs font-black uppercase tracking-widest text-uco-orange-600 mb-6">
                                {{ $sec['badge'] }}
                            </span>
                        @endif
                        <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-gray-950 tracking-tight mb-6">
                            {!! e($sec['title'] ?? '') !!}
                        </h1>
                        <p class="max-w-3xl mx-auto text-base md:text-lg text-gray-600 leading-relaxed font-medium">
                            {{ $sec['subtitle'] ?? '' }}
                        </p>
                    </div>
                </section>

            {{-- SECTION TYPE: FEATURE CARDS GRID --}}
            @elseif($type === 'feature_cards')
                <section class="py-20 bg-white px-6 relative overflow-hidden">
                    <div class="uco-ambient-glow uco-ambient-glow--purple uco-floating-blob-slow top-1/2 left-1/2 opacity-40"></div>
                    <div class="max-w-[1600px] mx-auto relative z-10">
                        <div class="text-center max-w-3xl mx-auto mb-16 space-y-4 reveal-on-scroll">
                            @if(!empty($sec['badge']))
                                <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-xs font-extrabold uppercase tracking-widest text-uco-orange-600">
                                    {{ $sec['badge'] }}
                                </span>
                            @endif
                            <h2 class="text-3xl md:text-5xl font-black text-gray-900 tracking-tight">
                                {{ $sec['title'] ?? '' }}
                            </h2>
                            <p class="text-base md:text-lg text-gray-500 font-medium">
                                {{ $sec['subtitle'] ?? '' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            @foreach(($sec['cards'] ?? []) as $card)
                                <div class="space-y-6 reveal-on-scroll uco-premium-card p-8 rounded-3xl border border-gray-100 bg-white shadow-sm hover:shadow-xl transition-all duration-300">
                                    <div class="w-16 h-16 bg-uco-orange-50 text-uco-orange-500 rounded-2xl flex items-center justify-center text-3xl shadow-sm border border-uco-orange-100">
                                        <i class="bi {{ $card['icon'] ?? 'bi-rocket-takeoff' }}"></i>
                                    </div>
                                    <h3 class="text-2xl font-black text-gray-900">{{ $card['title'] ?? '' }}</h3>
                                    <p class="text-gray-500 leading-relaxed font-medium">{{ $card['description'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

            {{-- SECTION TYPE: STATS GRID --}}
            @elseif($type === 'stats_grid')
                <section class="py-24 bg-slate-950 px-6 relative overflow-hidden text-white">
                    <div class="absolute inset-0 opacity-10 pointer-events-none">
                        <div class="w-full h-full bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:40px_40px]"></div>
                    </div>
                    <div class="max-w-[1600px] mx-auto relative z-10 text-center reveal-on-scroll">
                        <div class="relative inline-block mb-16">
                            <h2 class="text-3xl md:text-5xl font-black text-white relative z-10 tracking-tight">{{ $sec['title'] ?? '' }}</h2>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-12">
                            @foreach(($sec['items'] ?? []) as $st)
                                <div class="space-y-2">
                                    <p class="text-4xl md:text-6xl font-black text-uco-orange-500 tracking-tighter">{{ $st['number'] ?? '' }}</p>
                                    <p class="text-xs md:text-sm font-extrabold text-gray-400 uppercase tracking-widest">{{ $st['label'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

            {{-- SECTION TYPE: TEXT / FAQ BLOCK --}}
            @elseif($type === 'text_block')
                <section class="py-16 px-6 max-w-4xl mx-auto">
                    <h2 class="text-2xl md:text-4xl font-black text-slate-900 mb-4">{{ $sec['heading'] ?? '' }}</h2>
                    <p class="text-base md:text-lg text-slate-600 font-medium leading-relaxed whitespace-pre-line">{{ $sec['content'] ?? '' }}</p>
                </section>

            {{-- SECTION TYPE: CTA BANNER --}}
            @elseif($type === 'cta_banner')
                <section class="py-24 px-6">
                    <div class="max-w-5xl mx-auto bg-slate-950 rounded-[3rem] p-12 md:p-20 text-center text-white relative overflow-hidden border border-slate-800/80 shadow-[0_24px_50px_-12px_rgba(0,0,0,0.5)] reveal-on-scroll">
                        <div class="absolute inset-0 bg-[radial-gradient(#ffffff05_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none opacity-60"></div>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[70%] h-[70%] bg-gradient-to-tr from-uco-orange-500/20 to-yellow-500/10 rounded-full filter blur-[100px] pointer-events-none opacity-80 z-0"></div>
                        
                        <h2 class="text-3xl md:text-5xl font-black mb-6 relative z-10 leading-tight">
                            {!! e($sec['heading'] ?? '') !!}
                        </h2>
                        <p class="text-base md:text-lg text-slate-400 mb-10 max-w-2xl mx-auto relative z-10 font-medium leading-relaxed">
                            {{ $sec['subtitle'] ?? '' }}
                        </p>
                        <div class="flex flex-wrap justify-center gap-4 relative z-10">
                            @if(!empty($sec['primary_btn_text']))
                                <a href="{{ route('login') }}" class="px-8 py-4 bg-gradient-to-r from-uco-orange-500 to-amber-500 text-white font-extrabold rounded-2xl shadow-[0_8px_30px_rgba(247,147,30,0.35)] transition-all duration-300 hover:scale-[1.05] hover:shadow-[0_12px_40px_rgba(247,147,30,0.5)]">
                                    {{ $sec['primary_btn_text'] }}
                                </a>
                            @endif
                            @if(!empty($sec['secondary_btn_text']))
                                <a href="{{ route('businesses.index') }}" class="px-8 py-4 bg-white/5 hover:bg-white/10 text-white font-bold rounded-2xl border border-white/10 hover:border-white/20 transition-all duration-300 hover:scale-[1.05] backdrop-blur-md shadow-lg">
                                    {{ $sec['secondary_btn_text'] }}
                                </a>
                            @endif
                        </div>
                    </div>
                </section>
            @endif
        @endforeach
    </div>
</x-app-layout>
