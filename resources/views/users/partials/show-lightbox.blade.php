{{-- --- Full-screen image viewer for certificates and activity photos (showLightbox / lightboxUrl on the page) --- --}}
<div x-show="showLightbox" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-label="Image viewer" @keydown.escape.window="showLightbox = false">
    <div class="absolute inset-0 bg-black/80" @click="showLightbox = false"></div>
    <div class="relative">
        <img :src="lightboxUrl" alt="" class="max-h-[85vh] max-w-[95vw] rounded-md bg-white object-contain">
        <button type="button" @click="showLightbox = false" class="btn btn-secondary absolute right-2 top-2">Close</button>
    </div>
</div>
