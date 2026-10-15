{{-- --- Full-screen image viewer for certificates and activity photos (showLightbox / lightboxUrl on the page) --- --}}
<div x-show="showLightbox" x-cloak
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/95 cursor-zoom-out" @click="showLightbox = false"></div>
    <div class="relative max-w-5xl max-h-[90vh] flex items-center justify-center pointer-events-none">
        <img :src="lightboxUrl" 
             class="max-w-[95vw] max-h-[92vh] object-contain rounded-lg shadow-2xl pointer-events-auto cursor-zoom-out" 
             @click="showLightbox = false" 
             @keydown.escape.window="showLightbox = false">
    </div>
</div>
