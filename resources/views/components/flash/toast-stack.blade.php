<div x-data="toastStack" @notify.window="add($event)"
     class="pointer-events-none fixed right-6 top-6 z-50 flex flex-col items-end gap-3">

    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="true" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-[-10px] scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="pointer-events-auto flex w-full max-w-sm items-center justify-between gap-3 rounded-2xl border border-slate-100/90 bg-white/95 p-3.5 text-slate-800 shadow-[0_20px_40px_-5px_rgba(0,0,0,0.08),0_1px_4px_rgba(0,0,0,0.02)] backdrop-blur-md transition-all duration-300">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl"
                     :class="{
                         'bg-emerald-50 text-emerald-600 ring-4 ring-emerald-500/10': toast
                             .type === 'success',
                         'bg-red-50 text-red-600 ring-4 ring-red-500/10': toast.type === 'error',
                         'bg-blue-50 text-blue-600 ring-4 ring-blue-500/10': toast
                             .type === 'info' || toast.type === 'status',
                         'bg-yellow-50 text-yellow-600 ring-4 ring-yellow-500/10': toast
                             .type === 'warning'
                     }">
                    <template x-if="toast.type === 'success'"><i
                           class="bi bi-check-circle-fill text-base"></i></template>
                    <template x-if="toast.type === 'error'"><i
                           class="bi bi-exclamation-triangle-fill text-base"></i></template>
                    <template x-if="toast.type === 'info' || toast.type === 'status'"><i
                           class="bi bi-info-circle-fill text-base"></i></template>
                    <template x-if="toast.type === 'warning'"><i
                           class="bi bi-exclamation-circle-fill text-base"></i></template>
                </div>
                <span class="text-xs font-bold leading-snug text-slate-700"
                      x-text="toast.message"></span>
            </div>
            <button @click="remove(toast.id)"
                    class="flex-shrink-0 rounded-lg p-1 text-gray-600 transition-colors hover:bg-slate-50 hover:text-slate-600">
                <i class="bi bi-x-lg text-xs"></i>
            </button>
        </div>
    </template>
</div>
