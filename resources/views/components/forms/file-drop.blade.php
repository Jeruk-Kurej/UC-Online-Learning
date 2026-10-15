{{-- --- File field: drop box until a file is picked (fileDrop Alpine component). Slot "selection" shows the picked file, the default slot holds the error line --- --}}
@props(['label', 'name', 'accept', 'prompt', 'formats', 'icon' => 'bi-cloud-upload'])

<div {{ $attributes->class(['relative']) }} x-data="fileDrop()">
    <label class="form-label-custom">{{ $label }}</label>

    {{ $selection }}

    <div x-show="!selected" class="form-file-container-custom group relative">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-blue-500 transition-all">
                <i class="bi {{ $icon }} text-base"></i>
            </div>
            <div>
                <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900 transition-colors block leading-tight">{{ $prompt }}</span>
                <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">{{ $formats }}</span>
            </div>
        </div>
        <input type="file" name="{{ $name }}" id="{{ $name }}" accept="{{ $accept }}"
               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
               x-ref="fileInput" @change="pick($event)">
    </div>
    {{ $slot }}
</div>
