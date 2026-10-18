<div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
    @forelse ($users as $user)
        <article class="card flex h-full flex-col gap-4" id="testimony-card-{{ $user->id }}">
            <div class="flex items-start gap-4">
                <x-ui.thumb :src="$user->profile_photo_url" :name="$user->name" size="h-14 w-14" round class="text-xl" />
                <div class="min-w-0 flex-1">
                    <a href="{{ route('users.show', $user) }}" class="block truncate text-lg font-semibold text-brand-900 hover:underline">{{ $user->name }}</a>
                    <p class="truncate text-sm text-gray-600">{{ $user->current_status ?? 'Member' }}{{ $user->major ? ', ' . $user->major : '' }}</p>
                    <p class="mt-1"><x-admin.visibility-badge :visible="$user->is_visible" /></p>
                </div>
            </div>

            <blockquote class="flex-1 text-base text-gray-800">&ldquo;{{ Str::limit($user->testimony, 200) }}&rdquo;</blockquote>

            <div class="border-t border-line pt-4">
                <x-admin.featured-button type="button" :visible="$user->is_visible" :featured="$user->is_featured_testimony" data-user-id="{{ $user->id }}" data-testimony-featured-toggle="{{ route('uc-testimonies.toggle-featured', $user, false) }}" />
            </div>
        </article>
    @empty
        <x-ui.empty-state class="col-span-full" title="No testimonies found">Try a different search.</x-ui.empty-state>
    @endforelse
</div>

<div class="pagination-ajax mt-8 w-full">
    {{ $users->appends(request()->query())->links() }}
</div>
