{{-- --- Heading over a list on a profile page: coloured bar, then "start" with "end" in the accent gradient --- --}}
@props(['start', 'end', 'tone' => 'orange'])

<div class="relative mb-4">
    <div class="flex items-center gap-2.5">
        <span @class([
            'w-1.5 h-6 bg-gradient-to-b rounded-full flex-shrink-0',
            'from-[#2563eb] to-[#60a5fa]' => $tone === 'blue',
            'from-[#f7931e] to-[#fdb913]' => $tone !== 'blue',
        ])></span>
        <h2 class="text-xl font-bold leading-none text-gray-950">{{ $start }} <span @class([
            'uco-text-gradient-blue' => $tone === 'blue',
            'uco-text-gradient-orange' => $tone !== 'blue',
        ])>{{ $end }}</span></h2>
    </div>
</div>
