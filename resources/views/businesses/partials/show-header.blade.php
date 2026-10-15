{{-- --- Business page top: breadcrumbs, logo, type and mode badges, edit and collab buttons --- --}}
<div class="mb-8 px-4 sm:px-0">
    {{-- --- Breadcrumbs --- --}}
    <nav class="mb-8 flex pt-4 text-sm font-medium sm:pt-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('businesses.index') }}?view=entrepreneur" class="text-gray-400 transition hover:text-uco-orange-500">Directory</a></li>
            <li class="flex items-center space-x-2">
                <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
                </svg>
                <span class="text-gray-900">{{ $business->name }}</span>
            </li>
        </ol>
    </nav>

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">

        <div class="flex flex-1 flex-row items-center gap-4 sm:items-start sm:gap-5">
            {{-- --- Business Logo --- --}}
            @if ($business->logo_url)
                <img src="{{ storage_image_url($business->logo_url, ['width' => 256, 'height' => 256, 'crop' => 'thumb', 'quality' => 'auto', 'fetch_format' => 'auto']) }}"
                     alt="{{ $business->name }} Logo" loading="lazy"
                     class="h-16 w-16 flex-shrink-0 rounded-sm object-contain sm:h-20 sm:w-20">
            @else
                <div
                     class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-sm bg-gradient-to-br from-soft-gray-50 to-soft-gray-100 sm:h-20 sm:w-20">
                    <i class="bi bi-briefcase text-2xl text-soft-gray-300 sm:text-3xl"></i>
                </div>
            @endif

            <div class="min-w-0 flex-1">
                <div class="mb-2 flex flex-wrap items-center gap-2">
                    <span class="{{ strtolower($business->type ?? '') === 'entrepreneur' ? 'bg-orange-100 text-orange-700' : 'bg-indigo-100 text-indigo-700' }} inline-flex items-center gap-1.5 rounded-xl px-3 py-1 text-xs font-semibold">
                        <i class="bi bi-person-workspace text-[10px]"></i>
                        {{ ucfirst($business->type ?? 'Entrepreneur') }}
                    </span>

                    <x-business.mode-badge :mode="$business->offering_mode" />
                </div>
                <h1 class="text-2xl font-bold leading-tight tracking-tight text-soft-gray-900 sm:text-3xl">
                    {{ $business->name }}</h1>
            </div>
        </div>
        @auth
            <div class="flex items-center gap-3">
                @if ($canManageBusiness)
                    <a href="{{ route('businesses.edit', $business) }}"
                       class="btn-uco btn-uco-secondary">
                        <i class="bi bi-pencil-square"></i>
                        <span class="hidden sm:inline">Edit Business</span>
                        <span class="sm:hidden">Edit</span>
                    </a>
                @endif

                @if ($business->user_id && Auth::id() !== $business->user_id)
                    @if ($collabStatus === 'accepted')
                        <button disabled class="btn-uco flex cursor-default items-center gap-2 border border-green-200 bg-green-50 text-green-700">
                            <i class="bi bi-person-check-fill"></i>
                            <span class="hidden sm:inline">Connected with Owner</span>
                            <span class="sm:hidden">Connected</span>
                        </button>
                    @elseif($collabStatus === 'pending')
                        <button disabled class="btn-uco flex cursor-default items-center gap-2 border border-orange-200 bg-orange-50 text-orange-700">
                            <i class="bi bi-clock-history"></i>
                            <span class="hidden sm:inline">Pending Collab</span>
                            <span class="sm:hidden">Pending</span>
                        </button>
                    @else
                        <form action="{{ route('collabs.store', $business->public_owner) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn-uco btn-uco-primary flex items-center gap-2 transition hover:scale-105">
                                <i class="bi bi-person-plus-fill"></i>
                                <span class="hidden sm:inline">Collab with Owner</span>
                                <span class="sm:hidden">Collab</span>
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        @endauth
    </div>
</div>
