<div x-data="guestToasts(@js($initial))" @notify.window="add($event)" class="toast-stack">
    <template x-for="toast in toasts" :key="toast.id">
        <div class="toast" :class="{ 'toast-success': toast.type === 'success', 'toast-error': toast.type === 'error', 'toast-info': toast.type === 'info' || toast.type === 'status', 'toast-warning': toast.type === 'warning' }" role="status">
            <i class="bi mt-0.5 text-lg" :class="{ 'bi-check-circle-fill text-green-700': toast.type === 'success', 'bi-exclamation-triangle-fill text-red-700': toast.type === 'error', 'bi-info-circle-fill text-brand-600': toast.type === 'info' || toast.type === 'status', 'bi-exclamation-circle-fill text-amber-600': toast.type === 'warning' }" aria-hidden="true"></i>
            <p class="flex-1 text-base text-gray-900" x-text="toast.message"></p>
            <button type="button" @click="remove(toast.id)" class="-m-1 rounded p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900" aria-label="Close message">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </div>
    </template>
</div>
