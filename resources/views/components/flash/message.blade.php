<div x-data="autoHide(4000)" x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-[-10px] scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
     class="{{ $border }} pointer-events-auto flex w-full max-w-sm items-center justify-between gap-3 rounded-2xl border bg-white/95 p-3.5 text-slate-800 shadow-[0_20px_40px_-5px_rgba(0,0,0,0.08),0_1px_4px_rgba(0,0,0,0.02)] backdrop-blur-md transition-all duration-300"
     role="alert">
    <div class="flex items-center gap-3">
        <div
             class="{{ $badge }} flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl">
            <i class="bi {{ $icon }} text-base"></i>
        </div>
        <span
              class="text-xs font-bold leading-snug text-slate-700">{{ $message }}</span>
    </div>
    <button @click="show = false"
            class="flex-shrink-0 rounded-lg p-1 text-slate-400 transition-colors hover:bg-slate-50 hover:text-slate-600">
        <i class="bi bi-x-lg text-xs"></i>
    </button>
</div>
