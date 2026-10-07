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

    <div class="cms-builder-wrapper max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8"
        x-data="initSectionBuilder(JSON.parse(document.getElementById('initial-sections-data').textContent))">

        {{-- Page Header --}}
        <section class="relative overflow-hidden rounded-xl border border-gray-200 bg-white px-6 py-6 shadow-sm md:px-8 mb-8 reveal-on-scroll">
            <div class="relative z-10 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-1">
                    <span class="inline-flex items-center rounded-md border border-uco-orange-200 bg-uco-orange-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-uco-orange-700">
                        Dynamic Section Builder (CMS)
                    </span>
                    <h1 class="text-3xl font-extrabold text-soft-gray-900 md:text-4xl">Edit {{ $page->title }}</h1>
                    <p class="text-sm text-soft-gray-600 mt-1">Add, remove, reorder, or edit any section visually. No code required!</p>
                </div>

                <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full lg:w-auto">
                    <a href="{{ route($page->slug) }}" target="_blank" class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200 transition-all">
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>View Public Page</span>
                    </a>
                </div>
            </div>
        </section>

        {{-- Sections Builder List --}}
        <div class="space-y-6">
            <template x-for="(sec, sIdx) in sections" :key="sIdx">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8 space-y-6 relative transition-all duration-300 hover:border-gray-300">
                    {{-- Section Header Controls --}}
                    <div class="border-b border-gray-100 pb-4 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-gray-900 text-white font-bold flex items-center justify-center text-xs" x-text="sIdx + 1"></span>
                            <div>
                                <h2 class="text-base font-bold text-gray-900 uppercase tracking-wide" x-text="getSectionTypeName(sec.type)"></h2>
                                <p class="text-xs text-gray-500 font-medium">Reorder, edit fields, or delete this section anytime.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="moveUp(sIdx)" :disabled="sIdx === 0" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold text-xs disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                                <i class="bi bi-arrow-up"></i> Move Up
                            </button>
                            <button type="button" @click="moveDown(sIdx)" :disabled="sIdx === sections.length - 1" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold text-xs disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                                <i class="bi bi-arrow-down"></i> Move Down
                            </button>
                            <button type="button" @click="removeSection(sIdx)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 text-red-600 font-semibold text-xs transition-colors">
                                <i class="bi bi-trash3"></i> Remove
                            </button>
                        </div>
                    </div>

                    {{-- TYPE 1: HERO SECTION --}}
                    <template x-if="sec.type === 'hero'">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Top Tagline Badge</label>
                                    <input type="text" x-model="sec.badge" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 font-semibold text-gray-900 text-sm focus:border-uco-orange-500 focus:ring-uco-orange-500">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Main Headline Title</label>
                                    <input type="text" x-model="sec.title" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 font-semibold text-gray-900 text-sm focus:border-uco-orange-500 focus:ring-uco-orange-500">
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Subtitle Description</label>
                                <textarea x-model="sec.subtitle" rows="2" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 font-medium text-gray-700 text-sm focus:border-uco-orange-500 focus:ring-uco-orange-500"></textarea>
                            </div>
                        </div>
                    </template>

                    {{-- TYPE 2: FEATURE CARDS GRID --}}
                    <template x-if="sec.type === 'feature_cards'">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Section Badge</label>
                                    <input type="text" x-model="sec.badge" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 font-semibold text-gray-900 text-sm focus:border-uco-orange-500 focus:ring-uco-orange-500">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Section Title</label>
                                    <input type="text" x-model="sec.title" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 font-semibold text-gray-900 text-sm focus:border-uco-orange-500 focus:ring-uco-orange-500">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Section Subtitle</label>
                                    <input type="text" x-model="sec.subtitle" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 font-medium text-gray-700 text-sm focus:border-uco-orange-500 focus:ring-uco-orange-500">
                                </div>
                            </div>

                            {{-- Cards Container --}}
                            <div class="space-y-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Feature Cards (<span x-text="sec.cards ? sec.cards.length : 0"></span>)</span>
                                    <button type="button" @click="addCard(sIdx)" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-uco-orange-50 text-uco-orange-700 border border-uco-orange-200 font-bold text-xs hover:bg-uco-orange-100 transition-colors">
                                        <i class="bi bi-plus-circle"></i> Add Card
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <template x-for="(card, cIdx) in sec.cards" :key="cIdx">
                                        <div class="p-5 rounded-xl bg-gray-50 border border-gray-200 space-y-4 relative">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-gray-500" x-text="'Card #' + (cIdx + 1)"></span>
                                                <button type="button" @click="removeCard(sIdx, cIdx)" class="text-red-500 hover:text-red-700 text-xs font-bold transition-colors">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>

                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500">Icon</label>
                                                <select x-model="card.icon" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs font-semibold text-gray-800 focus:border-uco-orange-500 focus:ring-uco-orange-500">
                                                    <option value="bi-rocket-takeoff">Rocket (Rapid Launch)</option>
                                                    <option value="bi-people">People (Global Network)</option>
                                                    <option value="bi-graph-up-arrow">Graph (Scalable Growth)</option>
                                                    <option value="bi-lightning-charge">Lightning</option>
                                                    <option value="bi-shield-check">Shield</option>
                                                    <option value="bi-trophy">Trophy</option>
                                                    <option value="bi-star">Star</option>
                                                    <option value="bi-lightbulb">Lightbulb</option>
                                                </select>
                                            </div>

                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500">Title</label>
                                                <input type="text" x-model="card.title" class="w-full px-3 py-2 rounded-lg border border-gray-200 font-semibold text-xs text-gray-900 focus:border-uco-orange-500 focus:ring-uco-orange-500">
                                            </div>

                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500">Description</label>
                                                <textarea x-model="card.description" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-200 font-medium text-xs text-gray-700 focus:border-uco-orange-500 focus:ring-uco-orange-500"></textarea>
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
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Stats Section Title</label>
                                <input type="text" x-model="sec.title" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 font-semibold text-gray-900 text-sm focus:border-uco-orange-500 focus:ring-uco-orange-500">
                            </div>

                            <div class="space-y-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Stat Metric Cards (<span x-text="sec.items ? sec.items.length : 0"></span>)</span>
                                    <button type="button" @click="addStat(sIdx)" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-uco-orange-50 text-uco-orange-700 border border-uco-orange-200 font-bold text-xs hover:bg-uco-orange-100 transition-colors">
                                        <i class="bi bi-plus-circle"></i> Add Stat
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                    <template x-for="(st, stIdx) in sec.items" :key="stIdx">
                                        <div class="p-5 rounded-xl bg-gray-50 border border-gray-200 space-y-3 relative">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-gray-400" x-text="'Stat #' + (stIdx + 1)"></span>
                                                <button type="button" @click="removeStat(sIdx, stIdx)" class="text-red-500 hover:text-red-700 text-xs font-bold transition-colors">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500">Value / Number</label>
                                                <input type="text" x-model="st.number" class="w-full px-3 py-2 rounded-lg border border-gray-200 font-extrabold text-sm text-uco-orange-600 focus:border-uco-orange-500 focus:ring-uco-orange-500">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500">Label</label>
                                                <input type="text" x-model="st.label" class="w-full px-3 py-2 rounded-lg border border-gray-200 font-semibold text-xs text-gray-800 focus:border-uco-orange-500 focus:ring-uco-orange-500">
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
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Block Heading</label>
                                <input type="text" x-model="sec.heading" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 font-semibold text-gray-900 text-sm focus:border-uco-orange-500 focus:ring-uco-orange-500">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Paragraph Content</label>
                                <textarea x-model="sec.content" rows="4" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 font-medium text-gray-700 text-sm focus:border-uco-orange-500 focus:ring-uco-orange-500"></textarea>
                            </div>
                        </div>
                    </template>

                    {{-- TYPE 5: CTA BANNER --}}
                    <template x-if="sec.type === 'cta_banner'">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">CTA Heading</label>
                                    <input type="text" x-model="sec.heading" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 font-semibold text-gray-900 text-sm focus:border-uco-orange-500 focus:ring-uco-orange-500">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">CTA Subtitle</label>
                                    <input type="text" x-model="sec.subtitle" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 font-medium text-gray-700 text-sm focus:border-uco-orange-500 focus:ring-uco-orange-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Primary Button Label</label>
                                    <input type="text" x-model="sec.primary_btn_text" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 font-semibold text-gray-900 text-sm focus:border-uco-orange-500 focus:ring-uco-orange-500">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Secondary Button Label</label>
                                    <input type="text" x-model="sec.secondary_btn_text" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 font-semibold text-gray-900 text-sm focus:border-uco-orange-500 focus:ring-uco-orange-500">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        {{-- Add Section Options Bar --}}
        <div class="mt-8 bg-gray-900 rounded-xl p-6 text-white shadow-sm flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-uco-orange-500/20 text-uco-orange-400 flex items-center justify-center text-xl">
                    <i class="bi bi-plus-circle"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Add New Section</h3>
                    <p class="text-xs text-gray-400">Choose a section type to insert into your page layout.</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <button type="button" @click="addSection('hero')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white font-semibold text-xs transition-all">
                    <i class="bi bi-layout-text-window-reverse"></i> Hero Header
                </button>
                <button type="button" @click="addSection('feature_cards')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white font-semibold text-xs transition-all">
                    <i class="bi bi-grid-3x3-gap"></i> Feature Cards Grid
                </button>
                <button type="button" @click="addSection('stats_grid')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white font-semibold text-xs transition-all">
                    <i class="bi bi-bar-chart-line"></i> Impact Stats Grid
                </button>
                <button type="button" @click="addSection('text_block')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white font-semibold text-xs transition-all">
                    <i class="bi bi-chat-square-text"></i> Text / FAQ Block
                </button>
                <button type="button" @click="addSection('cta_banner')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white font-semibold text-xs transition-all">
                    <i class="bi bi-megaphone"></i> CTA Banner
                </button>
            </div>
        </div>

        {{-- Save Bar --}}
        <div class="mt-8 flex justify-end">
            <button type="button" @click="saveSections('{{ route('pages.update', $page->slug) }}')" class="inline-flex items-center justify-center gap-2 rounded-lg bg-uco-orange-600 px-8 py-3.5 text-sm font-bold text-white shadow-sm hover:bg-uco-orange-700 active:scale-95 transition-all">
                <i class="bi bi-check2-circle"></i>
                <span>Save All Changes Live</span>
            </button>
        </div>
    </div>
</x-app-layout>
