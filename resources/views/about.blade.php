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

    <div class="relative overflow-hidden font-sans bg-white pb-16">
        @if(auth()->check() && auth()->user()->isAdmin())
            <div class="flex justify-end max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-4 relative z-20">
                <a href="{{ route('pages.edit', 'about') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 text-xs font-extrabold shadow-sm transition-all">
                    <i class="bi bi-pencil-square"></i> Edit About Page (CMS)
                </a>
            </div>
        @endif

        {{-- Dynamic Renderer for Sections --}}
        @foreach($sections as $sec)
            @php $type = $sec['type'] ?? ''; @endphp

            {{-- SECTION TYPE: HERO --}}
            @if($type === 'hero')
                <section class="relative py-16 px-4 sm:px-6 lg:px-8 overflow-hidden bg-gradient-to-b from-amber-50/70 via-white to-orange-50/40 border-b border-orange-100/60">
                    <div class="max-w-[1600px] mx-auto text-center relative z-10 reveal-on-scroll">
                        @if(!empty($sec['badge']))
                            <div class="mb-4">
                                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border border-orange-200 bg-orange-50 text-xs font-extrabold uppercase tracking-widest text-orange-700 shadow-sm">
                                    <i class="bi bi-info-circle-fill text-orange-500"></i> {{ $sec['badge'] }}
                                </span>
                            </div>
                        @endif
                        <h1 class="text-4xl md:text-6xl font-black text-gray-950 tracking-tight leading-tight mb-6 max-w-4xl mx-auto">
                            {!! e($sec['title'] ?? '') !!}
                        </h1>
                        <p class="max-w-2xl mx-auto text-base md:text-lg text-gray-600 leading-relaxed font-medium">
                            {{ $sec['subtitle'] ?? '' }}
                        </p>
                    </div>
                </section>

            {{-- SECTION TYPE: FEATURE CARDS GRID --}}
            @elseif($type === 'feature_cards')
                <section class="py-16 md:py-20 bg-white px-4 sm:px-6 lg:px-8 relative overflow-hidden">
                    <div class="max-w-[1600px] mx-auto relative z-10">
                        <div class="text-center max-w-3xl mx-auto mb-14 space-y-3 reveal-on-scroll">
                            @if(!empty($sec['badge']))
                                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border border-orange-200 bg-orange-50 text-xs font-extrabold uppercase tracking-widest text-orange-700">
                                    {{ $sec['badge'] }}
                                </span>
                            @endif
                            <h2 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">
                                {{ $sec['title'] ?? '' }}
                            </h2>
                            <p class="text-base text-gray-500 font-medium leading-relaxed">
                                {{ $sec['subtitle'] ?? '' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach(($sec['cards'] ?? []) as $card)
                                <div class="space-y-5 reveal-on-scroll p-8 rounded-2xl border border-gray-200 bg-white shadow-sm hover:border-orange-200 hover:shadow-md transition-all duration-300">
                                    <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center text-2xl shadow-sm border border-orange-100">
                                        <i class="bi {{ $card['icon'] ?? 'bi-rocket-takeoff' }}"></i>
                                    </div>
                                    <h3 class="text-xl font-extrabold text-gray-900 leading-snug">{{ $card['title'] ?? '' }}</h3>
                                    <p class="text-sm text-gray-500 leading-relaxed font-normal">{{ $card['description'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

            {{-- SECTION TYPE: STATS GRID --}}
            @elseif($type === 'stats_grid')
                <section class="py-16 md:py-20 px-4 sm:px-6 lg:px-8 max-w-[1600px] mx-auto">
                    <div class="bg-slate-900 rounded-3xl border border-slate-800 p-10 md:p-16 text-white relative overflow-hidden shadow-sm">
                        <div class="text-center relative z-10 reveal-on-scroll">
                            <div class="relative inline-block mb-12">
                                <h2 class="text-2xl md:text-4xl font-black text-white tracking-tight">{{ $sec['title'] ?? '' }}</h2>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                                @foreach(($sec['items'] ?? []) as $st)
                                    <div class="space-y-1">
                                        <p class="text-3xl md:text-5xl font-black text-[#f7931e] tracking-tight">{{ $st['number'] ?? '' }}</p>
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $st['label'] ?? '' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>

            {{-- SECTION TYPE: TEXT / FAQ BLOCK --}}
            @elseif($type === 'text_block')
                <section class="py-12 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
                    <h2 class="text-2xl md:text-3xl font-black text-gray-900 mb-4 tracking-tight">{{ $sec['heading'] ?? '' }}</h2>
                    <p class="text-base text-gray-600 font-normal leading-relaxed whitespace-pre-line">{{ $sec['content'] ?? '' }}</p>
                </section>

            {{-- SECTION TYPE: CTA BANNER --}}
            @elseif($type === 'cta_banner')
                <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-[1600px] mx-auto">
                    <div class="bg-slate-900 rounded-3xl p-10 md:p-16 text-center text-white relative overflow-hidden border border-slate-800 shadow-sm reveal-on-scroll">
                        <div class="relative z-10 max-w-3xl mx-auto space-y-6">
                            <h2 class="text-3xl md:text-4xl font-black leading-tight tracking-tight">
                                {!! e($sec['heading'] ?? '') !!}
                            </h2>
                            <p class="text-base text-slate-300 max-w-2xl mx-auto font-normal leading-relaxed">
                                {{ $sec['subtitle'] ?? '' }}
                            </p>
                            <div class="flex flex-wrap justify-center gap-4 pt-2">
                                @if(!empty($sec['primary_btn_text']))
                                    <a href="{{ route('login') }}" class="px-7 py-3.5 bg-gradient-to-r from-[#f7931e] to-[#fdb913] hover:from-[#e0831a] hover:to-[#e6a600] text-white font-extrabold rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                                        {{ $sec['primary_btn_text'] }}
                                    </a>
                                @endif
                                @if(!empty($sec['secondary_btn_text']))
                                    <a href="{{ route('businesses.index') }}" class="px-7 py-3.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl border border-white/20 transition-all duration-200">
                                        {{ $sec['secondary_btn_text'] }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        @endforeach
    </div>
</x-app-layout>
