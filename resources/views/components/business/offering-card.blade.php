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
        type: {{ Js::from($offering->type) }}
     })"
     class="group relative flex h-full cursor-pointer flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white transition-all duration-300 hover:border-orange-200 hover:shadow-xl">

    {{-- --- Edit / delete for people who manage the business --- --}}
    @auth
        @if ($canManage)
            <div class="absolute right-3 top-3 z-10 flex items-center gap-1.5" @click.stop>
                <a href="{{ route('businesses.' . $offering->type . 's.edit', [$business, $offering]) }}"
                   class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-gray-100 bg-white/95 text-gray-700 shadow-md transition-all duration-300 hover:bg-white hover:text-uco-orange-600"
                   title="Edit {{ ucfirst($offering->type) }}">
                    <i class="bi bi-pencil-square"></i>
                </a>
                <form action="{{ route('businesses.' . $offering->type . 's.destroy', [$business, $offering]) }}"
                      method="POST"
                      data-confirm="Are you sure you want to delete this {{ $offering->type }}?"
                      class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-gray-100 bg-white/95 text-red-600 shadow-md transition-all duration-300 hover:bg-red-50 hover:text-red-700"
                            title="Delete {{ ucfirst($offering->type) }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        @endif
    @endauth

    {{-- --- Photo --- --}}
    <div class="relative flex aspect-square w-full flex-shrink-0 items-center justify-center overflow-hidden">
        @if ($offering->has_photo)
            <x-premium-image :src="$offering->photo_url" :alt="$offering->name" class="h-full w-full transition-transform duration-500 group-hover:scale-105" />
            @if ($offering->photo_caption)
                <div class="absolute bottom-0 left-0 right-0 translate-y-full transform bg-black/60 p-2 backdrop-blur-sm transition-transform duration-300 group-hover:translate-y-0">
                    <p class="line-clamp-1 text-center text-[10px] font-medium text-white">{{ $offering->photo_caption }}</p>
                </div>
            @endif
        @else
            <i class="bi bi-image text-5xl text-gray-300"></i>
        @endif
    </div>

    {{-- --- Name, description and price --- --}}
    <div class="flex flex-grow flex-col px-4 pb-3 pt-4">
        <h4 class="mb-1.5 line-clamp-2 text-sm font-bold leading-snug text-gray-800 transition-colors group-hover:text-uco-orange-600" title="{{ $offering->name }}">
            {{ $offering->name }}
        </h4>

        <p class="mb-3 line-clamp-2 text-xs leading-relaxed text-gray-500">
            {{ $offering->description }}
        </p>

        <div class="mt-auto flex flex-col items-start gap-1">
            <div>
                @if ($offering->shows_price)
                    <span class="text-base font-extrabold text-[#f7931e]">
                        Rp {{ $offering->formatted_price }}
                    </span>
                @else
                    <span class="inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-xs font-bold text-gray-500">
                        {{ $offering->price_text }}
                    </span>
                @endif
            </div>
            @if (is_numeric($offering->price) && $offering->price_type_label)
                <span class="inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                    {{ $offering->price_type_label }}
                </span>
            @endif
        </div>
    </div>
</div>
