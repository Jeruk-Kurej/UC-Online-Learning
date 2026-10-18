{{-- --- Product / service detail dialog, filled by openDetailModal() (businessShowPage) --- --}}
<div x-show="detailModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4" role="dialog" aria-modal="true" aria-labelledby="detail-title" @keydown.escape.window="detailModalOpen = false">
    <div class="fixed inset-0 bg-black/60" @click="detailModalOpen = false"></div>

    <div class="relative flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-lg border border-line bg-white shadow-xl md:flex-row">
        {{-- --- Picture --- --}}
        <div class="flex min-h-[16rem] items-center justify-center border-b border-line bg-gray-50 p-4 md:w-1/2 md:border-b-0 md:border-r">
            <div class="flex flex-col items-center gap-2">
                <img x-show="detailItem.photo_url" :src="detailItem.photo_url" :alt="detailItem.name" class="max-h-[60vh] max-w-full rounded-md object-contain">
                <p x-show="!detailItem.photo_url" class="text-base text-gray-600">No picture added</p>
                <p x-show="detailItem.photo_caption" x-text="detailItem.photo_caption" class="text-center text-sm text-gray-700"></p>
            </div>
        </div>

        {{-- --- Details --- --}}
        <div class="flex min-h-0 flex-col p-6 md:w-1/2">
            <div class="flex-1 space-y-4 overflow-y-auto">
                <p><span class="badge badge-info" x-text="detailItem.type === 'service' ? 'Service' : 'Product'"></span></p>
                <h2 id="detail-title" class="font-serif text-2xl font-semibold text-brand-900" x-text="detailItem.name"></h2>

                <div>
                    <p class="text-sm text-gray-600">Price</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        <template x-if="detailItem.formatted_price"><span>Rp <span x-text="detailItem.formatted_price"></span></span></template>
                        <template x-if="!detailItem.formatted_price"><span x-text="detailItem.price_text"></span></template>
                    </p>
                    <p x-show="detailItem.price_type_label" x-text="detailItem.price_type_label" class="text-sm text-gray-600"></p>
                </div>

                <div>
                    <p class="text-sm text-gray-600">Description</p>
                    <p class="whitespace-pre-line text-base text-gray-800" x-text="detailItem.description"></p>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-2 border-t border-line pt-4 sm:flex-row">
                <template x-if="detailItem.whatsapp_link">
                    <a :href="detailItem.whatsapp_link" target="_blank" rel="noopener" class="btn btn-primary flex-1"><i class="bi bi-whatsapp" aria-hidden="true"></i> Message on WhatsApp</a>
                </template>
                <button type="button" @click="detailModalOpen = false" class="btn btn-secondary flex-1">Close</button>
            </div>
        </div>
    </div>
</div>
