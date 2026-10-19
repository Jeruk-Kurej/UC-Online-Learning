{{-- --- Testimonies an admin featured: the first one large on an orange tint, the others below it --- --}}
<section class="border-y border-brand-100 bg-brand-50" aria-labelledby="stories-title">
    <div class="page space-y-8 !py-12">
        <div>
            <h2 id="stories-title" class="section-heading">Stories from the community</h2>
            <p class="mt-1 text-base text-gray-700">What students and alumni say about their time at UC Online Learning.</p>
        </div>

        @foreach ($testimonies->take(1) as $student)
            <figure class="card flex flex-col gap-6 !border-brand-200 sm:!p-8 lg:flex-row lg:items-center" x-data="{ open: false, clamped: false }" x-init="clamped = $refs.quote.scrollHeight > $refs.quote.clientHeight">
                <div class="lg:w-1/3">
                    <span class="photo-frame aspect-square w-full max-w-[16rem] text-6xl font-semibold text-brand-800">
                        <span aria-hidden="true">{{ $student->initial }}</span>
                        @if ($student->has_profile_photo)
                            <img src="{{ $student->profile_photo_url }}" alt="" data-thumb loading="lazy" class="absolute inset-0 h-full w-full bg-white object-cover object-top" referrerpolicy="no-referrer">
                        @endif
                    </span>
                </div>
                <div class="min-w-0 flex-1">
                    <blockquote x-ref="quote" :class="open ? '' : 'line-clamp-6'" class="font-display text-2xl font-medium leading-snug text-brand-900">&ldquo;{{ $student->testimony }}&rdquo;</blockquote>
                    <button type="button" x-show="clamped" x-cloak @click="open = !open" :aria-expanded="open" class="text-link mt-2 text-sm" x-text="open ? 'Show less' : 'Read more'"></button>
                    <figcaption class="mt-5">
                        <a href="{{ route('users.show', $student) }}" class="text-lg font-semibold text-brand-900 hover:underline">{{ $student->full_titled_name }}</a>
                        @if ($student->current_status)
                            <p class="text-base text-gray-700">{{ $student->current_status }}</p>
                        @endif
                    </figcaption>
                </div>
            </figure>
        @endforeach

        @if ($testimonies->count() > 1)
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($testimonies->slice(1) as $student)
                    <figure class="card flex h-full flex-col" x-data="{ open: false, clamped: false }" x-init="clamped = $refs.quote.scrollHeight > $refs.quote.clientHeight">
                        <blockquote x-ref="quote" :class="open ? '' : 'line-clamp-5'" class="text-base text-gray-800">&ldquo;{{ $student->testimony }}&rdquo;</blockquote>
                        <button type="button" x-show="clamped" x-cloak @click="open = !open" :aria-expanded="open" class="text-link mt-2 self-start text-sm" x-text="open ? 'Show less' : 'Read more'"></button>

                        <figcaption class="mt-auto pt-4">
                            <div class="flex items-center gap-3 border-t border-line pt-4">
                                <x-ui.thumb :src="$student->has_profile_photo ? $student->profile_photo_url : null" :name="$student->name" size="h-12 w-12" round class="text-lg" />
                                <div class="min-w-0">
                                    <a href="{{ route('users.show', $student) }}" class="block truncate text-base font-semibold text-brand-900 hover:underline">{{ $student->full_titled_name }}</a>
                                    @if ($student->current_status)
                                        <p class="truncate text-sm text-gray-600">{{ $student->current_status }}</p>
                                    @endif
                                </div>
                            </div>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        @endif

        @if ($testimonies->isEmpty())
            <x-ui.empty-state title="No stories yet">Testimonies picked by the admin team will show here.</x-ui.empty-state>
        @endif
    </div>
</section>
