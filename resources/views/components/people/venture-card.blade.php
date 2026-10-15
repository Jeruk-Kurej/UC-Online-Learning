<div class="bg-white border border-gray-100 rounded-2xl overflow-hidden {{ $look['border'] }} hover:shadow-lg transition-all duration-300 group flex flex-col sm:flex-row shadow-[0_8px_30px_rgb(0,0,0,0.02)] relative">
    <a href="{{ $href }}" class="block flex-1 flex flex-col sm:flex-row">
        {{-- --- Logo --- --}}
        <div class="w-full sm:w-52 h-48 sm:h-auto bg-gray-50 flex items-center justify-center relative border-b sm:border-b-0 sm:border-r border-gray-100 overflow-hidden flex-shrink-0">
            @if($logo)
                <img src="{{ $logo }}" @if($logoAlt) alt="{{ $logoAlt }}" @endif class="w-full h-full object-contain p-4 group-hover:scale-105 transition duration-500">
            @else
                <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-200 select-none absolute inset-0">
                    <span class="text-4xl font-black opacity-20 tracking-tighter">{{ substr($name, 0, 1) }}</span>
                </div>
            @endif
        </div>

        {{-- --- Name, details (slot) and footer --- --}}
        <div class="p-6 flex flex-col justify-between flex-1 space-y-3">
            <div class="space-y-1.5">
                <h3 class="{{ $look['title'] }} text-base leading-tight {{ $look['accent'] }} transition-colors line-clamp-1">{{ $name }}</h3>
                {{ $slot }}
            </div>

            <div class="flex items-center justify-between border-t border-gray-50 pt-3 mt-auto {{ $look['footer'] }}">
                {{ $footer ?? '' }}
                <span class="text-xs font-bold text-gray-800 flex items-center gap-1 {{ $look['accent'] }} transition duration-300 {{ $look['link'] }}">
                    View Details <i class="bi bi-arrow-right"></i>
                </span>
            </div>
        </div>
    </a>
</div>
