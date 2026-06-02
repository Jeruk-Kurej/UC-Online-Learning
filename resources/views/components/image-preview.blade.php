@props([
    'inputId'      => 'photo',
    'previewId'    => 'img-preview',
    'shape'        => 'rounded',   // 'rounded' | 'circle' | 'square'
    'maxSize'      => 10,          // MB
    'currentImage' => null,
    'currentLabel' => 'Current',
    'newLabel'     => 'New',
    'sideBySide'   => false,       // side-by-side mode (logo / profile)
    'height'       => 'h-64',      // full-preview container height
    'placeholder'  => 'Click to upload or drag & drop',
    'hint'         => 'JPG, PNG, GIF — max 10MB',
    'multiple'     => false,       // multi-photo upload support
])

@php
    $thumbClass = match($shape) {
        'circle' => 'w-24 h-24 rounded-sm object-contain',
        'square' => 'w-24 h-24 rounded-sm object-contain',
        default  => 'w-full h-full object-contain rounded-sm',
    };
    $thumbPlaceholderClass = match($shape) {
        'circle' => 'w-24 h-24 rounded-sm',
        'square' => 'w-24 h-24 rounded-sm',
        default  => 'w-full h-full rounded-sm',
    };
@endphp

@if($sideBySide)
    {{-- ===================== --}}
    {{-- SIDE-BY-SIDE MODE     --}}
    {{-- ===================== --}}
    <div class="flex items-end gap-4 flex-wrap">

        {{-- Current --}}
        <div class="flex flex-col items-center gap-1.5">
            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">{{ $currentLabel }}</span>
            @if($currentImage)
                <img src="{{ $currentImage }}"
                     alt="{{ $currentLabel }}"
                     class="{{ $thumbClass }}">
            @else
                <div class="{{ $thumbPlaceholderClass }} border-2 border-dashed border-gray-300 flex items-center justify-center">
                    <i class="bi bi-image text-gray-400 text-2xl"></i>
                </div>
            @endif
        </div>

        {{-- Arrow --}}
        <div id="{{ $previewId }}-arrow" class="hidden flex-col items-center text-uco-orange-400 pb-8 transition-all duration-300">
            <i class="bi bi-arrow-right text-xl"></i>
        </div>

        {{-- New Preview --}}
        <div id="{{ $previewId }}-wrapper" class="hidden flex-col items-center gap-1.5">
            <span class="text-[11px] font-semibold text-uco-orange-500 uppercase tracking-wider">{{ $newLabel }}</span>
            <div class="relative">
                <img id="{{ $previewId }}-img"
                     src=""
                     alt="{{ $newLabel }}"
                     class="{{ $thumbClass }}">
                <span class="absolute -top-2 -right-2 bg-uco-orange-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold shadow-lg leading-none">NEW</span>
            </div>
        </div>

        {{-- Upload Button --}}
        <div class="flex flex-col gap-2">
            <label for="{{ $inputId }}"
                   class="cursor-pointer inline-flex items-center gap-2 px-4 py-2.5 bg-white border-2 border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:border-uco-orange-400 hover:text-uco-orange-600 hover:bg-uco-orange-50 transition-all duration-200 shadow-sm group">
                <i class="bi bi-camera-fill text-base group-hover:text-uco-orange-500 transition-colors"></i>
                Choose Photo
            </label>
            <p class="text-xs text-gray-400 pl-1">{{ $hint }}</p>
            <button type="button"
                    id="{{ $previewId }}-cancel"
                    onclick="ucoCancelPreview('{{ $previewId }}', '{{ $inputId }}')"
                    class="hidden text-xs text-red-400 hover:text-red-600 pl-1 text-left transition-colors underline">
                <i class="bi bi-x-circle me-1"></i>Remove new photo
            </button>
        </div>
    </div>

@else
    {{-- ===================== --}}
    {{-- FULL DROPZONE MODE    --}}
    {{-- ===================== --}}
    <div class="space-y-3">

        {{-- Drop Zone --}}
        <div id="{{ $previewId }}-dropzone"
             class="relative border-2 border-dashed border-gray-300 rounded-lg {{ $height }} overflow-hidden cursor-pointer group hover:border-uco-orange-400 hover:bg-uco-orange-50/30 transition-all duration-300 bg-gradient-to-br from-gray-50 to-gray-100/80"
             onclick="document.getElementById('{{ $inputId }}').click()"
             ondragover="event.preventDefault(); this.classList.add('!border-uco-orange-400','!bg-uco-orange-50/50')"
             ondragleave="this.classList.remove('!border-uco-orange-400','!bg-uco-orange-50/50')"
             ondrop="ucoHandleDrop(event, '{{ $inputId }}', '{{ $previewId }}', {{ $maxSize }}, {{ $multiple ? 'true' : 'false' }})">

            {{-- Placeholder --}}
            <div id="{{ $previewId }}-placeholder"
                 class="absolute inset-0 flex flex-col items-center justify-center gap-3 p-6 transition-all duration-300">
                <div class="w-16 h-16 rounded-lg bg-white shadow-md border border-gray-100 flex items-center justify-center group-hover:scale-110 group-hover:shadow-lg transition-all duration-300">
                    <i class="bi bi-cloud-arrow-up text-3xl text-uco-orange-400 group-hover:text-uco-orange-500"></i>
                </div>
                <div class="text-center pointer-events-none">
                    <p class="text-sm font-semibold text-gray-600 group-hover:text-uco-orange-600 transition-colors">{{ $placeholder }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $hint }}</p>
                </div>
                {{-- Animated border on hover --}}
                <div class="absolute inset-2 border-2 border-dashed border-transparent group-hover:border-uco-orange-200 rounded-xl transition-all duration-300 pointer-events-none"></div>
            </div>

            {{-- Single Preview (for backward compatibility / legacy) --}}
            <div id="{{ $previewId }}-result" class="hidden absolute inset-0 opacity-0 transition-opacity duration-300 pointer-events-none">
                <img id="{{ $previewId }}-img"
                     src=""
                     alt="Preview"
                     class="w-full h-full object-contain rounded-sm">
            </div>

            {{-- Multiple Gallery Grid --}}
            <div id="{{ $previewId }}-gallery" class="hidden absolute inset-0 bg-white/40 backdrop-blur-sm p-4 overflow-y-auto">
                <div id="{{ $previewId }}-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    {{-- Grid items injected by JS --}}
                </div>
            </div>

            {{-- Change overlay (shows on hover if files exist) --}}
            <div id="{{ $previewId }}-overlay" class="hidden absolute inset-0 bg-black/0 group-hover:bg-black/35 transition-all duration-300 flex items-center justify-center pointer-events-none">
                <div class="opacity-0 group-hover:opacity-100 transition-all duration-300 text-white text-center scale-90 group-hover:scale-100">
                    <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center mx-auto mb-2 border border-white/30">
                        <i class="bi bi-pencil-fill text-lg"></i>
                    </div>
                    <p class="text-xs font-semibold">Click to change selection</p>
                </div>
            </div>
        </div>

        {{-- File Info Bar (Single mode) --}}
        <div id="{{ $previewId }}-info"
             class="hidden items-center justify-between px-4 py-2.5 bg-uco-orange-50 border border-uco-orange-200 rounded-xl animate-in">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-uco-orange-100 border border-uco-orange-200 flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-image-fill text-uco-orange-500 text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p id="{{ $previewId }}-filename" class="text-xs font-semibold text-gray-800 truncate max-w-[220px]"></p>
                    <p id="{{ $previewId }}-filesize" class="text-[11px] text-gray-500"></p>
                </div>
            </div>
            <button type="button"
                    onclick="ucoCancelPreview('{{ $previewId }}', '{{ $inputId }}')"
                    class="flex-shrink-0 w-7 h-7 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-red-500 hover:border-red-300 transition-all duration-200 shadow-sm ml-3">
                <i class="bi bi-x text-base leading-none"></i>
            </button>
        </div>

        {{-- Multi-file Badge (shows total selected) --}}
        <div id="{{ $previewId }}-multi-info" class="hidden items-center justify-between px-4 py-2 bg-gray-900 border border-gray-800 rounded-xl">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                <span id="{{ $previewId }}-count" class="text-xs font-bold text-white uppercase tracking-widest"></span>
            </div>
            <button type="button" 
                    onclick="ucoCancelPreview('{{ $previewId }}', '{{ $inputId }}')"
                    class="text-[10px] font-black text-red-400 hover:text-red-300 uppercase tracking-tighter transition-colors">
                Clear All
            </button>
        </div>

    </div>
@endif
