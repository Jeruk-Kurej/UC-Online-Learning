<div x-data="{ show: true }" x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-[-10px] scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95" x-init="setTimeout(() => show = false, 7000)"
    class="pointer-events-auto max-w-md w-full p-4 bg-white/95 backdrop-blur-md rounded-2xl border border-red-100/90 shadow-[0_20px_40px_-5px_rgba(0,0,0,0.08),0_1px_4px_rgba(0,0,0,0.02)] flex items-start justify-between gap-3 text-slate-800 transition-all duration-300"
    role="alert">
    <div class="flex items-start gap-3 flex-1 min-w-0">
        <div
            class="flex items-center justify-center w-8 h-8 rounded-xl flex-shrink-0 bg-red-50 text-red-600 ring-4 ring-red-500/10 mt-0.5">
            <i class="bi bi-exclamation-triangle-fill text-base"></i>
        </div>
        <div class="flex flex-col gap-1 flex-1 min-w-0">
            <span class="text-xs font-bold text-red-800 leading-snug">Validation Error</span>
            <ul class="text-[11px] text-slate-500 font-medium list-disc ml-4 space-y-0.5 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <button @click="show = false"
        class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-50 flex-shrink-0 mt-0.5">
        <i class="bi bi-x-lg text-xs"></i>
    </button>
</div>
