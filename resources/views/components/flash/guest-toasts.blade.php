<div x-data="guestToasts(@js($initial))" @notify.window="add($event)"
    class="fixed top-6 right-6 z-50 flex flex-col gap-3 items-end pointer-events-none">

    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="true" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-[-8px]"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-[-8px]"
            class="pointer-events-auto max-w-sm w-full p-4 rounded-xl shadow-lg flex items-start justify-between gap-3 text-white transition-all duration-300"
            :class="{
                'bg-emerald-600': toast.type === 'success',
                'bg-red-600': toast.type === 'error',
                'bg-blue-600': toast.type === 'info' || toast.type === 'status',
                'bg-yellow-600': toast.type === 'warning'
            }">
            <div class="flex items-center gap-3">
                <template x-if="toast.type === 'success'"><i
                        class="bi bi-check-circle-fill text-lg"></i></template>
                <template x-if="toast.type === 'error'"><i
                        class="bi bi-exclamation-triangle-fill text-lg"></i></template>
                <template x-if="toast.type === 'info' || toast.type === 'status'"><i
                        class="bi bi-info-circle-fill text-lg"></i></template>
                <template x-if="toast.type === 'warning'"><i
                        class="bi bi-exclamation-circle-fill text-lg"></i></template>
                <span class="text-sm font-semibold" x-text="toast.message"></span>
            </div>
            <button @click="remove(toast.id)" class="text-white/80 hover:text-white transition-colors">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </template>
</div>
