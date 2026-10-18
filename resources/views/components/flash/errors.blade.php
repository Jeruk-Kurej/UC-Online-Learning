<div x-data="autoHide(7000)" x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-[-10px] scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="pointer-events-auto flex w-full max-w-md items-start justify-between gap-3 rounded-2xl border border-red-100/90 bg-white/95 p-4 text-slate-800 shadow-[0_20px_40px_-5px_rgba(0,0,0,0.08),0_1px_4px_rgba(0,0,0,0.02)] backdrop-blur-md transition-all duration-300"
     role="alert">
    <div class="flex min-w-0 flex-1 items-start gap-3">
        <div
             class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600 ring-4 ring-red-500/10">
            <i class="bi bi-exclamation-triangle-fill text-base"></i>
        </div>
        <div class="flex min-w-0 flex-1 flex-col gap-1">
            <span class="text-xs font-bold leading-snug text-red-800">Validation Error</span>
            <ul class="ml-4 mt-1 list-disc space-y-0.5 text-xs font-medium text-gray-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <button @click="show = false"
            class="mt-0.5 flex-shrink-0 rounded-lg p-1 text-gray-600 transition-colors hover:bg-slate-50 hover:text-slate-600">
        <i class="bi bi-x-lg text-xs"></i>
    </button>
</div>
