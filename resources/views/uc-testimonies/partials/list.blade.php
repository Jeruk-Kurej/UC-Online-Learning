<div class="grid grid-cols-1 justify-items-center gap-6 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
    @forelse($users as $user)
        <div class="relative flex w-full max-w-[320px] flex-col overflow-hidden rounded-[20px] border border-gray-100 bg-white shadow-[0_20px_25px_-5px_rgba(0,0,0,0.05),0_10px_10px_-5px_rgba(0,0,0,0.01)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_30px_50px_rgba(0,0,0,0.08)]"
             id="testimony-card-{{ $user->id }}">

            {{-- --- Status Badge (Top Left) --- --}}
            <div class="absolute left-4 top-4 z-20">
                <span class="{{ $user->is_visible ? 'bg-emerald-500/90 text-white' : 'bg-red-500/90 text-white' }} inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold shadow-sm backdrop-blur-md">
                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                    {{ $user->is_visible ? 'Active' : 'Inactive' }}
                </span>
            </div>

            {{-- --- Featured Star Button (Top Right) --- --}}
            <div class="absolute right-4 top-4 z-20">
                <button type="button"
                        data-user-id="{{ $user->id }}"
                        data-featured="{{ $user->is_featured_testimony ? '1' : '0' }}"
                        data-testimony-featured-toggle="{{ route('uc-testimonies.toggle-featured', $user, false) }}"
                        title="{{ !$user->is_visible ? 'User must be visible to be featured' : ($user->is_featured_testimony ? 'Remove from featured' : 'Add to featured') }}"
                        {{ !$user->is_visible ? 'disabled' : '' }}
                        class="{{ !$user->is_visible ? 'bg-gray-200/90 border-gray-300 text-gray-600 cursor-not-allowed' : ($user->is_featured_testimony ? 'bg-[#ff8a00] border-[#ff8a00] text-white hover:bg-orange-600' : 'bg-white/90 border-gray-200 text-gray-600 hover:text-[#ff8a00] hover:border-[#ff8a00]') }} inline-flex h-10 w-10 items-center justify-center rounded-full border shadow-md transition-all duration-300 focus:outline-none">
                    <i class="bi {{ $user->is_featured_testimony ? 'bi-star-fill' : 'bi-star' }} text-lg leading-none"></i>
                </button>
            </div>

            {{-- --- Top Section: Image & Info --- --}}
            <div class="relative h-[300px] w-full flex-shrink-0">
                @if ($user->profile_photo_url)
                    <x-premium-image :src="$user->profile_photo_url" :alt="$user->name" class="h-full w-full" />
                @else
                    <div class="uco-initials-bg flex h-full w-full items-center justify-center text-4xl font-bold text-white">
                        {{ $user->initial }}
                    </div>
                @endif

                {{-- --- Overlay Gradient --- --}}
                <div class="testimony-shade"></div>

                {{-- --- Text Content on Image --- --}}
                <div class="absolute bottom-[45px] left-5 right-5 text-white">
                    <h3 class="mb-0.5 text-base font-bold leading-[1.2]">{{ $user->name }}</h3>
                    <p class="mb-0.5 text-xs font-semibold text-[#cbd5e1]">
                        {{ $user->current_status ?? 'Member' }} at UCO Community
                    </p>
                    <p class="text-xs font-bold text-[#ff8a00]">
                        {{ $user->major ?? 'Student' }}
                    </p>
                </div>
            </div>

            {{-- --- Bottom Section: Testimony content --- --}}
            <div class="relative flex flex-grow items-center justify-center rounded-b-[20px] bg-white px-5 pb-[25px] pt-[35px] text-center">
                {{-- --- Quote Icon --- --}}
                <div class="absolute -top-5 left-1/2 z-10 flex h-10 w-10 -translate-x-1/2 items-center justify-center rounded-xl bg-uco-orange-500 text-white shadow-[0_10px_15px_-3px_rgba(247,147,30,0.3)]">
                    <i class="bi bi-quote text-base"></i>
                </div>

                <p class="m-0 text-xs font-semibold italic leading-[1.6] text-[#334155]">
                    “{{ Str::limit($user->testimony, 200) }}”
                </p>
            </div>
        </div>
    @empty
        <div class="col-span-full py-16 text-center">
            <p class="italic text-gray-600">No testimonies found.</p>
        </div>
    @endforelse
</div>

<div class="pagination-ajax mt-8 w-full">
    {{ $users->appends(request()->query())->links() }}
</div>
