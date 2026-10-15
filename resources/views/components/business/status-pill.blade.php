{{-- --- Status pill (App\View\Components\Business\StatusPill picks the colour and icon) --- --}}
<span class="{{ $colorClass }} flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-[9px] font-black uppercase tracking-wider shadow-sm">
    <i class="bi {{ $icon }} text-[9px]"></i>
    {{ $item->status_label }}
</span>
