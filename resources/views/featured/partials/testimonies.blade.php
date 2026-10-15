{{-- --- Community voices: testimony carousel and its reading dialog --- --}}
<section class="relative mx-auto max-w-[1600px] space-y-16 overflow-hidden px-8 py-12 md:px-12">
    {{-- --- Background decorative glows --- --}}
    <div class="uco-ambient-glow uco-ambient-glow--purple uco-floating-blob-slow -left-20 -top-20"></div>

    {{-- --- Floating micro-shapes (Ada Kehidupan) --- --}}
    <div class="uco-floating-shape uco-floating-shape--plus-blue right-[12%] top-[15%]"></div>
    <div class="uco-floating-shape uco-floating-shape--ring-blue bottom-[30%] left-[6%]"></div>
    <div class="uco-floating-shape uco-floating-shape--dot right-[30%] top-[55%]"></div>

    <div class="reveal-on-scroll relative z-10 flex flex-col justify-between gap-10 md:flex-row md:items-end">
        <div class="relative space-y-4">
            {{-- --- Outline background text --- --}}
            <div class="uco-outline-bg-text uco-outline-bg-text--purple uco-parallax-text">VOICES</div>
            <h2 class="uco-section-title relative z-10 text-5xl font-[900] tracking-tighter text-gray-950">
                Community <span class="uco-text-gradient-orange">Voices</span></h2>
            <p class="relative z-10 mt-4 max-w-2xl text-xl font-medium leading-relaxed text-gray-500">
                Real stories, journeys, and experiences shared by UCO community.
            </p>
        </div>
        <div class="flex items-center gap-4 text-xs font-black uppercase tracking-[0.25em] text-gray-300">
            <span class="h-[2px] w-12 bg-gray-100"></span>
            Student Stories
        </div>
    </div>

    <div class="relative z-10 w-full" x-data="testimonyCarousel()" x-init="init()"
         @mouseenter="stopAutoScroll()" @mouseleave="startAutoScroll()">
        <div x-ref="track" @scroll.passive="updateScroll"
             class="scrollbar-none flex snap-x snap-mandatory items-stretch gap-6 overflow-x-auto scroll-smooth pb-12 pt-4 [&::-webkit-scrollbar]:hidden">
            @forelse($testimonies as $student)
                <div data-carousel-slide
                     class="flex h-auto w-[min(100%,17rem)] shrink-0 snap-start sm:w-[calc(50%-0.75rem)] md:w-[calc((100%-3rem)/3)] lg:w-[calc((100%-4.5rem)/4)]">
                    <div class="reveal-on-scroll uco-premium-card uco-premium-card--orange group relative flex w-full cursor-pointer flex-col overflow-hidden rounded-[20px] border border-gray-200/80 bg-white transition-all duration-300 hover:-translate-y-2"
                         data-name="{{ $student->full_titled_name }}"
                         data-status="{{ $student->current_status ?? 'Member' }} at UCO Community"
                         data-photo="{{ $student->profile_photo_url ?? '' }}"
                         data-testimony="{{ $student->testimony }}"
                         @click="openModal($el.dataset.name, $el.dataset.status, $el.dataset.photo, $el.dataset.testimony)">

                        {{-- --- Top Section: Image & Info --- --}}
                        <div class="relative h-[280px] w-full flex-shrink-0">
                            @if ($student->profile_photo_url)
                                <x-premium-image :src="$student->profile_photo_url" :alt="$student->full_titled_name" class="h-full w-full" />
                            @else
                                <div class="uco-initials-bg flex h-full w-full items-center justify-center text-4xl font-black text-white">
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
                        <div class="testimony-card-body group/text relative flex flex-grow items-center justify-center rounded-b-[20px] bg-white">
                            {{-- --- Quote Icon --- --}}
                            <div
                                 class="absolute -top-5 left-1/2 z-10 flex h-10 w-10 -translate-x-1/2 items-center justify-center rounded-xl bg-uco-orange-500 text-white shadow-[0_10px_15px_-3px_rgba(247,147,30,0.3)] transition-transform duration-300 group-hover:scale-110">
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
                     class="uco-placeholder-mesh relative col-span-full w-full shrink-0 snap-start rounded-[3rem] border border-dashed border-gray-200 px-6 py-20 text-center">
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
             class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4 sm:p-6"
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
                 class="relative z-50 flex h-auto max-h-[85vh] w-full max-w-3xl flex-col overflow-hidden rounded-[2.5rem] border border-slate-100/80 bg-white shadow-[0_30px_70px_rgba(0,0,0,0.25)] md:h-[450px] md:flex-row">

                <!-- Left Column: Portrait and Info -->
                <div class="relative min-h-[220px] flex-shrink-0 bg-slate-900 md:min-h-full md:w-5/12">
                    <!-- Image -->
                    <template x-if="modalPhoto">
                        <div class="absolute inset-0 flex h-full w-full items-center justify-center">
                            <img :src="modalPhoto" :alt="modalName" class="max-h-full max-w-full rounded-sm object-contain" referrerpolicy="no-referrer">
                        </div>
                    </template>
                    <!-- Image Fallback Gradient -->
                    <template x-if="!modalPhoto">
                        <div
                             class="absolute inset-0 flex h-full w-full items-center justify-center bg-gradient-to-br from-[#f7931e] to-[#fdb913] text-7xl font-black text-white">
                            <span x-text="modalName.substring(0, 1).toUpperCase()"></span>
                        </div>
                    </template>

                    <!-- Premium Dark Bottom Overlay Gradient -->
                    <div
                         class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent">
                    </div>

                    <!-- Student identity at the bottom -->
                    <div class="absolute bottom-6 left-6 right-6 z-10 text-white">
                        <h3 class="mb-1 text-xl font-black leading-tight tracking-tight" x-text="modalName">
                        </h3>
                        <p class="text-xs font-bold uppercase tracking-wider text-orange-400"
                           x-text="modalStatus"></p>
                    </div>
                </div>

                <!-- Right Column: Scrollable Quote content -->
                <div
                     class="relative flex flex-col justify-between overflow-hidden bg-white p-8 md:w-7/12 md:p-10">
                    <!-- Close button -->
                    <button @click="closeModal()"
                            class="absolute right-5 top-5 z-30 flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-500 shadow-sm transition-colors duration-200 hover:bg-slate-200 hover:text-slate-800">
                        <i class="bi bi-x-lg text-[10px]"></i>
                    </button>

                    <!-- Background Quote Icon -->
                    <div
                         class="pointer-events-none absolute -right-4 -top-4 select-none text-orange-500 opacity-[0.06]">
                        <i class="bi bi-quote text-[150px]"></i>
                    </div>

                    <!-- Scrollable quote body -->
                    <div class="mt-4 max-h-[250px] flex-grow overflow-y-auto pr-2 md:max-h-[320px]">
                        <div class="flex items-start gap-3">
                            <span
                                  class="select-none text-2xl font-black leading-none text-orange-500">&ldquo;</span>
                            <p class="pt-1 text-sm font-medium italic leading-relaxed text-slate-700 md:text-[15px]"
                               x-text="modalText"></p>
                            <span
                                  class="select-none self-end text-2xl font-black leading-none text-orange-500">&rdquo;</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
