{{-- --- Preview of the image picked in x-forms.file-drop (its "selection" slot), with a Clear Selection button --- --}}
@props(['label', 'frameClass', 'imageClass'])

<div x-show="selected" class="mb-3 flex items-center gap-4">
    <div class="{{ $frameClass }}">
        <img :src="fileUrl" class="{{ $imageClass }}">
    </div>
    <div class="flex flex-col gap-1.5">
        <span class="text-xs font-bold uppercase tracking-wider text-gray-500" x-text="fileName">{{ $label }}</span>
        <button type="button" @click="clear()"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-red-500 transition-all hover:text-red-700">
            <i class="bi bi-x-circle text-sm"></i>
            <span>Clear Selection</span>
        </button>
    </div>
</div>
