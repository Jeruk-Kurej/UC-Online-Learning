{{-- --- Profile certificates: slides of PDFs and images, images open in the page lightbox --- --}}
@if(count($user->certification_files) > 0)
    <div class="pt-6 border-t border-slate-100">
        <div x-data="{ 
            activeIndex: 0, 
            total: {{ count($user->certification_files) }}
        }" class="relative w-full h-[250px] md:h-[300px] bg-slate-50 rounded-2xl overflow-hidden group border border-slate-200/60 shadow-[0_4px_20px_rgba(0,0,0,0.02)]">

            <!-- Slides -->
            <div class="w-full h-full relative bg-slate-100 flex items-center justify-center">
                @foreach($user->certification_files as $index => $cert)
                    <div x-show="activeIndex === {{ $index }}"
                         class="absolute inset-0 w-full h-full z-10"
                         @if($index !== 0) x-cloak @endif>

                        @if($cert['isPdf'])
                            <div class="w-full h-full bg-slate-100 flex flex-col items-center justify-center p-6 text-center">
                                <div class="w-20 h-20 bg-white rounded-2xl shadow-sm border border-slate-200/60 flex items-center justify-center mb-4 transition-transform group-hover:-translate-y-1">
                                    <i class="bi bi-file-earmark-pdf-fill text-red-500 text-4xl"></i>
                                </div>
                                <h4 class="text-slate-700 font-semibold mb-1">PDF Document</h4>
                                <p class="text-slate-500 text-sm mb-4">Click below to view or download this certification.</p>
                                <a href="{{ $cert['originalUrl'] }}" target="_blank" class="px-5 py-2.5 bg-uco-orange-500 hover:bg-uco-orange-600 text-white text-sm font-medium rounded-xl transition-all shadow-sm">
                                    Open PDF
                                </a>
                            </div>
                        @else
                            <div @click="lightboxUrl = '{{ $cert['previewUrl'] }}'; showLightbox = true" 
                                 class="w-full h-full bg-slate-900/5 flex items-center justify-center group/img relative cursor-zoom-in transition-all duration-300 hover:scale-[1.02]">
                                <img src="{{ $cert['previewUrl'] }}" 
                                     class="w-full h-full object-contain" 
                                     alt="Certification Preview">
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Navigation Arrows -->
            <template x-if="total > 1">
                <div>
                    <button @click="activeIndex = (activeIndex - 1 + total) % total" 
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-slate-800 flex items-center justify-center transition backdrop-blur-sm z-20 border border-slate-200/60 shadow-sm opacity-0 group-hover:opacity-100 duration-200">
                        <i class="bi bi-chevron-left text-sm"></i>
                    </button>
                    <button @click="activeIndex = (activeIndex + 1) % total" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-slate-800 flex items-center justify-center transition backdrop-blur-sm z-20 border border-slate-200/60 shadow-sm opacity-0 group-hover:opacity-100 duration-200">
                        <i class="bi bi-chevron-right text-sm"></i>
                    </button>
                </div>
            </template>

            <!-- Dots Indicators -->
            <template x-if="total > 1">
                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-20">
                    <template x-for="(item, index) in total" :key="index">
                        <button @click="activeIndex = index" 
                                class="w-1.5 h-1.5 rounded-full transition-all duration-300"
                                :class="activeIndex === index ? 'bg-[#f7931e] w-4' : 'bg-slate-300'"></button>
                    </template>
                </div>
            </template>

            <!-- Label -->
            <div class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-white/90 backdrop-blur-md text-[9px] font-black uppercase tracking-widest text-slate-700 border border-slate-200/60 shadow-sm z-20 flex items-center gap-1.5">
                <i class="bi bi-patch-check-fill text-[#f7931e]"></i>
                <span>Certifications</span>
            </div>
        </div>
    </div>
@endif
