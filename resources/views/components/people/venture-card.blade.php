<div class="{{ $look['border'] }} group relative flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.02)] transition-all duration-300 hover:shadow-lg sm:flex-row">
    <a href="{{ $href }}" class="block flex flex-1 flex-col sm:flex-row">
        {{-- --- Logo --- --}}
        <div class="relative flex h-48 w-full flex-shrink-0 items-center justify-center overflow-hidden border-b border-gray-100 bg-gray-50 sm:h-auto sm:w-52 sm:border-b-0 sm:border-r">
            @if ($logo)
                <img src="{{ $logo }}" @if ($logoAlt) alt="{{ $logoAlt }}" @endif class="h-full w-full object-contain p-4 transition duration-500 group-hover:scale-105">
            @else
                <div class="absolute inset-0 flex h-full w-full select-none items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 text-gray-200">
                    <span class="text-4xl font-bold opacity-20">{{ substr($name, 0, 1) }}</span>
                </div>
            @endif
        </div>

        {{-- --- Name, details (slot) and footer --- --}}
        <div class="flex flex-1 flex-col justify-between space-y-3 p-6">
            <div class="space-y-1.5">
                <h3 class="{{ $look['title'] }} {{ $look['accent'] }} line-clamp-1 text-base leading-tight transition-colors">{{ $name }}</h3>
                {{ $slot }}
            </div>

            <div class="{{ $look['footer'] }} mt-auto flex items-center justify-between border-t border-gray-50 pt-3">
                {{ $footer ?? '' }}
                <span class="{{ $look['accent'] }} {{ $look['link'] }} flex items-center gap-1 text-xs font-bold text-gray-800 transition duration-300">
                    View Details <i class="bi bi-arrow-right"></i>
                </span>
            </div>
        </div>
    </a>
</div>
