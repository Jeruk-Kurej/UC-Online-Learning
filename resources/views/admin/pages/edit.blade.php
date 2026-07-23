<x-app-layout>
    @section('title', 'Edit Page: ' . $page->title)

    @php
        $content = is_string($page->content_json) ? json_decode($page->content_json, true) : ($page->content_json ?? []);
        
        // Hero defaults
        $heroBadge = $content['hero']['badge'] ?? 'About UC Online Learning';
        $heroTitle = $content['hero']['title'] ?? 'Building the Future of Student & Alumni Entrepreneurship';
        $heroSubtitle = $content['hero']['subtitle'] ?? 'Connecting founders, intrapreneurs, and corporate innovators across Universitas Ciputra.';

        // Pillars defaults
        $pillarsBadge = $content['pillars']['badge'] ?? 'Pillars of Excellence';
        $pillarsTitle = $content['pillars']['title'] ?? 'Built for Sustainable Impact';
        $pillarsSubtitle = $content['pillars']['subtitle'] ?? 'Designed to support founders and intrapreneurs at every phase of their growth journey.';
        $pillarCards = $content['pillars']['cards'] ?? [
            ['title' => 'Rapid Launch', 'description' => 'We provide the tools and network needed to transform academic theories into viable market products within weeks, not years.', 'icon' => 'bi-rocket-takeoff'],
            ['title' => 'Global Network', 'description' => 'Connect with a diverse community of alumni mentors, industry experts, and fellow entrepreneurs across all major industries.', 'icon' => 'bi-people'],
            ['title' => 'Scalable Growth', 'description' => 'From local startups to multinational enterprises, our platform supports scaling businesses at every stage of their lifecycle.', 'icon' => 'bi-graph-up-arrow'],
        ];

        // Stats defaults
        $statsTitle = $content['stats']['title'] ?? 'Driving Community Impact';
        $statsItems = $content['stats']['items'] ?? [
            ['number' => '500+', 'label' => 'Active Ventures'],
            ['number' => '1200+', 'label' => 'Graduated Founders'],
            ['number' => '24', 'label' => 'Industry Categories'],
            ['number' => '15+', 'label' => 'Years of Heritage'],
        ];

        // CTA defaults
        $ctaHeading = $content['cta']['heading'] ?? 'Ready to build your legacy?';
        $ctaSubtitle = $content['cta']['subtitle'] ?? 'Join the UCO community today and gain access to a world of entrepreneurial opportunities.';
        $primaryBtnText = $content['cta']['primary_btn_text'] ?? 'Get Started Now';
        $secondaryBtnText = $content['cta']['secondary_btn_text'] ?? 'Explore Directory';
    @endphp

    <div class="py-12 px-6 max-w-[1200px] mx-auto font-sans">
        <div class="flex items-center justify-between mb-8">
            <div>
                <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-uco-orange-600 mb-2">
                    Non-Tech Visual CMS
                </span>
                <h1 class="text-3xl font-black text-slate-900">Edit {{ $page->title }}</h1>
                <p class="text-xs text-slate-500 font-medium mt-1">Fill out clean form fields below to update page headers, cards, stats, and text. No coding required!</p>
            </div>
            <a href="{{ route($page->slug) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-sm transition-all">
                View Public Page <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </div>
        
        <div class="space-y-8">
            @if($page->slug === 'about')
                {{-- 1. Hero Section Form --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 space-y-6">
                    <div class="border-b border-slate-100 pb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-orange-100 text-uco-orange-600 font-black flex items-center justify-center text-sm">1</span>
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Hero Header Banner</h2>
                            <p class="text-xs text-slate-400 font-medium">Main title and subtitle displayed at the top of the About page.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Top Tagline Badge</label>
                            <input type="text" id="hero_badge" value="{{ $heroBadge }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm focus:border-uco-orange-500">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Main Headline Title</label>
                            <input type="text" id="hero_title" value="{{ $heroTitle }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm focus:border-uco-orange-500">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Hero Subtitle Description</label>
                        <textarea id="hero_subtitle" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-medium text-slate-700 text-sm focus:border-uco-orange-500">{{ $heroSubtitle }}</textarea>
                    </div>
                </div>

                {{-- 2. Pillars of Excellence Section Form --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 space-y-6">
                    <div class="border-b border-slate-100 pb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 font-black flex items-center justify-center text-sm">2</span>
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Pillars of Excellence (3 Feature Cards)</h2>
                            <p class="text-xs text-slate-400 font-medium">Edit section title and content for each of the 3 value cards.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Section Badge</label>
                            <input type="text" id="pillars_badge" value="{{ $pillarsBadge }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Section Title</label>
                            <input type="text" id="pillars_title" value="{{ $pillarsTitle }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Section Subtitle</label>
                            <input type="text" id="pillars_subtitle" value="{{ $pillarsSubtitle }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-medium text-slate-700 text-sm">
                        </div>
                    </div>

                    {{-- Cards Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
                        @foreach([0, 1, 2] as $idx)
                            @php $card = $pillarCards[$idx] ?? ['title' => '', 'description' => '', 'icon' => 'bi-rocket-takeoff']; @endphp
                            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                                <span class="inline-block px-3 py-1 bg-white rounded-lg text-xs font-bold text-slate-600 border border-slate-200">Card #{{ $idx + 1 }}</span>
                                
                                <div class="space-y-1">
                                    <label class="block text-[11px] font-bold uppercase text-slate-500">Icon</label>
                                    <select id="card_icon_{{ $idx }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs font-bold text-slate-800">
                                        <option value="bi-rocket-takeoff" {{ ($card['icon'] ?? '') === 'bi-rocket-takeoff' ? 'selected' : '' }}>🚀 Rocket (Rapid Launch)</option>
                                        <option value="bi-people" {{ ($card['icon'] ?? '') === 'bi-people' ? 'selected' : '' }}>👥 People (Global Network)</option>
                                        <option value="bi-graph-up-arrow" {{ ($card['icon'] ?? '') === 'bi-graph-up-arrow' ? 'selected' : '' }}>📈 Graph (Scalable Growth)</option>
                                        <option value="bi-lightning-charge" {{ ($card['icon'] ?? '') === 'bi-lightning-charge' ? 'selected' : '' }}>⚡ Lightning</option>
                                        <option value="bi-shield-check" {{ ($card['icon'] ?? '') === 'bi-shield-check' ? 'selected' : '' }}>🛡️ Shield</option>
                                        <option value="bi-trophy" {{ ($card['icon'] ?? '') === 'bi-trophy' ? 'selected' : '' }}>🏆 Trophy</option>
                                    </select>
                                </div>

                                <div class="space-y-1">
                                    <label class="block text-[11px] font-bold uppercase text-slate-500">Card Title</label>
                                    <input type="text" id="card_title_{{ $idx }}" value="{{ $card['title'] ?? '' }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 font-bold text-xs text-slate-900">
                                </div>

                                <div class="space-y-1">
                                    <label class="block text-[11px] font-bold uppercase text-slate-500">Description</label>
                                    <textarea id="card_desc_{{ $idx }}" rows="3" class="w-full px-3 py-2 rounded-lg border border-slate-200 font-medium text-xs text-slate-700">{{ $card['description'] ?? '' }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 3. Impact Stats Section Form --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 space-y-6">
                    <div class="border-b border-slate-100 pb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 font-black flex items-center justify-center text-sm">3</span>
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Community Impact Statistics (4 Stats)</h2>
                            <p class="text-xs text-slate-400 font-medium">Update key achievement numbers and metric labels.</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Stats Section Title</label>
                        <input type="text" id="stats_title" value="{{ $statsTitle }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm">
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-4 border-t border-slate-100">
                        @foreach([0, 1, 2, 3] as $idx)
                            @php $stat = $statsItems[$idx] ?? ['number' => '', 'label' => '']; @endphp
                            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Stat #{{ $idx + 1 }}</span>
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase text-slate-500">Number / Value</label>
                                    <input type="text" id="stat_num_{{ $idx }}" value="{{ $stat['number'] ?? '' }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 font-black text-sm text-uco-orange-600">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase text-slate-500">Label Description</label>
                                    <input type="text" id="stat_label_{{ $idx }}" value="{{ $stat['label'] ?? '' }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 font-bold text-xs text-slate-800">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 4. CTA Section Form --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 space-y-6">
                    <div class="border-b border-slate-100 pb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-green-100 text-green-600 font-black flex items-center justify-center text-sm">4</span>
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Call-To-Action Banner</h2>
                            <p class="text-xs text-slate-400 font-medium">Bottom call-to-action banner heading and button text.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">CTA Heading</label>
                            <input type="text" id="cta_heading" value="{{ $ctaHeading }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">CTA Subtitle</label>
                            <input type="text" id="cta_subtitle" value="{{ $ctaSubtitle }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-medium text-slate-700 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Primary Button Label</label>
                            <input type="text" id="primary_btn_text" value="{{ $primaryBtnText }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Secondary Button Label</label>
                            <input type="text" id="secondary_btn_text" value="{{ $secondaryBtnText }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm">
                        </div>
                    </div>
                </div>
            @else
                {{-- Standard EditorJS for other pages --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                    <p class="text-sm font-bold text-slate-500 mb-6 uppercase tracking-wider">Page Content Editor</p>
                    <div id="editorjs" class="prose max-w-none min-h-[400px] p-6 border border-slate-100 rounded-xl bg-slate-50"></div>
                </div>
            @endif

            {{-- Save Action --}}
            <div class="flex justify-end pt-4">
                <button id="save-btn" class="px-10 py-4 bg-uco-orange-500 text-white font-black text-sm rounded-xl hover:bg-uco-orange-600 transition-all duration-200 shadow-lg shadow-uco-orange-500/25 hover:scale-[1.02]">
                    Save All Changes
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    @if($page->slug !== 'about')
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/image@latest"></script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pageSlug = @json($page->slug);
            let editor = null;

            if (pageSlug !== 'about') {
                try {
                    editor = new EditorJS({
                        holder: 'editorjs',
                        placeholder: 'Start writing your content here...',
                        tools: {
                            header: { class: window.Header, inlineToolbar: true, config: { levels: [2, 3, 4], defaultLevel: 2 } },
                            list: { class: window.NestedList || window.EditorjsList || window.List, inlineToolbar: true },
                            image: { class: window.ImageTool || window.SimpleImage, config: { endpoints: { byFile: '{{ route('pages.upload-image') }}' }, additionalRequestHeaders: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } } }
                        },
                        data: @json($content)
                    });
                } catch (e) {
                    console.error("EditorJS initialization failed:", e);
                }
            }

            document.getElementById('save-btn').addEventListener('click', () => {
                const btn = document.getElementById('save-btn');
                const originalText = btn.innerText;
                btn.innerText = 'Saving...';
                btn.disabled = true;

                let payloadPromise;

                if (pageSlug === 'about') {
                    const payload = {
                        hero: {
                            badge: document.getElementById('hero_badge').value,
                            title: document.getElementById('hero_title').value,
                            subtitle: document.getElementById('hero_subtitle').value,
                        },
                        pillars: {
                            badge: document.getElementById('pillars_badge').value,
                            title: document.getElementById('pillars_title').value,
                            subtitle: document.getElementById('pillars_subtitle').value,
                            cards: [
                                {
                                    title: document.getElementById('card_title_0').value,
                                    description: document.getElementById('card_desc_0').value,
                                    icon: document.getElementById('card_icon_0').value,
                                },
                                {
                                    title: document.getElementById('card_title_1').value,
                                    description: document.getElementById('card_desc_1').value,
                                    icon: document.getElementById('card_icon_1').value,
                                },
                                {
                                    title: document.getElementById('card_title_2').value,
                                    description: document.getElementById('card_desc_2').value,
                                    icon: document.getElementById('card_icon_2').value,
                                },
                            ]
                        },
                        stats: {
                            title: document.getElementById('stats_title').value,
                            items: [
                                { number: document.getElementById('stat_num_0').value, label: document.getElementById('stat_label_0').value },
                                { number: document.getElementById('stat_num_1').value, label: document.getElementById('stat_label_1').value },
                                { number: document.getElementById('stat_num_2').value, label: document.getElementById('stat_label_2').value },
                                { number: document.getElementById('stat_num_3').value, label: document.getElementById('stat_label_3').value },
                            ]
                        },
                        cta: {
                            heading: document.getElementById('cta_heading').value,
                            subtitle: document.getElementById('cta_subtitle').value,
                            primary_btn_text: document.getElementById('primary_btn_text').value,
                            secondary_btn_text: document.getElementById('secondary_btn_text').value,
                        }
                    };
                    payloadPromise = Promise.resolve(payload);
                } else {
                    payloadPromise = editor.save();
                }

                payloadPromise.then((outputData) => {
                    fetch('{{ route('pages.update', $page->slug) }}', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            title: '{{ $page->title }}',
                            content_json: outputData
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        btn.innerText = 'Saved Successfully!';
                        btn.classList.replace('bg-uco-orange-500', 'bg-slate-900');
                        setTimeout(() => {
                            btn.innerText = originalText;
                            btn.classList.replace('bg-slate-900', 'bg-uco-orange-500');
                            btn.disabled = false;
                        }, 2000);
                    })
                    .catch(err => {
                        console.error('Saving failed: ', err);
                        alert('Error saving data.');
                        btn.innerText = originalText;
                        btn.disabled = false;
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
