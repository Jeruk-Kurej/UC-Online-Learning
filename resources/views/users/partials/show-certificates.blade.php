{{-- --- Profile certificates: slides of PDFs and images, images open in the page lightbox --- --}}
@if (count($user->certification_files) > 0)
    <div class="border-t border-slate-100 pt-6">
        <div x-data="{
            activeIndex: 0,
            total: {{ count($user->certification_files) }}
        }" class="group relative h-[250px] w-full overflow-hidden rounded-2xl border border-slate-200/60 bg-slate-50 shadow-[0_4px_20px_rgba(0,0,0,0.02)] md:h-[300px]">

            {{-- --- Slides --- --}}
            <div class="relative flex h-full w-full items-center justify-center bg-slate-100">
                @foreach ($user->certification_files as $index => $cert)
                    <div x-show="activeIndex === {{ $index }}"
                         class="absolute inset-0 z-10 h-full w-full"
                         @if ($index !== 0) x-cloak @endif>

                        @if ($cert['isPdf'])
                            <div class="flex h-full w-full flex-col items-center justify-center bg-slate-100 p-6 text-center">
                                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-2xl border border-slate-200/60 bg-white shadow-sm transition-transform group-hover:-translate-y-1">
                                    <i class="bi bi-file-earmark-pdf-fill text-4xl text-red-500"></i>
                                </div>
                                <h4 class="mb-1 font-semibold text-slate-700">PDF Document</h4>
                                <p class="mb-4 text-sm text-slate-500">Click below to view or download this certification.</p>
                                <a href="{{ $cert['originalUrl'] }}" target="_blank" class="rounded-xl bg-uco-orange-500 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-uco-orange-600">
                                    Open PDF
                                </a>
                            </div>
                        @else
                            <div @click="lightboxUrl = '{{ $cert['previewUrl'] }}'; showLightbox = true"
                                 class="group/img relative flex h-full w-full cursor-zoom-in items-center justify-center bg-slate-900/5 transition-all duration-300 hover:scale-[1.02]">
                                <img src="{{ $cert['previewUrl'] }}"
                                     class="h-full w-full object-contain"
                                     alt="Certification Preview">
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- --- Navigation Arrows --- --}}
            <template x-if="total > 1">
                <div>
                    <button @click="activeIndex = (activeIndex - 1 + total) % total"
                            class="absolute left-3 top-1/2 z-20 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200/60 bg-white/90 text-slate-800 opacity-0 shadow-sm backdrop-blur-sm transition duration-200 hover:bg-white group-hover:opacity-100">
                        <i class="bi bi-chevron-left text-sm"></i>
                    </button>
                    <button @click="activeIndex = (activeIndex + 1) % total"
                            class="absolute right-3 top-1/2 z-20 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200/60 bg-white/90 text-slate-800 opacity-0 shadow-sm backdrop-blur-sm transition duration-200 hover:bg-white group-hover:opacity-100">
                        <i class="bi bi-chevron-right text-sm"></i>
                    </button>
                </div>
            </template>

            {{-- --- Dots Indicators --- --}}
            <template x-if="total > 1">
                <div class="absolute bottom-3 left-1/2 z-20 flex -translate-x-1/2 gap-1.5">
                    <template x-for="(item, index) in total" :key="index">
                        <button @click="activeIndex = index"
                                class="h-1.5 w-1.5 rounded-full transition-all duration-300"
                                :class="activeIndex === index ? 'bg-[#f7931e] w-4' : 'bg-slate-300'"></button>
                    </template>
                </div>
            </template>

            {{-- --- Label --- --}}
            <div class="absolute left-3 top-3 z-20 flex items-center gap-1.5 rounded-md border border-slate-200/60 bg-white/90 px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-slate-700 shadow-sm backdrop-blur-md">
                <i class="bi bi-patch-check-fill text-[#f7931e]"></i>
                <span>Certifications</span>
            </div>
        </div>
    </div>
@endif
