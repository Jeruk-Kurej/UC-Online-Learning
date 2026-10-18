{{-- --- Testimonies an admin featured --- --}}
<x-ui.section title="Stories from the community" lead="What students and alumni say about their time at UC Online Learning.">
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($testimonies as $student)
            <figure class="card flex h-full flex-col" x-data="{ open: false, clamped: false }" x-init="clamped = $refs.quote.scrollHeight > $refs.quote.clientHeight">
                <blockquote x-ref="quote" :class="open ? '' : 'line-clamp-6'" class="text-base text-gray-800">&ldquo;{{ $student->testimony }}&rdquo;</blockquote>
                <button type="button" x-show="clamped" x-cloak @click="open = !open" :aria-expanded="open" class="text-link mt-2 self-start text-sm" x-text="open ? 'Show less' : 'Read more'"></button>

                <figcaption class="mt-auto pt-4">
                    <div class="flex items-center gap-3 border-t border-line pt-4">
                        <x-ui.thumb :src="$student->profile_photo_url" :name="$student->name" size="h-12 w-12" round class="text-lg" />
                        <div class="min-w-0">
                            <a href="{{ route('users.show', $student) }}" class="block truncate text-base font-semibold text-brand-900 hover:underline">{{ $student->full_titled_name }}</a>
                            @if ($student->current_status)
                                <p class="truncate text-sm text-gray-600">{{ $student->current_status }}</p>
                            @endif
                        </div>
                    </div>
                </figcaption>
            </figure>
        @empty
            <x-ui.empty-state class="col-span-full" title="No stories yet">Testimonies picked by the admin team will show here.</x-ui.empty-state>
        @endforelse
    </div>
</x-ui.section>
