{{-- --- Contact link in the profile card (App\View\Components\People\ContactRow picks the hover tint) --- --}}
<a href="{{ $href }}" @if ($newTab) target="_blank" @endif
   class="{{ $rowClass }} group -mx-2 flex items-center justify-between rounded-xl border border-transparent p-2 text-xs transition duration-200">
    <span class="{{ $labelClass }} flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 transition-colors">
        <i class="bi {{ $icon }} {{ $iconClass }} text-sm"></i>
        {{ $label }}
    </span>
    <span class="{{ $valueClass }} ml-2 break-all text-right font-extrabold text-slate-700 transition-colors">
        {{ $slot }}
    </span>
</a>
