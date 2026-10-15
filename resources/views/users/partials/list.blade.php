{{-- --- Users Table --- --}}
<div class="reveal-on-scroll overflow-x-auto rounded-xl border bg-white shadow-sm delay-[350ms]">
    <table class="w-full text-left">
        <thead class="border-b bg-gray-50">
            <tr>
                <x-admin.th>Name</x-admin.th>
                <x-admin.th>Email</x-admin.th>
                <x-admin.th class="whitespace-nowrap">Status</x-admin.th>
                <x-admin.th>Major</x-admin.th>
                <x-admin.th class="whitespace-nowrap text-center">Flags</x-admin.th>
                <x-admin.th class="whitespace-nowrap text-center">Biz</x-admin.th>
                <x-admin.th class="w-px whitespace-nowrap text-right">Actions</x-admin.th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($users as $user)
                <tr class="transition hover:bg-orange-50/30">

                    {{-- --- Name + Avatar --- --}}
                    <td class="max-w-[180px] px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="h-8 w-8 flex-shrink-0 overflow-hidden rounded-sm">
                                @if ($user->has_profile_photo)
                                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="h-full w-full object-contain">
                                @else
                                    <div class="uco-initials-bg flex h-full w-full items-center justify-center text-xs font-black text-white">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold leading-tight text-gray-900">{{ $user->name }}</p>
                                @if ($user->nis)
                                    <p class="text-[10px] font-medium text-gray-400">{{ $user->nis }}</p>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- --- Email (truncated) --- --}}
                    <td class="max-w-[180px] px-4 py-3">
                        <span class="block truncate text-sm text-gray-500" title="{{ $user->email }}">{{ $user->email }}</span>
                    </td>

                    {{-- --- Student Status Badge --- --}}
                    <td class="px-4 py-3">
                        <span class="{{ $user->student_status === 'alumni' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} rounded-md px-2 py-1 text-[10px] font-bold uppercase">
                            {{ $user->student_status }}
                        </span>
                    </td>

                    {{-- --- Major (truncated) --- --}}
                    <td class="max-w-[120px] px-4 py-3">
                        <span class="block truncate text-sm text-gray-500" title="{{ $user->major ?: '-' }}">{{ $user->major ?: '-' }}</span>
                    </td>

                    {{-- --- Flags: Visible dot + Featured star (combined column) --- --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">
                            <x-admin.visibility-dot :visible="$user->is_visible" />
                            <x-admin.featured-toggle :action="route('users.toggle-featured', $user)" :visible="$user->is_visible" :featured="$user->is_featured" />
                        </div>
                    </td>

                    {{-- --- Businesses count --- --}}
                    <td class="px-4 py-3 text-center">
                        <span class="text-sm font-bold text-gray-700">{{ $user->businesses_count }}</span>
                    </td>

                    {{-- --- Actions: icon-only buttons with tooltips --- --}}
                    <td class="w-px whitespace-nowrap px-4 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            {{-- --- View --- --}}
                            <x-admin.icon-action :href="route('users.show', $user)" icon="bi-eye-fill" tip="View" />

                            @if (auth()->user()?->isAdmin())
                                {{-- --- Edit --- --}}
                                <x-admin.icon-action :href="route('users.edit', $user)" variant="secondary" icon="bi-pencil-fill" tip="Edit" />

                                @if (auth()->id() !== $user->id)
                                    {{-- --- Disable / Enable --- --}}
                                    <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="inline-block">
                                        @csrf
                                        <x-admin.icon-action type="submit" :variant="$user->is_visible ? 'danger' : 'success'"
                                                             :icon="$user->is_visible ? 'bi-person-x-fill' : 'bi-person-check-fill'"
                                                             :tip="$user->is_visible ? 'Disable' : 'Enable'" tip-align="right" arrow-class="mr-2" />
                                    </form>
                                @endif
                            @endif
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="space-y-3">
                            <p class="italic text-gray-400">
                                {{ $hasActiveFilters ? 'No users matched your current filters.' : 'No users found.' }}
                            </p>

                            @if ($hasActiveFilters)
                                <button type="button" @click="resetFilters()" class="inline-flex items-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-bold text-gray-700 transition hover:bg-gray-200">
                                    Clear all filters
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-ajax mt-8">
    {{ $users->appends(request()->query())->links() }}
</div>
