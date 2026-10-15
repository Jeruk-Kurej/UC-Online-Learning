{{-- --- Dots under a testimonyCarousel(): one per page of slides, the active one stretched --- --}}
@props(['mobileOnly' => false])

<div @class([
    'md:hidden' => $mobileOnly,
    'flex justify-center items-center gap-2 mt-4',
]) x-show="totalSlides > 1" x-cloak>
    <template x-for="slideIndex in dotIndices" :key="slideIndex">
        <button @click="scrollTo(slideIndex)" class="h-2 rounded-full transition-all duration-300"
                :class="activeSlide === slideIndex ? 'w-8 bg-uco-orange-500' : 'w-2 bg-gray-300 hover:bg-gray-400'">
        </button>
    </template>
</div>
