{{-- --- Status pill (App\View\Components\Business\StatusPill picks the colour and icon) --- --}}
<span class="{{ $colorClass }} flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-bold shadow-sm">
    <i class="bi {{ $icon }} text-xs"></i>
    {{ $item->status_label }}
</span>
