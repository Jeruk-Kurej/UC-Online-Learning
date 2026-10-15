{{-- --- Business page top: breadcrumbs, logo, type and mode badges, edit and collab buttons --- --}}
<div class="mb-8 px-4 sm:px-0">
    <x-ui.breadcrumbs class="mb-8 pt-4 sm:pt-6" :href="route('businesses.index', ['view' => 'entrepreneur'])" :current="$business->name" />

    <x-business.profile-heading :name="$business->name" :logo-url="$business->logo_url" fallback-icon="bi-briefcase">
        <x-slot:badges>
            <span class="{{ strtolower($business->type ?? '') === 'entrepreneur' ? 'bg-orange-100 text-orange-700' : 'bg-indigo-100 text-indigo-700' }} inline-flex items-center gap-1.5 rounded-xl px-3 py-1 text-xs font-semibold">
                <i class="bi bi-person-workspace text-[10px]"></i>
                {{ ucfirst($business->type ?? 'Entrepreneur') }}
            </span>

            <x-business.mode-badge :mode="$business->offering_mode" />
        </x-slot:badges>

        <x-slot:actions>
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
        </x-slot:actions>
    </x-business.profile-heading>
</div>
