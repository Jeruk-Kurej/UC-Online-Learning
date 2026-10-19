{{-- --- Users table (cards on phones) --- --}}
<div class="card overflow-x-auto !p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <x-admin.th>Name</x-admin.th>
                <x-admin.th>Email</x-admin.th>
                <x-admin.th>Student status</x-admin.th>
                <x-admin.th>Profile</x-admin.th>
                <x-admin.th>Home page</x-admin.th>
                <x-admin.th>Businesses</x-admin.th>
                <x-admin.th>Actions</x-admin.th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr data-user-row data-visible="{{ $user->is_visible ? '1' : '0' }}">
                    <td data-label="">
                        <div class="flex min-w-0 items-center gap-3 text-left">
                            <x-ui.thumb :src="$user->has_profile_photo ? $user->profile_photo_url : null" :name="$user->name" size="h-10 w-10" round class="text-base" />
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-gray-900">{{ $user->name }}</p>
                                @if ($user->major || $user->nis)
                                    <p class="truncate text-sm text-gray-600">{{ collect([$user->major, $user->nis])->filter()->implode(', ') }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td data-label="Email"><span class="break-all">{{ $user->email }}</span></td>
                    <td data-label="Student status"><span class="badge {{ $user->student_status === 'alumni' ? 'badge-info' : 'badge-neutral' }}">{{ ucfirst((string) $user->student_status) }}</span></td>
                    <td data-label="Profile"><x-admin.visibility-badge :visible="$user->is_visible" data-visibility-badge /></td>
                    <td data-label="Home page"><x-admin.featured-toggle :action="route('users.toggle-featured', $user)" :visible="$user->is_visible" :featured="$user->is_featured" data-row-toggle="featured" /></td>
                    <td data-label="Businesses">{{ $user->businesses_count }}</td>
                    <td data-label="Actions">
                        <div class="flex flex-wrap justify-end gap-2 md:flex-nowrap">
                            <a href="{{ route('users.show', $user) }}" class="btn btn-secondary btn-sm">View</a>
                            @if (auth()->user()?->isAdmin())
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary btn-sm">Edit</a>
                                @if (auth()->id() !== $user->id)
                                    <form action="{{ route('users.toggle-status', $user) }}" method="POST" data-row-toggle="status">
                                        @csrf
                                        <button type="submit" data-status-button class="btn btn-sm {{ $user->is_visible ? 'btn-danger-outline' : 'btn-secondary' }}">{{ $user->is_visible ? 'Hide' : 'Show' }}</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="!block px-6 py-12 text-center !text-base">
                        <p class="text-gray-700">{{ $hasActiveFilters ? 'No users match your filters.' : 'No users yet.' }}</p>
                        @if ($hasActiveFilters)
                            <button type="button" @click="resetFilters()" class="btn btn-secondary mt-4">Clear filters</button>
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-ajax mt-8">
    {{ $users->appends(request()->query())->links() }}
</div>
