{{-- --- Green or red dot for a visible or hidden record, with a hover tip --- --}}
@props(['visible'])

<div {{ $attributes->class(['group relative flex-shrink-0']) }}>
    <span @class([
        'inline-block h-2.5 w-2.5 rounded-full',
        $visible ? 'bg-emerald-400' : 'bg-red-400',
    ])></span>
    <x-ui.tooltip>{{ $visible ? 'Visible' : 'Hidden' }}</x-ui.tooltip>
</div>
