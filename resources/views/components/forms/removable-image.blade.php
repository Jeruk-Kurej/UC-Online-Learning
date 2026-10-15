{{-- --- Image field with preview, initials fallback and remove / undo / cancel (removablePhoto Alpine component); the slot holds the error line --- --}}
@props(['label', 'name', 'deleteName', 'url', 'hasImage' => null, 'initial', 'noun', 'hint', 'boxClass', 'actionsClass', 'imageClass' => 'photo-box__image', 'initialsClass' => 'uco-initials-bg-deep'])

<div {{ $attributes->class(['relative']) }} x-data="removablePhoto(@js($url), @js($hasImage))">
    <label class="form-label-custom">{{ $label }}</label>
    <input type="hidden" name="{{ $deleteName }}" :value="photoDeleted ? '1' : '0'">

    <div class="flex items-start gap-5">
        <div class="photo-box {{ $boxClass }}">
            <img :src="previewUrl" class="{{ $imageClass }}" x-show="hasPhoto && !photoDeleted">

            <div class="photo-box__initials {{ $initialsClass }}" x-show="!hasPhoto || photoDeleted">
                <span class="select-none text-3xl font-black text-white">{{ $initial }}</span>
            </div>

            <label for="{{ $name }}" class="absolute inset-0 z-20 cursor-pointer">
                <input type="file" name="{{ $name }}" id="{{ $name }}" accept="image/*" class="hidden" x-ref="photoInput" @change="pick($event)">
            </label>

            <div class="photo-box__overlay">
                <i class="bi bi-camera-fill text-lg text-white"></i>
                <span class="mt-1 text-[8px] font-black uppercase tracking-widest text-white">Change</span>
            </div>
        </div>

        <div class="{{ $actionsClass }} flex flex-col justify-center">
            <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ $hint }}</p>

            <template x-if="hasPhoto && !photoDeleted && !newPhotoSelected">
                <button type="button" @click="photoDeleted = true"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-red-500 transition-all hover:text-red-700">
                    <i class="bi bi-trash3 text-sm"></i>
                    <span>Remove {{ $noun }}</span>
                </button>
            </template>

            <template x-if="photoDeleted">
                <button type="button" @click="photoDeleted = false"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-500 transition-all hover:text-blue-700">
                    <i class="bi bi-arrow-counterclockwise text-sm"></i>
                    <span>Undo Delete</span>
                </button>
            </template>

            <template x-if="newPhotoSelected">
                <button type="button" @click="cancelNew()"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 transition-all hover:text-amber-700">
                    <i class="bi bi-x-circle text-sm"></i>
                    <span>Cancel New {{ $noun }}</span>
                </button>
            </template>
        </div>
    </div>
    {{ $slot }}
</div>
