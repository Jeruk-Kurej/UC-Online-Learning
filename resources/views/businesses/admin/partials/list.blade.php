<div class="card overflow-x-auto !p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <x-admin.th>{{ $viewType === 'intrapreneur' ? 'Company' : 'Business' }}</x-admin.th>
                <x-admin.th>Owner</x-admin.th>
                <x-admin.th>{{ $viewType === 'intrapreneur' ? 'Job title' : 'Location' }}</x-admin.th>
                <x-admin.th>Status</x-admin.th>
                <x-admin.th>Listing</x-admin.th>
                @unless ($viewType === 'intrapreneur')
                    <x-admin.th>Home page</x-admin.th>
                @endunless
                <x-admin.th>Actions</x-admin.th>
            </tr>
        </thead>
        <tbody>
            @forelse ($businesses as $b)
                <tr>
                    <td data-label="">
                        <div class="flex min-w-0 items-center gap-3 text-left">
                            <x-ui.thumb :src="$b->logo_url" :name="$b->name" size="h-10 w-10" class="text-base" />
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-gray-900">{{ $b->name }}</p>
                                @if ($viewType === 'intrapreneur' && $b->company_scale)
                                    <p class="truncate text-sm text-gray-600">{{ $b->company_scale }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td data-label="Owner">{{ $b->user?->name ?? 'Unknown' }}</td>
                    <td data-label="{{ $viewType === 'intrapreneur' ? 'Job title' : 'Location' }}">
                        @if ($viewType === 'intrapreneur')
                            {{ $b->position ?: 'Not set' }}
                        @else
                            {{ collect([$b->city, $b->province])->filter()->implode(', ') ?:'Not set' }}
                        @endif
                    </td>
                    <td data-label="Status"><x-business.status-pill :item="$b" /></td>
                    <td data-label="Listing"><x-admin.visibility-badge :visible="$b->is_visible" /></td>
                    @unless ($viewType === 'intrapreneur')
                        <td data-label="Home page"><x-admin.featured-toggle :action="route('businesses.toggle-featured', $b)" :visible="$b->is_visible" :featured="$b->is_featured" class="toggle-featured-form" /></td>
                    @endunless
                    <td data-label="Actions">
                        <div class="flex flex-wrap justify-end gap-2 md:flex-nowrap">
                            <a href="{{ $viewType === 'intrapreneur' ? route('intrapreneurs.show', $b) : route('businesses.show', $b) }}" class="btn btn-secondary btn-sm">View</a>
                            <button type="button" class="btn btn-secondary btn-sm" data-status-modal="{{ json_encode(['name' => $b->name, 'status' => $b->status, 'reason' => $b->rejection_reason, 'url' => $viewType === 'intrapreneur' ? route('intrapreneurs.update-status', $b, false) : route('businesses.update-status', $b, false)]) }}">Change status</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="!block px-6 py-12 text-center !text-base">
                        <p class="text-gray-700">No {{ $viewType === 'intrapreneur' ? 'work profiles' : 'businesses' }} match your search.</p>
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
