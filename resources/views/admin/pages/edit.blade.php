<x-app-layout>
    @section('title', 'Edit Page: ' . $page->title)

    @php
        $content = is_string($page->content_json) ? json_decode($page->content_json, true) : ($page->content_json ?? []);
        
        $initialSections = $content['sections'] ?? [];
        if (empty($initialSections)) {
            $initialSections = [
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

    <script id="initial-sections-data" type="application/json">@json($initialSections)</script>

    <div class="py-12 px-6 max-w-[1200px] mx-auto font-sans" x-data="initSectionBuilder(JSON.parse(document.getElementById('initial-sections-data').textContent))">
        <div class="flex items-center justify-between mb-8">
            <div>
                <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-uco-orange-600 mb-2">
                    Dynamic Section Builder (Non-Tech)
                </span>
                <h1 class="text-3xl font-black text-slate-900">Edit {{ $page->title }}</h1>
                <p class="text-xs text-slate-500 font-medium mt-1">Add, remove, reorder, or edit any section visually. No code required!</p>
            </div>
            <a href="{{ route($page->slug) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-sm transition-all">
                View Public Page <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </div>

        {{-- Sections Builder List --}}
        <div class="space-y-8">
            <template x-for="(sec, sIdx) in sections" :key="sIdx">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 space-y-6 relative transition-all duration-300 hover:border-slate-300">
                    {{-- Section Header Controls --}}
                    <div class="border-b border-slate-100 pb-4 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-slate-900 text-white font-black flex items-center justify-center text-xs" x-text="sIdx + 1"></span>
                            <div>
                                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide" x-text="getSectionTypeName(sec.type)"></h2>
                                <p class="text-xs text-slate-400 font-medium">Reorder, edit fields, or delete this section anytime.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="moveUp(sIdx)" :disabled="sIdx === 0" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs disabled:opacity-30 disabled:cursor-not-allowed">
                                <i class="bi bi-arrow-up"></i> Move Up
                            </button>
                            <button type="button" @click="moveDown(sIdx)" :disabled="sIdx === sections.length - 1" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs disabled:opacity-30 disabled:cursor-not-allowed">
                                <i class="bi bi-arrow-down"></i> Move Down
                            </button>
                            <button type="button" @click="removeSection(sIdx)" class="px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs transition-colors">
                                <i class="bi bi-trash3"></i> Remove
                            </button>
                        </div>
                    </div>

                    {{-- TYPE 1: HERO SECTION --}}
                    <template x-if="sec.type === 'hero'">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Top Tagline Badge</label>
                                    <input type="text" x-model="sec.badge" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Main Headline Title</label>
                                    <input type="text" x-model="sec.title" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Subtitle Description</label>
                                <textarea x-model="sec.subtitle" rows="2" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-medium text-slate-700 text-sm"></textarea>
                            </div>
                        </div>
                    </template>

                    {{-- TYPE 2: FEATURE CARDS GRID --}}
                    <template x-if="sec.type === 'feature_cards'">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Section Badge</label>
                                    <input type="text" x-model="sec.badge" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Section Title</label>
                                    <input type="text" x-model="sec.title" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Section Subtitle</label>
                                    <input type="text" x-model="sec.subtitle" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-medium text-slate-700 text-sm">
                                </div>
                            </div>

                            {{-- Cards Container --}}
                            <div class="space-y-4 pt-4 border-t border-slate-100">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Feature Cards (<span x-text="sec.cards ? sec.cards.length : 0"></span>)</span>
                                    <button type="button" @click="addCard(sIdx)" class="px-4 py-2 rounded-xl bg-uco-orange-50 text-uco-orange-600 border border-uco-orange-200 font-bold text-xs hover:bg-uco-orange-100 transition-colors">
                                        <i class="bi bi-plus-circle-fill"></i> Add Card
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <template x-for="(card, cIdx) in sec.cards" :key="cIdx">
                                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4 relative">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[11px] font-bold text-slate-500" x-text="'Card #' + (cIdx + 1)"></span>
                                                <button type="button" @click="removeCard(sIdx, cIdx)" class="text-red-500 hover:text-red-700 text-xs font-bold">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>

                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-bold uppercase text-slate-500">Icon</label>
                                                <select x-model="card.icon" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs font-bold text-slate-800">
                                                    <option value="bi-rocket-takeoff">🚀 Rocket (Rapid Launch)</option>
                                                    <option value="bi-people">👥 People (Global Network)</option>
                                                    <option value="bi-graph-up-arrow">📈 Graph (Scalable Growth)</option>
                                                    <option value="bi-lightning-charge">⚡ Lightning</option>
                                                    <option value="bi-shield-check">🛡️ Shield</option>
                                                    <option value="bi-trophy">🏆 Trophy</option>
                                                    <option value="bi-star">⭐ Star</option>
                                                    <option value="bi-lightbulb">💡 Lightbulb</option>
                                                </select>
                                            </div>

                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-bold uppercase text-slate-500">Title</label>
                                                <input type="text" x-model="card.title" class="w-full px-3 py-2 rounded-lg border border-slate-200 font-bold text-xs text-slate-900">
                                            </div>

                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-bold uppercase text-slate-500">Description</label>
                                                <textarea x-model="card.description" rows="3" class="w-full px-3 py-2 rounded-lg border border-slate-200 font-medium text-xs text-slate-700"></textarea>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- TYPE 3: STATS GRID --}}
                    <template x-if="sec.type === 'stats_grid'">
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Stats Section Title</label>
                                <input type="text" x-model="sec.title" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm">
                            </div>

                            <div class="space-y-4 pt-4 border-t border-slate-100">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Stat Metric Cards (<span x-text="sec.items ? sec.items.length : 0"></span>)</span>
                                    <button type="button" @click="addStat(sIdx)" class="px-4 py-2 rounded-xl bg-purple-50 text-purple-600 border border-purple-200 font-bold text-xs hover:bg-purple-100 transition-colors">
                                        <i class="bi bi-plus-circle-fill"></i> Add Stat
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                    <template x-for="(st, stIdx) in sec.items" :key="stIdx">
                                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 relative">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-bold text-slate-400" x-text="'Stat #' + (stIdx + 1)"></span>
                                                <button type="button" @click="removeStat(sIdx, stIdx)" class="text-red-500 hover:text-red-700 text-xs font-bold">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-bold uppercase text-slate-500">Value / Number</label>
                                                <input type="text" x-model="st.number" class="w-full px-3 py-2 rounded-lg border border-slate-200 font-black text-sm text-uco-orange-600">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-bold uppercase text-slate-500">Label</label>
                                                <input type="text" x-model="st.label" class="w-full px-3 py-2 rounded-lg border border-slate-200 font-bold text-xs text-slate-800">
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- TYPE 4: TEXT / FAQ BLOCK --}}
                    <template x-if="sec.type === 'text_block'">
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Block Heading</label>
                                <input type="text" x-model="sec.heading" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Paragraph Content</label>
                                <textarea x-model="sec.content" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-medium text-slate-700 text-sm"></textarea>
                            </div>
                        </div>
                    </template>

                    {{-- TYPE 5: CTA BANNER --}}
                    <template x-if="sec.type === 'cta_banner'">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">CTA Heading</label>
                                    <input type="text" x-model="sec.heading" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">CTA Subtitle</label>
                                    <input type="text" x-model="sec.subtitle" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-medium text-slate-700 text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Primary Button Label</label>
                                    <input type="text" x-model="sec.primary_btn_text" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Secondary Button Label</label>
                                    <input type="text" x-model="sec.secondary_btn_text" class="w-full px-4 py-3 rounded-xl border border-slate-200 font-bold text-slate-900 text-sm">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        {{-- Add Section Options Bar --}}
        <div class="mt-8 bg-slate-900 rounded-2xl p-6 text-white flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <i class="bi bi-plus-circle text-uco-orange-400 text-2xl"></i>
                <div>
                    <h3 class="text-sm font-bold text-white">Add New Section</h3>
                    <p class="text-xs text-slate-400">Choose a section type to insert into your page layout.</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <button type="button" @click="addSection('hero')" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition-all">
                    + Hero Header
                </button>
                <button type="button" @click="addSection('feature_cards')" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition-all">
                    + Feature Cards Grid
                </button>
                <button type="button" @click="addSection('stats_grid')" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition-all">
                    + Impact Stats Grid
                </button>
                <button type="button" @click="addSection('text_block')" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition-all">
                    + Text / FAQ Block
                </button>
                <button type="button" @click="addSection('cta_banner')" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition-all">
                    + CTA Banner
                </button>
            </div>
        </div>

        {{-- Save Bar --}}
        <div class="mt-8 flex justify-end">
            <button type="button" @click="saveSections('{{ route('pages.update', $page->slug) }}')" class="px-10 py-4 bg-uco-orange-500 text-white font-black text-sm rounded-xl hover:bg-uco-orange-600 transition-all shadow-lg shadow-uco-orange-500/25 hover:scale-[1.02]">
                Save All Changes Live
            </button>
        </div>
    </div>
</x-app-layout>
