{{-- --- One of the Entrepreneurs / Intrapreneurs cards above the directory; businessDirectory.tabClass() picks the active look --- --}}
@props(['type', 'icon', 'label'])

<a href="{{ route('businesses.index', ['view' => $type]) }}" @click.prevent="updateType(@js($type), $el.href)"
   class="group relative flex w-full select-none items-center justify-start gap-2 overflow-hidden rounded-xl border bg-white/90 px-3 py-3 backdrop-blur-md transition-all duration-300 sm:w-auto sm:min-w-[240px] sm:gap-4 sm:rounded-2xl sm:px-6 sm:py-4"
   :class="tabClass(@js($type), 'card')">
    <div class="absolute inset-0 bg-gradient-to-br from-uco-orange-500/5 to-yellow-500/5 opacity-0 transition-opacity duration-500" :class="tabClass(@js($type), 'glow')"></div>
    <div class="pointer-events-none absolute inset-y-0 -left-1/3 w-1/3 skew-x-12 bg-gradient-to-r from-transparent via-white/80 to-transparent transition-all" :class="tabClass(@js($type), 'sweep')"></div>
    <div class="absolute -bottom-6 -right-6 h-16 w-16 scale-75 rounded-full bg-uco-orange-500/15 opacity-0 blur-md filter transition-all duration-500" :class="tabClass(@js($type), 'orb')"></div>

    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl transition-all duration-300" :class="tabClass(@js($type), 'icon')">
        <i class="bi {{ $icon }} text-lg"></i>
    </div>

    <div class="flex min-w-0 flex-col text-left">
        <span class="text-xs font-bold text-gray-600">Directory</span>
        <span class="truncate text-xs font-bold transition-colors duration-300 sm:text-sm" :class="tabClass(@js($type), 'label')">{{ $label }}</span>
    </div>
</a>
