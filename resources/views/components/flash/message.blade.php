<div x-data="autoHide(5000)" x-show="show" x-transition.opacity.duration.150ms class="toast {{ $class }}" role="status">
    <i class="bi {{ $icon }} {{ $iconColor }} mt-0.5 text-lg" aria-hidden="true"></i>
    <p class="flex-1 text-base text-gray-900">{{ $message }}</p>
    <button type="button" @click="show = false" class="-m-1 rounded p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900" aria-label="Close message">
        <i class="bi bi-x-lg" aria-hidden="true"></i>
    </button>
</div>
