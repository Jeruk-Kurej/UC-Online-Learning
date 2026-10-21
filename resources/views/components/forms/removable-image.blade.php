{{-- --- Image field with preview, initial (or icon) fallback and replace / remove / undo (removablePhoto Alpine component); the slot holds the error line; removable="false" hides Remove for a photo that must stay --- --}}
@props(['label', 'name', 'deleteName', 'url', 'hasImage' => null, 'initial' => '', 'icon' => null, 'noun', 'hint', 'removable' => true])

<div {{ $attributes->class(['relative']) }} x-data="removablePhoto(@js($url), @js($hasImage))">
    <span class="field-label">{{ $label }}</span>
    <input type="hidden" name="{{ $deleteName }}" :value="photoDeleted ? '1' : '0'">

    <div class="flex items-center gap-4">
        <span class="photo-frame h-24 w-24 text-3xl font-semibold text-brand-800">
            <span x-show="!hasPhoto || photoDeleted" aria-hidden="true">
                @if ($icon)
                    <i class="bi {{ $icon }} text-3xl text-gray-500"></i>
                @else
                    {{ $initial }}
                @endif
            </span>
            <img :src="previewUrl" alt="" x-show="hasPhoto && !photoDeleted" class="absolute inset-0 h-full w-full bg-white object-contain" referrerpolicy="no-referrer">
        </span>

        <div class="space-y-2">
            <p class="text-sm text-gray-600">{{ $hint }}</p>
            <div class="flex flex-wrap gap-2">
                <label class="btn btn-secondary btn-sm cursor-pointer focus-within:ring-2 focus-within:ring-brand-500">
                    Change {{ strtolower($noun) }}
                    <input type="file" name="{{ $name }}" id="{{ $name }}" accept="image/*" class="sr-only" x-ref="photoInput" @change="pick($event)">
                </label>

                @if ($removable)
                    <template x-if="hasPhoto && !photoDeleted && !newPhotoSelected">
                        <button type="button" @click="photoDeleted = true" class="btn btn-secondary btn-sm">Remove {{ strtolower($noun) }}</button>
                    </template>
                    <template x-if="photoDeleted">
                        <button type="button" @click="photoDeleted = false" class="btn btn-secondary btn-sm">Keep {{ strtolower($noun) }}</button>
                    </template>
                @endif
                <template x-if="newPhotoSelected">
                    <button type="button" @click="cancelNew()" class="btn btn-secondary btn-sm">Cancel change</button>
                </template>
            </div>
        </div>
    </div>
    {{ $slot }}
</div>
