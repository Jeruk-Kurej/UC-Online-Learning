{{-- --- Community voices: testimony carousel and its reading dialog --- --}}
<section class="max-w-[1600px] mx-auto px-8 md:px-12 py-12 space-y-16 relative overflow-hidden">
    {{-- --- Background decorative glows --- --}}
    <div class="uco-ambient-glow uco-ambient-glow--purple uco-floating-blob-slow -top-20 -left-20"></div>

    {{-- --- Floating micro-shapes (Ada Kehidupan) --- --}}
    <div class="uco-floating-shape uco-floating-shape--plus-blue top-[15%] right-[12%]"></div>
    <div class="uco-floating-shape uco-floating-shape--ring-blue bottom-[30%] left-[6%]"></div>
    <div class="uco-floating-shape uco-floating-shape--dot top-[55%] right-[30%]"></div>

    <div class="reveal-on-scroll flex flex-col md:flex-row md:items-end justify-between gap-10 relative z-10">
        <div class="space-y-4 relative">
            {{-- --- Outline background text --- --}}
            <div class="uco-outline-bg-text uco-outline-bg-text--purple uco-parallax-text">VOICES</div>
            <h2 class="text-5xl font-[900] text-gray-950 tracking-tighter uco-section-title relative z-10">
                Community <span class="uco-text-gradient-orange">Voices</span></h2>
            <p class="text-xl font-medium text-gray-500 max-w-2xl leading-relaxed mt-4 relative z-10">
                Real stories, journeys, and experiences shared by UCO community.
            </p>
        </div>
        <div class="flex items-center gap-4 text-xs font-black text-gray-300 uppercase tracking-[0.25em]">
            <span class="w-12 h-[2px] bg-gray-100"></span>
            Student Stories
        </div>
    </div>

    <div class="relative z-10 w-full" x-data="testimonyCarousel()" x-init="init()"
        @mouseenter="stopAutoScroll()" @mouseleave="startAutoScroll()">
        <div x-ref="track" @scroll.passive="updateScroll"
            class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth gap-6 pb-12 pt-4 items-stretch [&::-webkit-scrollbar]:hidden scrollbar-none">
            @forelse($testimonies as $student)
                <div data-carousel-slide
                    class="snap-start shrink-0 w-[min(100%,17rem)] sm:w-[calc(50%-0.75rem)] md:w-[calc((100%-3rem)/3)] lg:w-[calc((100%-4.5rem)/4)] flex h-auto">
                    <div class="w-full bg-white border border-gray-200/80 rounded-[20px] overflow-hidden transition-all duration-300 hover:-translate-y-2 flex flex-col relative reveal-on-scroll uco-premium-card uco-premium-card--orange cursor-pointer group"
                        data-name="{{ $student->full_titled_name }}"
                        data-status="{{ $student->current_status ?? 'Member' }} at UCO Community"
                        data-photo="{{ $student->profile_photo_url ?? '' }}"
                        data-testimony="{{ $student->testimony }}"
                        @click="openModal($el.dataset.name, $el.dataset.status, $el.dataset.photo, $el.dataset.testimony)">

                        {{-- --- Top Section: Image & Info --- --}}
                        <div class="relative h-[280px] w-full flex-shrink-0">
                            @if ($student->profile_photo_url)
                                <x-premium-image :src="$student->profile_photo_url" :alt="$student->full_titled_name" class="w-full h-full" />
                            @else
                                <div class="w-full h-full flex items-center justify-center text-white text-4xl font-black uco-initials-bg">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </div>
                            @endif

                            {{-- --- Overlay Gradient --- --}}
                            <div class="testimony-shade"></div>

                            {{-- --- Text Content on Image --- --}}
                            <div class="testimony-card-info">
                                <h3 class="testimony-card-name">
                                    {{ $student->full_titled_name }}</h3>
                                <p class="testimony-card-role">
                                    {{ $student->current_status ?? 'Member' }} at UCO Community
                                </p>
                            </div>
                        </div>

                        {{-- --- Bottom Section: Testimony content --- --}}
                        <div class="testimony-card-body flex-grow flex items-center justify-center bg-white rounded-b-[20px] relative group/text">
                            {{-- --- Quote Icon --- --}}
                            <div
                                class="absolute -top-5 left-1/2 -translate-x-1/2 w-10 h-10 bg-uco-orange-500 rounded-xl shadow-[0_10px_15px_-3px_rgba(247,147,30,0.3)] flex items-center justify-center text-white z-10 transition-transform duration-300 group-hover:scale-110">
                                <i class="fa-solid fa-quote-left text-base"></i>
                            </div>

                            <p class="testimony-card-text transition-colors duration-300">
                                "{{ $student->testimony }}"
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="snap-start shrink-0 w-full col-span-full uco-placeholder-mesh relative rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center">
                    <div class="relative z-10 space-y-4">
                        <div
                            class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                            <i class="bi bi-chat-quote text-2xl text-uco-orange-400"></i>
                        </div>
                        <div class="space-y-1">
                            <p class="text-lg font-black text-gray-900">No Testimonies</p>
                            <p class="text-sm font-medium text-gray-500">No student testimonies are featured at
                                this moment.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <x-featured.carousel-dots />

        <!-- Premium Glassmorphic Testimony Modal -->
        <div x-show="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
            x-cloak>
            <!-- Backdrop with blur -->
            <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="closeModal()"
                class="fixed inset-0 bg-slate-950/65 backdrop-blur-md"></div>

            <!-- Modal Body Container -->
            <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative w-full max-w-3xl bg-white rounded-[2.5rem] overflow-hidden shadow-[0_30px_70px_rgba(0,0,0,0.25)] border border-slate-100/80 z-50 flex flex-col md:flex-row h-auto max-h-[85vh] md:h-[450px]">

                <!-- Left Column: Portrait and Info -->
                <div class="md:w-5/12 bg-slate-900 relative flex-shrink-0 min-h-[220px] md:min-h-full">
                    <!-- Image -->
                    <template x-if="modalPhoto">
                        <div class="w-full h-full absolute inset-0 flex items-center justify-center">
                            <img :src="modalPhoto" :alt="modalName" class="max-w-full max-h-full object-contain rounded-sm" referrerpolicy="no-referrer">
                        </div>
                    </template>
                    <!-- Image Fallback Gradient -->
                    <template x-if="!modalPhoto">
                        <div
                            class="w-full h-full absolute inset-0 flex items-center justify-center text-white text-7xl font-black bg-gradient-to-br from-[#f7931e] to-[#fdb913]">
                            <span x-text="modalName.substring(0, 1).toUpperCase()"></span>
                        </div>
                    </template>

                    <!-- Premium Dark Bottom Overlay Gradient -->
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent">
                    </div>

                    <!-- Student identity at the bottom -->
                    <div class="absolute bottom-6 left-6 right-6 text-white z-10">
                        <h3 class="text-xl font-black tracking-tight leading-tight mb-1" x-text="modalName">
                        </h3>
                        <p class="text-xs text-orange-400 font-bold uppercase tracking-wider"
                            x-text="modalStatus"></p>
                    </div>
                </div>

                <!-- Right Column: Scrollable Quote content -->
                <div
                    class="md:w-7/12 p-8 md:p-10 flex flex-col justify-between overflow-hidden bg-white relative">
                    <!-- Close button -->
                    <button @click="closeModal()"
                        class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors duration-200 shadow-sm z-30">
                        <i class="bi bi-x-lg text-[10px]"></i>
                    </button>

                    <!-- Background Quote Icon -->
                    <div
                        class="absolute -top-4 -right-4 opacity-[0.06] text-orange-500 pointer-events-none select-none">
                        <i class="bi bi-quote text-[150px]"></i>
                    </div>

                    <!-- Scrollable quote body -->
                    <div class="flex-grow overflow-y-auto pr-2 mt-4 max-h-[250px] md:max-h-[320px]">
                        <div class="flex gap-3 items-start">
                            <span
                                class="text-orange-500 text-2xl font-black select-none leading-none">&ldquo;</span>
                            <p class="text-slate-700 font-medium italic leading-relaxed text-sm md:text-[15px] pt-1"
                                x-text="modalText"></p>
                            <span
                                class="text-orange-500 text-2xl font-black select-none leading-none self-end">&rdquo;</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
