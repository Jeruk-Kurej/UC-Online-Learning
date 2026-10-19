{{-- --- Preview of the image picked in x-forms.file-drop (its "selection" slot), with a button to clear it --- --}}
@props(['label'])

<div x-show="selected" class="flex items-center gap-4">
    <span class="photo-frame h-20 w-20">
        <img :src="fileUrl" alt="" class="absolute inset-0 h-full w-full bg-white object-contain">
    </span>
    <div class="space-y-2">
        <p class="text-base text-gray-800" x-text="fileName">{{ $label }}</p>
        <button type="button" @click="clear()" class="btn btn-secondary btn-sm">Remove</button>
    </div>
</div>
