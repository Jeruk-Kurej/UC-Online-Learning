{{-- --- File field: drop box until a file is picked (fileDrop Alpine component). Slot "selection" shows the picked file, the default slot holds the error line --- --}}
@props(['label', 'name', 'accept', 'prompt', 'formats', 'icon' => 'bi-cloud-upload'])

<div {{ $attributes->class(['relative']) }} x-data="fileDrop()">
    <label class="form-label-custom">{{ $label }}</label>

    {{ $selection }}

    <div x-show="!selected" class="form-file-container-custom group relative">
        <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-100 bg-white text-gray-400 transition-all group-hover:text-blue-500">
                <i class="bi {{ $icon }} text-base"></i>
            </div>
            <div>
                <span class="block text-sm font-semibold leading-tight text-gray-600 transition-colors group-hover:text-gray-900">{{ $prompt }}</span>
                <span class="text-[10px] font-bold uppercase tracking-tight text-gray-400">{{ $formats }}</span>
            </div>
        </div>
        <input type="file" name="{{ $name }}" id="{{ $name }}" accept="{{ $accept }}"
               class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0"
               x-ref="fileInput" @change="pick($event)">
    </div>
    {{ $slot }}
</div>
