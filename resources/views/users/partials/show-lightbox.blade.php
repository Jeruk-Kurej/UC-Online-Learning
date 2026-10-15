{{-- --- Full-screen image viewer for certificates and activity photos (showLightbox / lightboxUrl on the page) --- --}}
<div x-show="showLightbox" x-cloak
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
    <div class="absolute inset-0 cursor-zoom-out bg-black/95" @click="showLightbox = false"></div>
    <div class="pointer-events-none relative flex max-h-[90vh] max-w-5xl items-center justify-center">
        <img :src="lightboxUrl"
             class="pointer-events-auto max-h-[92vh] max-w-[95vw] cursor-zoom-out rounded-lg object-contain shadow-2xl"
             @click="showLightbox = false"
             @keydown.escape.window="showLightbox = false">
    </div>
</div>
