<div x-data="{ show: true }" x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-[-10px] scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
    x-init="setTimeout(() => show = false, 4000)"
    class="pointer-events-auto max-w-sm w-full p-3.5 bg-white/95 backdrop-blur-md rounded-2xl border {{ $border }} shadow-[0_20px_40px_-5px_rgba(0,0,0,0.08),0_1px_4px_rgba(0,0,0,0.02)] flex items-center justify-between gap-3 text-slate-800 transition-all duration-300"
    role="alert">
    <div class="flex items-center gap-3">
        <div
            class="flex items-center justify-center w-8 h-8 rounded-xl flex-shrink-0 {{ $badge }}">
            <i class="bi {{ $icon }} text-base"></i>
        </div>
        <span
            class="text-xs font-bold text-slate-700 leading-snug">{{ $message }}</span>
    </div>
    <button @click="show = false"
        class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-50 flex-shrink-0">
        <i class="bi bi-x-lg text-xs"></i>
    </button>
</div>
