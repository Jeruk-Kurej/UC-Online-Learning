{{-- --- Product / service detail dialog, filled by openDetailModal() (businessShowPage) --- --}}
<div x-show="detailModalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4 overflow-y-auto"
     role="dialog" aria-modal="true">
    {{-- Background Overlay --}}
    <div x-show="detailModalOpen" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="detailModalOpen = false"
        class="fixed inset-0 transition-opacity bg-black/80 backdrop-blur-sm"></div>

    {{-- Modal Content Card --}}
    <div x-show="detailModalOpen" x-transition:enter="ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="relative bg-white rounded-2xl shadow-2xl overflow-hidden max-w-4xl w-full max-h-[90vh] flex flex-col md:flex-row border border-gray-100 transition-all pointer-events-auto z-[10000]">

        {{-- Close Button --}}
        <button type="button" @click="detailModalOpen = false"
            class="absolute top-4 right-4 z-50 bg-white/80 hover:bg-white text-gray-500 hover:text-gray-800 p-2 rounded-full shadow-md transition border border-gray-200/50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        {{-- Left Column: Square Picture Container --}}
        <div class="w-full md:w-1/2 flex flex-col bg-gray-50 border-b md:border-b-0 md:border-r border-gray-100 relative aspect-square md:max-h-[90vh]">
            {{-- Image Container --}}
            <div class="flex-grow flex items-center justify-center p-6 relative overflow-hidden h-[calc(100%-3rem)] min-h-[300px]">
                <img x-show="detailItem.photo_url" :src="detailItem.photo_url" :alt="detailItem.name"
                     class="max-w-full max-h-full object-contain rounded-md shadow-sm">
                <div x-show="!detailItem.photo_url" class="flex flex-col items-center justify-center text-gray-300">
                    <i class="bi bi-image text-7xl mb-2"></i>
                    <span class="text-xs text-gray-400">No image available</span>
                </div>
            </div>
            {{-- Caption Container on Bottom --}}
            <div x-show="detailItem.photo_caption" 
                 class="bg-gray-900/90 backdrop-blur-xs text-white p-3 text-xs text-center font-medium border-t border-gray-800 w-full min-h-[3rem] flex items-center justify-center">
                <p x-text="detailItem.photo_caption"></p>
            </div>
        </div>

        {{-- Right Column: Information --}}
        <div class="w-full md:w-1/2 flex flex-col p-6 md:p-8 max-h-[90vh]">
            {{-- Scrollable Container --}}
            <div class="flex-grow overflow-y-auto pr-1">
                {{-- Tag (Product / Service) --}}
                <div class="mb-3">
                    <span x-show="detailItem.type === 'product'" class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 uppercase tracking-wider">
                        <i class="bi bi-box-seam"></i> Product
                    </span>
                    <span x-show="detailItem.type === 'service'" class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-700 uppercase tracking-wider">
                        <i class="bi bi-gear"></i> Service
                    </span>
                </div>

                {{-- Item Name --}}
                <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight leading-tight mb-4" x-text="detailItem.name"></h3>

                {{-- Price and Price Type --}}
                <div class="mb-6 bg-orange-50/50 rounded-xl p-4 border border-orange-100/50">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Pricing</p>
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
                            <span class="inline-flex items-center text-[10px] font-bold text-orange-700 bg-orange-100 px-2 py-0.5 rounded uppercase tracking-wider" x-text="detailItem.price_type_label"></span>
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                <div class="mb-6">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Description</p>
                    <div class="text-sm text-gray-600 leading-relaxed whitespace-pre-line" x-text="detailItem.description"></div>
                </div>
            </div>

            {{-- Actions / Contact (Sticky Bottom) --}}
            <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row gap-3 bg-white mt-auto">
                <template x-if="detailItem.whatsapp_link">
                    <a :href="detailItem.whatsapp_link" target="_blank"
                       class="flex-1 flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-green-500 hover:bg-green-600 text-white font-bold text-sm shadow-lg hover:shadow-green-500/20 transition-all duration-300">
                        <i class="bi bi-whatsapp"></i> Inquire via WhatsApp
                    </a>
                </template>
                <button type="button" @click="detailModalOpen = false"
                        class="flex-1 px-5 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-sm transition text-center">
                    Close Details
                </button>
            </div>
        </div>
    </div>
</div>
