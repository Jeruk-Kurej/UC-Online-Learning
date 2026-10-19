<div x-data="autoHide(9000)" x-show="show" x-transition.opacity.duration.150ms class="toast toast-error" role="alert">
    <i class="bi bi-exclamation-triangle-fill mt-0.5 text-lg text-red-700" aria-hidden="true"></i>
    <div class="flex-1">
        <p class="text-base font-semibold text-gray-900">Please fix the following</p>
        <ul class="mt-1 list-disc space-y-0.5 pl-5 text-base text-gray-800">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <button type="button" @click="show = false" class="-m-1 rounded p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900" aria-label="Close message">
        <i class="bi bi-x-lg" aria-hidden="true"></i>
    </button>
</div>
