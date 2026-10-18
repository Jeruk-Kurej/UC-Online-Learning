{{-- --- One product or service on the business page; the card opens the detail dialog (businessShowPage) --- --}}
@props(['business', 'offering', 'canManage'])

<article class="card flex h-full flex-col overflow-hidden !p-0">
    <button type="button" class="flex flex-1 flex-col text-left" aria-label="Show details of {{ $offering->name }}"
            @click="openDetailModal({
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
            })">
        <span class="relative flex aspect-square w-full items-center justify-center overflow-hidden border-b border-line bg-gray-50">
            <i class="bi bi-image text-4xl text-gray-400" aria-hidden="true"></i>
            @if ($offering->has_photo)
                <img src="{{ $offering->photo_url }}" alt="" data-thumb loading="lazy" class="absolute inset-0 h-full w-full bg-white object-contain" referrerpolicy="no-referrer">
            @endif
        </span>

        <span class="flex flex-1 flex-col gap-1 p-4">
            <span class="line-clamp-2 text-base font-semibold text-brand-900">{{ $offering->name }}</span>
            <span class="line-clamp-2 text-sm text-gray-600">{{ $offering->description }}</span>
            <span class="mt-auto pt-2">
                @if ($offering->shows_price)
                    <span class="text-lg font-semibold text-gray-900">Rp {{ $offering->formatted_price }}</span>
                @else
                    <span class="badge badge-neutral">{{ $offering->price_text }}</span>
                @endif
                @if (is_numeric($offering->price) && $offering->price_type_label)
                    <span class="badge badge-neutral ml-1">{{ $offering->price_type_label }}</span>
                @endif
            </span>
        </span>
    </button>

    @auth
        @if ($canManage)
            <div class="flex items-center gap-2 border-t border-line p-3">
                <a href="{{ route('businesses.' . $offering->type . 's.edit', [$business, $offering]) }}" class="btn btn-secondary btn-sm flex-1">Edit</a>
                <form action="{{ route('businesses.' . $offering->type . 's.destroy', [$business, $offering]) }}" method="POST" data-confirm="Delete this {{ $offering->type }}? This cannot be undone." class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm w-full">Delete</button>
                </form>
            </div>
        @endif
    @endauth
</article>
