<x-app-layout>
    @section('title', 'Edit Page: ' . $page->title)

    <script id="initial-sections-data" type="application/json">@json($initialSections)</script>

    <div class="cms-builder-wrapper mx-auto max-w-[1600px] px-4 py-8 sm:px-6 lg:px-8"
         data-save-url="{{ route('pages.update', $page->slug, false) }}"
         x-data="sectionBuilder(JSON.parse(document.getElementById('initial-sections-data').textContent))">

        {{-- --- Page Header --- --}}
        <x-ui.page-header :title="'Edit ' . $page->title" subtitle="Add, remove, reorder, or edit any section visually. No code required!">
            <x-slot:actions>
                <a href="{{ route($page->slug) }}" target="_blank" class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 transition-all hover:bg-gray-200">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>View Public Page</span>
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- --- Sections Builder List --- --}}
        <div class="space-y-6">
            <template x-for="(sec, sIdx) in sections" :key="sIdx">
                <div class="relative space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all duration-300 hover:border-gray-300 md:p-8">
                    {{-- --- Section Header Controls --- --}}
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-900 text-xs font-bold text-white" x-text="sIdx + 1"></span>
                            <div>
                                <h2 class="text-base font-bold text-gray-900" x-text="getSectionTypeName(sec.type)"></h2>
                                <p class="text-xs font-medium text-gray-500">Reorder, edit fields, or delete this section anytime.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="moveUp(sIdx)" :disabled="sIdx === 0" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-700 transition-all hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-30">
                                <i class="bi bi-arrow-up"></i> Move Up
                            </button>
                            <button type="button" @click="moveDown(sIdx)" :disabled="sIdx === sections.length - 1" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-700 transition-all hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-30">
                                <i class="bi bi-arrow-down"></i> Move Down
                            </button>
                            <button type="button" @click="removeSection(sIdx)" class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition-colors hover:bg-red-100">
                                <i class="bi bi-trash3"></i> Remove
                            </button>
                        </div>
                    </div>

                    {{-- --- Hero --- --}}
                    <template x-if="sec.type === 'hero'">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <x-cms.field label="Top Tagline Badge" model="sec.badge" />
                                <x-cms.field label="Main Headline Title" model="sec.title" />
                            </div>
                            <x-cms.field label="Subtitle Description" model="sec.subtitle" look="body" :rows="2" />
                        </div>
                    </template>

                    {{-- --- Feature cards --- --}}
                    <template x-if="sec.type === 'feature_cards'">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                                <x-cms.field label="Section Badge" model="sec.badge" />
                                <x-cms.field label="Section Title" model="sec.title" />
                                <x-cms.field label="Section Subtitle" model="sec.subtitle" look="body" />
                            </div>

                            <div class="space-y-4 border-t border-gray-100 pt-4">
                                <x-cms.list-heading label="Feature Cards" count="sec.cards ? sec.cards.length : 0" add="addCard(sIdx)" button="Add Card" />

                                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                                    <template x-for="(card, cIdx) in sec.cards" :key="cIdx">
                                        <div class="relative space-y-4 rounded-xl border border-gray-200 bg-gray-50 p-5">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-gray-500" x-text="'Card #' + (cIdx + 1)"></span>
                                                <button type="button" @click="removeCard(sIdx, cIdx)" class="text-xs font-bold text-red-500 transition-colors hover:text-red-700">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>

                                            <x-cms.field label="Icon" model="card.icon" look="item-label" :options="$featureIcons" />
                                            <x-cms.field label="Title" model="card.title" look="item-heading" />
                                            <x-cms.field label="Description" model="card.description" look="item-body" :rows="3" />
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- --- Stats grid --- --}}
                    <template x-if="sec.type === 'stats_grid'">
                        <div class="space-y-6">
                            <x-cms.field label="Stats Section Title" model="sec.title" />

                            <div class="space-y-4 border-t border-gray-100 pt-4">
                                <x-cms.list-heading label="Stat Metric Cards" count="sec.items ? sec.items.length : 0" add="addStat(sIdx)" button="Add Stat" />

                                <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                                    <template x-for="(st, stIdx) in sec.items" :key="stIdx">
                                        <div class="relative space-y-3 rounded-xl border border-gray-200 bg-gray-50 p-5">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-gray-400" x-text="'Stat #' + (stIdx + 1)"></span>
                                                <button type="button" @click="removeStat(sIdx, stIdx)" class="text-xs font-bold text-red-500 transition-colors hover:text-red-700">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            <x-cms.field label="Value / Number" model="st.number" look="item-number" />
                                            <x-cms.field label="Label" model="st.label" look="item-label" />
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- --- Text / FAQ block --- --}}
                    <template x-if="sec.type === 'text_block'">
                        <div class="space-y-4">
                            <x-cms.field label="Block Heading" model="sec.heading" />
                            <x-cms.field label="Paragraph Content" model="sec.content" look="body" :rows="4" />
                        </div>
                    </template>

                    {{-- --- CTA banner --- --}}
                    <template x-if="sec.type === 'cta_banner'">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <x-cms.field label="CTA Heading" model="sec.heading" />
                                <x-cms.field label="CTA Subtitle" model="sec.subtitle" look="body" />
                            </div>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <x-cms.field label="Primary Button Label" model="sec.primary_btn_text" />
                                <x-cms.field label="Secondary Button Label" model="sec.secondary_btn_text" />
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        {{-- --- Add Section Options Bar --- --}}
        <div class="mt-8 flex flex-wrap items-center justify-between gap-4 rounded-xl bg-gray-900 p-6 text-white shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-uco-orange-500/20 text-xl text-uco-orange-400">
                    <i class="bi bi-plus-circle"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Add New Section</h3>
                    <p class="text-xs text-gray-400">Choose a section type to insert into your page layout.</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                @foreach ($sectionTypes as $type => $section)
                    <button type="button" @click="addSection(@js($type))" class="inline-flex items-center gap-1.5 rounded-lg bg-white/10 px-3.5 py-2 text-xs font-semibold text-white transition-all hover:bg-white/20">
                        <i class="bi {{ $section['icon'] }}"></i> {{ $section['label'] }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- --- Save Bar --- --}}
        <div class="mt-8 flex justify-end">
            <button type="button" @click="saveSections()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-uco-orange-600 px-8 py-3.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-uco-orange-700 active:scale-95">
                <i class="bi bi-check2-circle"></i>
                <span>Save All Changes Live</span>
            </button>
        </div>
    </div>
</x-app-layout>
