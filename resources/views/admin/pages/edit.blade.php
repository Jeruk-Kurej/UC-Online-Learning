<x-app-layout>
    @section('title', 'Edit ' . $page->title)

    <script id="initial-sections-data" type="application/json">@json($initialSections)</script>

    <div class="mx-auto max-w-4xl" data-save-url="{{ route('pages.update', $page->slug, false) }}" x-data="sectionBuilder(JSON.parse(document.getElementById('initial-sections-data').textContent))">
        <x-ui.page-header :title="'Edit ' . $page->title" subtitle="Add, remove, reorder and edit the sections of the page. Changes go live when you save.">
            <x-slot:actions>
                <a href="{{ route($page->slug) }}" target="_blank" rel="noopener" class="btn btn-secondary">
                    <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                    View the page
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="space-y-6">
            <template x-for="(sec, sIdx) in sections" :key="sIdx">
                <section class="card space-y-5">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line pb-4">
                        <h2 class="card-title"><span x-text="sIdx + 1"></span>. <span x-text="getSectionTypeName(sec.type)"></span></h2>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="moveUp(sIdx)" :disabled="sIdx === 0" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-up" aria-hidden="true"></i> Move up</button>
                            <button type="button" @click="moveDown(sIdx)" :disabled="sIdx === sections.length - 1" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-down" aria-hidden="true"></i> Move down</button>
                            <button type="button" @click="removeSection(sIdx)" class="btn btn-danger-outline btn-sm">Remove</button>
                        </div>
                    </div>

                    {{-- --- Page title --- --}}
                    <template x-if="sec.type === 'hero'">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <x-cms.field label="Small text above the title" model="sec.badge" />
                            <x-cms.field label="Title" model="sec.title" />
                            <div class="md:col-span-2"><x-cms.field label="Text under the title" model="sec.subtitle" :rows="2" /></div>
                        </div>
                    </template>

                    {{-- --- Cards --- --}}
                    <template x-if="sec.type === 'feature_cards'">
                        <div class="space-y-5">
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                                <x-cms.field label="Small text above the title" model="sec.badge" />
                                <x-cms.field label="Title" model="sec.title" />
                                <x-cms.field label="Text under the title" model="sec.subtitle" />
                            </div>

                            <div class="space-y-4 border-t border-line pt-5">
                                <x-cms.list-heading label="Cards" count="sec.cards ? sec.cards.length : 0" add="addCard(sIdx)" button="Add a card" />

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <template x-for="(card, cIdx) in sec.cards" :key="cIdx">
                                        <div class="space-y-4 rounded-md border border-line bg-gray-50 p-4">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-semibold text-gray-800" x-text="'Card ' + (cIdx + 1)"></p>
                                                <button type="button" @click="removeCard(sIdx, cIdx)" class="btn btn-danger-outline btn-sm">Remove</button>
                                            </div>
                                            <x-cms.field label="Icon" model="card.icon" :options="$featureIcons" />
                                            <x-cms.field label="Title" model="card.title" />
                                            <x-cms.field label="Description" model="card.description" :rows="3" />
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- --- Numbers --- --}}
                    <template x-if="sec.type === 'stats_grid'">
                        <div class="space-y-5">
                            <x-cms.field label="Title" model="sec.title" />

                            <div class="space-y-4 border-t border-line pt-5">
                                <x-cms.list-heading label="Numbers" count="sec.items ? sec.items.length : 0" add="addStat(sIdx)" button="Add a number" />

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4">
                                    <template x-for="(st, stIdx) in sec.items" :key="stIdx">
                                        <div class="space-y-4 rounded-md border border-line bg-gray-50 p-4">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-semibold text-gray-800" x-text="'Number ' + (stIdx + 1)"></p>
                                                <button type="button" @click="removeStat(sIdx, stIdx)" class="btn btn-danger-outline btn-sm">Remove</button>
                                            </div>
                                            <x-cms.field label="Number" model="st.number" />
                                            <x-cms.field label="What it counts" model="st.label" />
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- --- Text --- --}}
                    <template x-if="sec.type === 'text_block'">
                        <div class="space-y-5">
                            <x-cms.field label="Heading" model="sec.heading" />
                            <x-cms.field label="Text" model="sec.content" :rows="5" />
                        </div>
                    </template>

                    {{-- --- Call to action --- --}}
                    <template x-if="sec.type === 'cta_banner'">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <x-cms.field label="Heading" model="sec.heading" />
                            <x-cms.field label="Text" model="sec.subtitle" />
                            <x-cms.field label="Main button text (leads to the login page)" model="sec.primary_btn_text" />
                            <x-cms.field label="Second button text (leads to the directory)" model="sec.secondary_btn_text" />
                        </div>
                    </template>
                </section>
            </template>
        </div>

        {{-- --- Add a section --- --}}
        <section class="card mt-6 space-y-3">
            <h2 class="card-title">Add a section</h2>
            <div class="flex flex-wrap gap-2">
                @foreach ($sectionTypes as $type => $section)
                    <button type="button" @click="addSection(@js($type))" class="btn btn-secondary">
                        <i class="bi {{ $section['icon'] }}" aria-hidden="true"></i>
                        {{ $section['label'] }}
                    </button>
                @endforeach
            </div>
        </section>

        <x-forms.actions class="mt-6">
            <button type="button" @click="saveSections()" class="btn btn-primary">Save and publish</button>
        </x-forms.actions>
    </div>
</x-app-layout>
