{{-- --- One product or service on the business page; a click opens the detail modal (businessShowPage) --- --}}
@props(['business', 'offering', 'canManage'])

<div @click="openDetailModal({
        name: {{ Js::from($offering->name) }},
        description: {{ Js::from($offering->description) }},
        price: {{ Js::from($offering->price) }},
        price_type: {{ Js::from($offering->price_type) }},
        photo_url: {{ Js::from($offering->has_photo ? $offering->photo_url : null) }},
        photo_caption: {{ Js::from($offering->photo_caption) }},
        formatted_price: {{ Js::from($offering->formatted_price) }},
        price_text: {{ Js::from($offering->price_text) }},
        price_type_label: {{ Js::from($offering->price_type_label) }},
        whatsapp_link: {{ Js::from($business->whatsappLinkFor($offering)) }},
        type: '{{ $offering->type }}'
     })"
    class="group relative flex flex-col h-full border border-gray-200 rounded-2xl overflow-hidden hover:shadow-xl hover:border-orange-200 transition-all duration-300 cursor-pointer bg-white">

    {{-- --- Edit / delete for people who manage the business --- --}}
    @auth
        @if ($canManage)
            <div class="absolute top-3 right-3 flex items-center gap-1.5 z-10" @click.stop>
                <a href="{{ route('businesses.'.$offering->type.'s.edit', [$business, $offering]) }}"
                    class="inline-flex items-center justify-center w-8 h-8 bg-white/95 hover:bg-white text-gray-700 hover:text-uco-orange-600 rounded-full shadow-md transition-all duration-300 border border-gray-100"
                    title="Edit {{ ucfirst($offering->type) }}">
                    <i class="bi bi-pencil-square"></i>
                </a>
                <form action="{{ route('businesses.'.$offering->type.'s.destroy', [$business, $offering]) }}"
                    method="POST"
                    data-confirm="Are you sure you want to delete this {{ $offering->type }}?"
                    class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center justify-center w-8 h-8 bg-white/95 hover:bg-red-50 text-red-600 hover:text-red-700 rounded-full shadow-md transition-all duration-300 border border-gray-100"
                        title="Delete {{ ucfirst($offering->type) }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        @endif
    @endauth

    {{-- --- Photo --- --}}
    <div class="relative aspect-square w-full flex items-center justify-center overflow-hidden flex-shrink-0">
        @if ($offering->has_photo)
            <x-premium-image :src="$offering->photo_url" :alt="$offering->name" class="w-full h-full group-hover:scale-105 transition-transform duration-500" />
            @if($offering->photo_caption)
                <div class="absolute bottom-0 left-0 right-0 bg-black/60 backdrop-blur-sm p-2 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                    <p class="text-[10px] text-white font-medium text-center line-clamp-1">{{ $offering->photo_caption }}</p>
                </div>
            @endif
        @else
            <i class="bi bi-image text-5xl text-gray-300"></i>
        @endif
    </div>

    {{-- --- Name, description and price --- --}}
    <div class="pt-4 px-4 pb-3 flex flex-col flex-grow">
        <h4 class="font-bold text-gray-800 text-sm line-clamp-2 leading-snug mb-1.5 group-hover:text-uco-orange-600 transition-colors" title="{{ $offering->name }}">
            {{ $offering->name }}
        </h4>

        <p class="text-xs text-gray-500 line-clamp-2 mb-3 leading-relaxed">
            {{ $offering->description }}
        </p>

        <div class="mt-auto flex flex-col items-start gap-1">
            <div>
                @if($offering->shows_price)
                    <span class="text-base font-extrabold text-[#f7931e]">
                        Rp {{ $offering->formatted_price }}
                    </span>
                @else
                    <span class="inline-flex items-center text-xs font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                        {{ $offering->price_text }}
                    </span>
                @endif
            </div>
            @if(is_numeric($offering->price) && $offering->price_type_label)
                <span class="inline-flex items-center text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded uppercase tracking-wider">
                    {{ $offering->price_type_label }}
                </span>
            @endif
        </div>
    </div>
</div>
