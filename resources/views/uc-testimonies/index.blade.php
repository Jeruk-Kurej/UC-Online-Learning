<x-app-layout>
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 space-y-8 pb-8">
        <section class="relative overflow-hidden rounded-[2.5rem] border border-uco-orange-100 bg-white px-6 py-8 shadow-sm md:px-8 md:py-10 mb-8 reveal-on-scroll">
            <div class="uco-hero-mesh"></div>
            <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="space-y-2 reveal-on-scroll">
                    <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-uco-orange-700">
                        UCO Community Voices
                    </span>
                    <h1 class="text-3xl font-extrabold text-soft-gray-900 md:text-4xl">UC Online Learning Testimonies</h1>
                    <p class="max-w-2xl text-sm leading-relaxed text-soft-gray-600 md:text-base">
                        Share your learning experience and discover stories from students and alumni in our UCO network.
                    </p>
                </div>
                <div class="reveal-on-scroll md:text-right" style="transition-delay: 90ms;">
                    <span class="inline-flex items-center gap-2 rounded-full border border-uco-yellow-300 bg-uco-yellow-50 px-4 py-2 text-xs font-semibold text-uco-yellow-800">
                        <i class="bi bi-chat-quote"></i>
                        {{ $testimonies->total() }} approved testimony(s)
                    </span>
                </div>
            </div>
        </section>


        @auth
            @if(!auth()->user()->isAdmin())
            <section class="rounded-2xl border border-soft-gray-200 bg-white p-5 shadow-sm md:p-6 reveal-on-scroll">
                <div class="mb-5 border-b border-soft-gray-200 pb-4">
                    <h2 class="text-xl font-bold text-soft-gray-900">Write a Testimony</h2>
                    <p class="mt-1 text-sm text-soft-gray-600">Tell us how UC Online Learning helped your journey.</p>
                </div>

                <form action="{{ route('uc-testimonies.store') }}" method="POST" class="space-y-5" x-data="{ rating: {{ old('rating', 0) }}, hoverRating: 0, setRating(value) { this.rating = value; } }">
                    @csrf

                    <div class="flex items-center gap-3 rounded-xl border border-soft-gray-200 bg-soft-gray-50 p-3.5">
                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center overflow-hidden rounded-full bg-uco-orange-100 text-sm font-bold text-uco-orange-700">
                            @if(auth()->user()->profile_photo_url)
                                <img src="{{ storage_image_url(auth()->user()->profile_photo_url, 'profile_thumb') }}" alt="{{ auth()->user()->name }}" class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="mb-0.5 text-xs text-soft-gray-500">You are submitting this testimony as:</p>
                            <p class="truncate text-sm font-semibold text-soft-gray-900">{{ auth()->user()->name }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-soft-gray-700">Rating</label>
                        <input type="hidden" name="rating" x-model="rating">
                        <div class="flex flex-wrap items-center gap-2">
                            <template x-for="star in 5" :key="star">
                                <button type="button"
                                        @click="setRating(star)"
                                        @mouseenter="hoverRating = star"
                                        @mouseleave="hoverRating = 0"
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border transition focus:outline-none focus:ring-2 focus:ring-uco-yellow-200"
                                        :class="(hoverRating >= star || (hoverRating === 0 && rating >= star)) ? 'border-uco-yellow-400 bg-uco-yellow-50 text-uco-yellow-600' : 'border-soft-gray-300 bg-white text-soft-gray-400'"
                                        :aria-label="`Rate ${star} star`">
                                    <i class="bi bi-star-fill text-lg"></i>
                                </button>
                            </template>
                            <span class="ml-1 text-sm text-soft-gray-600" x-show="rating > 0" x-text="rating + ' star' + (rating > 1 ? 's' : '')"></span>
                        </div>
                        @error('rating')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-soft-gray-700">Your Testimony</label>
                        <textarea name="content"
                                  rows="5"
                                  class="block w-full rounded-xl border border-soft-gray-300 px-4 py-3 text-sm text-soft-gray-800 shadow-sm focus:border-uco-orange-300 focus:ring-2 focus:ring-uco-orange-200"
                                  placeholder="Share your learning experience, what helped you most, or what changed after joining..."
                                  required>{{ old('content') }}</textarea>
                        <p class="mt-1.5 text-xs text-soft-gray-500">Minimum 20 characters and 4 words required to be eligible for the featured homepage.</p>
                        @error('content')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap items-center gap-3 border-t border-soft-gray-200 pt-2">
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-uco-orange-500 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-uco-orange-200 transition hover:-translate-y-0.5 hover:bg-uco-orange-600">
                            Submit Testimony
                            <i class="bi bi-send"></i>
                        </button>
                        <p class="text-xs text-soft-gray-500">Submissions are AI-moderated before publication.</p>
                    </div>
                </form>
            </section>
            @else
            <section class="rounded-2xl border border-uco-yellow-200 bg-uco-yellow-50 px-5 py-4 text-sm text-uco-yellow-900 shadow-sm reveal-on-scroll">
                <div class="flex items-start gap-2">
                    <i class="bi bi-info-circle mt-0.5"></i>
                    <p><strong>Note:</strong> Administrators cannot submit testimonies. Please use a non-admin account to post.</p>
                </div>
            </section>
            @endif
        @else
            <section class="rounded-2xl border border-soft-gray-200 bg-white p-5 shadow-sm md:p-6 reveal-on-scroll">
                <div class="mb-5 border-b border-soft-gray-200 pb-4">
                    <h2 class="text-xl font-bold text-soft-gray-900">Write a Testimony</h2>
                    <p class="mt-1 text-sm text-soft-gray-600">Sign in to share your learning experience.</p>
                </div>

                <div class="rounded-xl border border-dashed border-soft-gray-300 bg-soft-gray-50 p-5 text-sm text-soft-gray-600">
                    <p class="font-medium text-soft-gray-800">You can browse testimonies publicly, but posting requires an account.</p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl bg-uco-orange-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-uco-orange-600">Log in</a>
                    </div>
                </div>
            </section>
        @endauth

        <section class="space-y-4">
            <div class="flex items-end justify-between gap-4 border-b border-soft-gray-200 pb-4 reveal-on-scroll">
                <div>
                    <h2 class="text-2xl font-bold text-soft-gray-900">Approved Testimonies</h2>
                    <p class="mt-1 text-sm text-soft-gray-600">Stories that passed moderation and are visible to the public.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                @forelse ($testimonies as $testimony)
                    <article class="rounded-2xl border border-soft-gray-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:border-uco-orange-200 hover:shadow-md reveal-on-scroll">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-uco-orange-100 text-sm font-bold text-uco-orange-700">
                                    @if($testimony->profile_photo_url)
                                        <img src="{{ storage_image_url($testimony->profile_photo_url, 'profile_thumb') }}" alt="{{ $testimony->name }}" class="h-full w-full object-cover">
                                    @else
                                        {{ strtoupper(substr($testimony->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="text-base font-bold text-soft-gray-900">{{ $testimony->name }}</p>
                                    <p class="text-xs text-soft-gray-500">{{ optional($testimony->submitted_at)->format('M d, Y') ?? optional($testimony->created_at)->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-0.5 text-uco-yellow-500">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $testimony->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            </div>
                        </div>

                        <p class="mt-3 text-sm leading-relaxed text-soft-gray-700 md:text-base">“{{ $testimony->testimony }}”</p>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-soft-gray-300 bg-soft-gray-50 p-8 text-center text-soft-gray-600 reveal-on-scroll">
                        <div class="mb-2 text-uco-orange-500"><i class="bi bi-chat-left-text text-2xl"></i></div>
                        <p class="font-semibold">No approved testimonies yet.</p>
                        <p class="mt-1 text-sm">Be the first to share your experience with UC Online Learning.</p>
                    </div>
                @endforelse
            </div>

            <div class="rounded-2xl border border-soft-gray-200 bg-white px-4 py-4 shadow-sm reveal-on-scroll">
                {{ $testimonies->links() }}
            </div>
        </section>
    </div>
</x-app-layout>
