{{-- --- Label and value on one line in the profile card; extra classes go on the value --- --}}
@props(['label'])

<div class="-mx-2 flex items-center justify-between rounded-xl border border-transparent p-2 text-xs">
    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $label }}</span>
    <span {{ $attributes->class(['ml-2 text-right font-extrabold text-slate-700']) }}>{{ $slot }}</span>
</div>
