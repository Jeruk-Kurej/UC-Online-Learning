<div class="reveal-on-scroll overflow-x-auto rounded-xl border bg-white shadow-sm">
    <table class="w-full text-left">
        <thead class="border-b bg-gray-50">
            <tr>
                <x-admin.th>
                    {{ ($viewType ?? 'entrepreneur') === 'intrapreneur' ? 'Company' : 'Business' }}
                </x-admin.th>
                <x-admin.th>Owner</x-admin.th>
                <x-admin.th>
                    {{ ($viewType ?? 'entrepreneur') === 'intrapreneur' ? 'Position' : 'Location' }}
                </x-admin.th>
                <x-admin.th class="whitespace-nowrap">Status</x-admin.th>
                <x-admin.th class="whitespace-nowrap text-center">Visible</x-admin.th>
                @if (($viewType ?? 'entrepreneur') === 'entrepreneur')
                    <x-admin.th class="whitespace-nowrap text-center">Featured</x-admin.th>
                @endif
                <x-admin.th class="w-px whitespace-nowrap text-right">Actions</x-admin.th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($businesses as $b)
                <tr class="transition hover:bg-orange-50/30">
                    {{-- --- Business / Company name + logo --- --}}
                    <td class="max-w-[220px] px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center overflow-hidden rounded-sm">
                                @if ($b->logo_url)
                                    <img src="{{ $b->logo_url }}" class="h-full w-full object-contain">
                                @else
                                    <div class="uco-initials-bg flex h-full w-full items-center justify-center text-xs font-black text-white">
                                        {{ strtoupper(substr($b->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold leading-tight text-gray-900">{{ $b->name }}</p>
                                @if (($viewType ?? 'entrepreneur') === 'intrapreneur' && $b->company_scale)
                                    <p class="truncate text-[10px] text-gray-400">{{ $b->company_scale }}</p>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- --- Owner --- --}}
                    <td class="max-w-[180px] px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="h-6 w-6 flex-shrink-0 overflow-hidden rounded-sm">
                                @if ($b->user?->has_profile_photo)
                                    <img src="{{ $b->user->profile_photo_url }}" class="h-full w-full object-contain">
                                @else
                                    <div class="uco-initials-bg flex h-full w-full items-center justify-center text-[10px] font-black text-white">
                                        {{ strtoupper(substr($b->user?->name ?? 'Unknown', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <span class="truncate text-xs font-bold text-gray-700">{{ $b->user?->name ?? 'Unknown' }}</span>
                        </div>
                    </td>

                    {{-- --- Location (entrepreneur) / Position (intrapreneur) --- --}}
                    <td class="max-w-[160px] px-4 py-3">
                        @if (($viewType ?? 'entrepreneur') === 'intrapreneur')
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-gray-700">{{ $b->position }}</p>
                                @if ($b->level_position)
                                    <p class="truncate text-[10px] text-gray-400">{{ $b->level_position }}</p>
                                @endif
                            </div>
                        @else
                            <span class="block truncate text-sm text-gray-500" title="{{ $b->city }}, {{ $b->province }}">{{ $b->city }}, {{ $b->province }}</span>
                        @endif
                    </td>

                    {{-- --- Approval Status --- --}}
                    <td class="px-4 py-3">
                        <x-admin.status-badge :item="$b" />
                    </td>

                    {{-- --- Visible dot --- --}}
                    <td class="px-4 py-3 text-center">
                        <x-admin.visibility-dot :visible="$b->is_visible" class="inline-block" />
                    </td>

                    {{-- --- Featured (entrepreneur only) --- --}}
                    @if (($viewType ?? 'entrepreneur') === 'entrepreneur')
                        <td class="px-4 py-3 text-center">
                            <x-admin.featured-toggle :action="route('businesses.toggle-featured', $b)" :visible="$b->is_visible" :featured="$b->is_featured"
                                                     class="toggle-featured-form inline-block" tip-class="tooltip-text" />
                        </td>
                    @endif

                    {{-- --- Actions --- --}}
                    <td class="w-px whitespace-nowrap px-4 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            @if (($viewType ?? 'entrepreneur') === 'intrapreneur')
                                {{-- --- View intrapreneur profile --- --}}
                                <x-admin.icon-action :href="route('intrapreneurs.show', $b)" icon="bi-eye-fill" tip="View Profile" />
                                {{-- --- Change status (modal) --- --}}
                                <x-admin.icon-action variant="secondary" icon="bi-pencil-square" tip="Change Status" tip-align="right" arrow-class="mr-3"
                                                     data-status-modal="{{ json_encode(['id' => $b->id, 'name' => $b->name, 'status' => $b->status, 'reason' => $b->rejection_reason, 'type' => 'intrapreneur']) }}" />
                            @else
                                {{-- --- View entrepreneur business --- --}}
                                <x-admin.icon-action :href="route('businesses.show', $b)" icon="bi-eye-fill" tip="View Details" />
                                {{-- --- Change status (modal) --- --}}
                                <x-admin.icon-action variant="secondary" icon="bi-pencil-square" tip="Change Status" tip-align="right" arrow-class="mr-3"
                                                     data-status-modal="{{ json_encode(['id' => $b->id, 'name' => $b->name, 'status' => $b->status, 'reason' => $b->rejection_reason, 'type' => 'entrepreneur']) }}" />
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center">
                        <p class="italic text-gray-400">
                            No {{ ($viewType ?? 'entrepreneur') === 'intrapreneur' ? 'career profiles' : 'businesses' }} found matching the criteria.
                        </p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@if ($businesses->hasPages())
    <div class="pagination-ajax mt-8">
        {{ $businesses->links() }}
    </div>
@endif
