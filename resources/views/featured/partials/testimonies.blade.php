{{-- --- Testimonies an admin featured: all equal, in an order that changes daily --- --}}
<section class="border-y border-brand-100 bg-brand-50" aria-labelledby="stories-title">
    <div class="page space-y-8 !py-12">
        <div>
            <h2 id="stories-title" class="section-heading">Stories from the community</h2>
            <p class="mt-1 text-base text-gray-700">What students and alumni say about their time at UC Online Learning.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($testimonies as $student)
                <figure class="card flex h-full flex-col !border-brand-200" x-data="{ open: false, clamped: false }" x-init="clamped = $refs.quote.scrollHeight > $refs.quote.clientHeight">
                    <blockquote x-ref="quote" :class="open ? '' : 'line-clamp-6'" class="font-display text-lg font-medium leading-relaxed text-brand-900">&ldquo;{{ $student->testimony }}&rdquo;</blockquote>
                    <button type="button" x-show="clamped" x-cloak @click="open = !open" :aria-expanded="open" class="text-link mt-2 self-start text-sm" x-text="open ? 'Show less' : 'Read more'"></button>

                    <figcaption class="mt-auto pt-5">
                        <div class="flex items-center gap-3 border-t border-line pt-4">
                            <x-ui.thumb :src="$student->has_profile_photo ? $student->profile_photo_url : null" :name="$student->name" size="h-14 w-14" round class="text-xl" />
                            <div class="min-w-0">
                                <a href="{{ route('users.show', $student) }}" class="block truncate text-base font-semibold text-brand-900 hover:underline">{{ $student->full_titled_name }}</a>
                                @if ($student->current_status)
                                    <p class="truncate text-sm text-gray-700">{{ $student->current_status }}</p>
                                @endif
                            </div>
                        </div>
                    </figcaption>
                </figure>
            @empty
                <x-ui.empty-state class="col-span-full" title="No stories yet">Testimonies picked by the admin team will show here.</x-ui.empty-state>
            @endforelse
        </div>
    </div>
</section>
