{{-- --- Profile activities: photo grid that opens in the page viewer, with the caption underneath --- --}}
@if (count($user->activity_gallery_urls) > 0)
    <section class="card">
        <h2 class="card-title">Activities</h2>
        <ul class="mt-4 grid grid-cols-2 gap-3">
            @foreach ($user->activity_gallery_urls as $url)
                <li>
                    <button type="button" @click="open(@js($url))" class="block aspect-[4/3] w-full overflow-hidden rounded-md border border-line bg-gray-50 hover:border-brand-500" aria-label="View photo larger">
                        <img src="{{ $url }}" alt="" data-thumb loading="lazy" class="h-full w-full object-contain" referrerpolicy="no-referrer">
                    </button>
                </li>
            @endforeach
        </ul>
        @if ($user->activities_caption)
            <p class="mt-3 text-base text-gray-700">{{ $user->activities_caption }}</p>
        @endif
    </section>
@endif
