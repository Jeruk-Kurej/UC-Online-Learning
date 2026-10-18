{{-- --- Small tag in the top-left corner of a profile carousel --- --}}
@props(['layer'])

<div @class([
    $layer,
    'absolute left-3 top-3 flex items-center gap-1.5 rounded-md border border-slate-200/60 bg-white/90 px-2.5 py-1 text-xs font-bold   text-slate-700 shadow-sm backdrop-blur-md',
])>
    {{ $slot }}
</div>
