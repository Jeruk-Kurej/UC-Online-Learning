{{-- --- Profile certificates: slides of PDFs and images, images open in the page lightbox --- --}}
@if (count($user->certification_files) > 0)
    <div class="border-t border-slate-100 pt-6">
        <div x-data="slideCarousel(@js(count($user->certification_files)))" class="group relative h-[250px] w-full overflow-hidden rounded-2xl border border-slate-200/60 bg-slate-50 shadow-[0_4px_20px_rgba(0,0,0,0.02)] md:h-[300px]">

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
                            <div @click="open(@js($cert['previewUrl']))"
                                 class="group/img relative flex h-full w-full cursor-zoom-in items-center justify-center bg-slate-900/5 transition-all duration-300 hover:scale-[1.02]">
                                <img src="{{ $cert['previewUrl'] }}"
                                     class="h-full w-full object-contain"
                                     alt="Certification Preview">
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <x-people.slide-controls layer="z-20" />

            <x-people.slide-label layer="z-20">
                <i class="bi bi-patch-check-fill text-[#f7931e]"></i>
                <span>Certifications</span>
            </x-people.slide-label>
        </div>
    </div>
@endif
