{{-- --- File field: a box to click or drop onto until a file is picked (fileDrop Alpine component). Slot "selection" shows the picked file, the default slot holds the error line --- --}}
@props(['label', 'name', 'accept', 'prompt', 'formats', 'icon' => 'bi-cloud-upload'])

<div {{ $attributes->class(['relative']) }} x-data="fileDrop()">
    <label for="{{ $name }}" class="field-label">{{ $label }}</label>

    {{ $selection }}

    <div x-show="!selected" class="relative flex items-center gap-4 rounded-md border border-dashed border-gray-400 bg-white p-4 focus-within:ring-2 focus-within:ring-brand-300 hover:border-brand-600">
        <i class="bi {{ $icon }} text-3xl text-brand-700" aria-hidden="true"></i>
        <div>
            <span class="block text-base font-medium text-gray-900">{{ $prompt }}</span>
            <span class="block text-sm text-gray-600">{{ $formats }}</span>
        </div>
        <input type="file" name="{{ $name }}" id="{{ $name }}" accept="{{ $accept }}" class="absolute inset-0 h-full w-full cursor-pointer opacity-0" x-ref="fileInput" @change="pick($event)">
    </div>
    {{ $slot }}
</div>
