{{-- --- Testimonies an admin featured, as alumni cards on a light grey band: all equal, in an order that changes daily --- --}}
<section class="bg-mist" aria-labelledby="stories-title">
    <div class="page space-y-6 !py-12">
        <div>
            <h2 id="stories-title" class="section-heading">Stories <span class="text-brand-700">from our community</span></h2>
            <p class="mt-3 text-base text-gray-700">What students and alumni say about their time at UC Online Learning.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($testimonies as $student)
                <figure class="flex h-full flex-col rounded-2xl bg-white p-5" x-data="{ open: false, clamped: false }" x-init="clamped = $refs.quote.scrollHeight > $refs.quote.clientHeight">
                    <figcaption class="flex items-center gap-4">
                        <span class="photo-frame h-20 w-20 !rounded-xl text-3xl font-semibold text-brand-800">
                            <span aria-hidden="true">{{ $student->initial }}</span>
                            @if ($student->has_profile_photo)
                                <img src="{{ $student->profile_photo_url }}" alt="" data-thumb loading="lazy" class="absolute inset-0 h-full w-full object-cover object-top" referrerpolicy="no-referrer">
                            @endif
                        </span>
                        <span class="min-w-0">
                            <a href="{{ route('users.show', $student) }}" class="block truncate text-lg font-semibold text-brand-700 hover:underline">{{ $student->full_titled_name }}</a>
                            @if ($student->current_status)
                                <span class="block truncate text-sm text-gray-700">{{ $student->current_status }}</span>
                            @endif
                        </span>
                    </figcaption>

                    <blockquote x-ref="quote" :class="open ? '' : 'line-clamp-5'" class="mt-4 text-base font-semibold leading-relaxed text-ink">&ldquo;{{ $student->testimony }}&rdquo;</blockquote>
                    <button type="button" x-show="clamped" x-cloak @click="open = !open" :aria-expanded="open" class="text-link mt-2 self-start text-sm" x-text="open ? 'Show less' : 'Read more'"></button>
                </figure>
            @empty
                <x-ui.empty-state class="col-span-full" title="No stories yet">Testimonies picked by the admin team will show here.</x-ui.empty-state>
            @endforelse
        </div>
    </div>
</section>
