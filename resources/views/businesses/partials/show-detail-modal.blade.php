{{-- --- Product / service detail dialog, filled by openDetailModal() (businessShowPage) --- --}}
<div x-show="detailModalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto p-4"
     role="dialog" aria-modal="true">
    {{-- --- Background Overlay --- --}}
    <div x-show="detailModalOpen" x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0" @click="detailModalOpen = false"
         class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity"></div>

    {{-- --- Modal Content Card --- --}}
    <div x-show="detailModalOpen" x-transition:enter="ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="pointer-events-auto relative z-[10000] flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-2xl transition-all md:flex-row">

        {{-- --- Close Button --- --}}
        <button type="button" @click="detailModalOpen = false"
                class="absolute right-4 top-4 z-50 rounded-full border border-gray-200/50 bg-white/80 p-2 text-gray-500 shadow-md transition hover:bg-white hover:text-gray-800">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        {{-- --- Left Column: Square Picture Container --- --}}
        <div class="relative flex aspect-square w-full flex-col border-b border-gray-100 bg-gray-50 md:max-h-[90vh] md:w-1/2 md:border-b-0 md:border-r">
            {{-- --- Image Container --- --}}
            <div class="relative flex h-[calc(100%-3rem)] min-h-[300px] flex-grow items-center justify-center overflow-hidden p-6">
                <img x-show="detailItem.photo_url" :src="detailItem.photo_url" :alt="detailItem.name"
                     class="max-h-full max-w-full rounded-md object-contain shadow-sm">
                <div x-show="!detailItem.photo_url" class="flex flex-col items-center justify-center text-gray-300">
                    <i class="bi bi-image mb-2 text-7xl"></i>
                    <span class="text-xs text-gray-400">No image available</span>
                </div>
            </div>
            {{-- --- Caption Container on Bottom --- --}}
            <div x-show="detailItem.photo_caption"
                 class="backdrop-blur-xs flex min-h-[3rem] w-full items-center justify-center border-t border-gray-800 bg-gray-900/90 p-3 text-center text-xs font-medium text-white">
                <p x-text="detailItem.photo_caption"></p>
            </div>
        </div>

        {{-- --- Right Column: Information --- --}}
        <div class="flex max-h-[90vh] w-full flex-col p-6 md:w-1/2 md:p-8">
            {{-- --- Scrollable Container --- --}}
            <div class="flex-grow overflow-y-auto pr-1">
                {{-- --- Tag (Product / Service) --- --}}
                <div class="mb-3">
                    <span x-show="detailItem.type === 'product'" class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-blue-700">
                        <i class="bi bi-box-seam"></i> Product
                    </span>
                    <span x-show="detailItem.type === 'service'" class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-green-700">
                        <i class="bi bi-gear"></i> Service
                    </span>
                </div>

                {{-- --- Item Name --- --}}
                <h3 class="mb-4 text-2xl font-extrabold leading-tight tracking-tight text-gray-900" x-text="detailItem.name"></h3>

                {{-- --- Price and Price Type --- --}}
                <div class="mb-6 rounded-xl border border-orange-100/50 bg-orange-50/50 p-4">
                    <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">Pricing</p>
                    <div class="flex flex-col gap-1.5">
                        <div class="text-2xl font-extrabold text-[#f7931e]">
                            <template x-if="detailItem.formatted_price">
                                <span>Rp <span x-text="detailItem.formatted_price"></span></span>
                            </template>
                            <template x-if="!detailItem.formatted_price">
                                <span x-text="detailItem.price_text"></span>
                            </template>
                        </div>
                        <div x-show="detailItem.price_type_label" class="inline-flex">
                            <span class="inline-flex items-center rounded bg-orange-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-orange-700" x-text="detailItem.price_type_label"></span>
                        </div>
                    </div>
                </div>

                {{-- --- Description --- --}}
                <div class="mb-6">
                    <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">Description</p>
                    <div class="whitespace-pre-line text-sm leading-relaxed text-gray-600" x-text="detailItem.description"></div>
                </div>
            </div>

            {{-- --- Actions / Contact (Sticky Bottom) --- --}}
            <div class="mt-auto flex flex-col gap-3 border-t border-gray-100 bg-white pt-6 sm:flex-row">
                <template x-if="detailItem.whatsapp_link">
                    <a :href="detailItem.whatsapp_link" target="_blank"
                       class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-green-500 px-5 py-3 text-sm font-bold text-white shadow-lg transition-all duration-300 hover:bg-green-600 hover:shadow-green-500/20">
                        <i class="bi bi-whatsapp"></i> Inquire via WhatsApp
                    </a>
                </template>
                <button type="button" @click="detailModalOpen = false"
                        class="flex-1 rounded-xl bg-gray-100 px-5 py-3 text-center text-sm font-bold text-gray-700 transition hover:bg-gray-200">
                    Close Details
                </button>
            </div>
        </div>
    </div>
</div>
