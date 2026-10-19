{{-- --- Business page top: breadcrumbs, logo, type and mode badges, edit and collab buttons --- --}}
<x-ui.breadcrumbs class="mb-6" :href="route('businesses.index', ['view' => 'entrepreneur'])" :current="$business->name" />

<x-business.profile-heading :name="$business->name" :logo-url="$business->logo_url">
    <x-slot:badges>
        <span class="badge badge-info">{{ $business->type_label }}</span>
        <x-business.mode-badge :mode="$business->offering_mode" />
    </x-slot:badges>

    <x-slot:actions>
        @auth
            <div class="flex flex-wrap items-center gap-2">
                @if ($canManageBusiness)
                    <a href="{{ route('businesses.edit', $business) }}" class="btn btn-secondary">
                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                        Edit business
                    </a>
                @endif

                @if ($business->public_owner && Auth::user()->canCollabWith($business->public_owner))
                    @if ($collabStatus === 'accepted')
                        <span class="badge badge-success text-sm"><i class="bi bi-person-check" aria-hidden="true"></i> Connected with the owner</span>
                    @elseif ($collabStatus === 'pending')
                        <span class="badge badge-warning text-sm"><i class="bi bi-clock-history" aria-hidden="true"></i> Collab request sent</span>
                    @else
                        <form action="{{ route('collabs.store', $business->public_owner) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-plus" aria-hidden="true"></i>
                                Collab with the owner
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        @endauth
    </x-slot:actions>
</x-business.profile-heading>
