{{-- --- Image field with preview, initials fallback and remove / undo / cancel (removablePhoto Alpine component); the slot holds the error line --- --}}
@props([
    'label',
    'name',
    'deleteName',
    'url',
    'hasImage' => null,
    'initial',
    'noun',
    'hint',
    'boxClass',
    'actionsClass',
    'imageClass' => 'photo-box__image',
    'initialsClass' => 'uco-initials-bg-deep',
])

<div {{ $attributes->class(['relative']) }} x-data="removablePhoto(@js($url), @js($hasImage))">
    <label class="form-label-custom">{{ $label }}</label>
    <input type="hidden" name="{{ $deleteName }}" :value="photoDeleted ? '1' : '0'">

    <div class="flex items-start gap-5">
        <div class="photo-box {{ $boxClass }}">
            <img :src="previewUrl" class="{{ $imageClass }}" x-show="hasPhoto && !photoDeleted">

            <div class="photo-box__initials {{ $initialsClass }}" x-show="!hasPhoto || photoDeleted">
                <span class="text-3xl font-black text-white select-none">{{ $initial }}</span>
            </div>

            <label for="{{ $name }}" class="absolute inset-0 cursor-pointer z-20">
                <input type="file" name="{{ $name }}" id="{{ $name }}" accept="image/*" class="hidden" x-ref="photoInput" @change="pick($event)">
            </label>

            <div class="photo-box__overlay">
                <i class="bi bi-camera-fill text-white text-lg"></i>
                <span class="text-white text-[8px] font-black uppercase tracking-widest mt-1">Change</span>
            </div>
        </div>

        <div class="flex flex-col justify-center {{ $actionsClass }}">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-2">{{ $hint }}</p>

            <template x-if="hasPhoto && !photoDeleted && !newPhotoSelected">
                <button type="button" @click="photoDeleted = true"
                        class="inline-flex items-center gap-1.5 text-xs text-red-500 hover:text-red-700 font-bold transition-all">
                    <i class="bi bi-trash3 text-sm"></i>
                    <span>Remove {{ $noun }}</span>
                </button>
            </template>

            <template x-if="photoDeleted">
                <button type="button" @click="photoDeleted = false"
                        class="inline-flex items-center gap-1.5 text-xs text-blue-500 hover:text-blue-700 font-bold transition-all">
                    <i class="bi bi-arrow-counterclockwise text-sm"></i>
                    <span>Undo Delete</span>
                </button>
            </template>

            <template x-if="newPhotoSelected">
                <button type="button" @click="cancelNew()"
                        class="inline-flex items-center gap-1.5 text-xs text-amber-600 hover:text-amber-700 font-bold transition-all">
                    <i class="bi bi-x-circle text-sm"></i>
                    <span>Cancel New {{ $noun }}</span>
                </button>
            </template>
        </div>
    </div>
    {{ $slot }}
</div>
