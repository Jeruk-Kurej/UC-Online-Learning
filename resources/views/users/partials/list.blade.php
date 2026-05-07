{{-- Users Table --}}
<div class="bg-white border rounded-[2.5rem] overflow-hidden shadow-sm reveal-on-scroll" style="transition-delay: 350ms;">
    <table class="w-full text-left">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Name</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Email</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Status</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Peminatan</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Visible</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Featured</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Businesses</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 font-bold text-gray-900">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase {{ $user->student_status === 'alumni' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $user->student_status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->major }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="w-3 h-3 rounded-full inline-block {{ $user->is_visible ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('users.toggle-featured', $user) }}" method="POST">
                            @csrf
                            <button type="submit"
                                title="{{ $user->is_featured ? 'Remove from featured' : 'Add to featured' }}"
                                class="w-7 h-7 rounded-full inline-flex items-center justify-center transition-all border
                                    {{ $user->is_featured
                                        ? 'bg-uco-yellow-400 border-uco-yellow-500 text-white hover:bg-uco-yellow-500'
                                        : 'bg-white border-gray-200 text-gray-300 hover:text-uco-yellow-400 hover:border-uco-yellow-300' }}">
                                <i class="bi bi-star-fill text-[10px]"></i>
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-center font-bold text-gray-900">{{ $user->businesses_count }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('users.show', $user) }}" class="p-2 text-gray-400 hover:text-uco-orange-500 transition">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center">
                        <div class="space-y-3">
                            @php
                                $hasActiveFilters = request()->filled('search')
                                    || request()->filled('sort_name')
                                    || request()->filled('sort_year')
                                    || request()->filled('student_status')
                                    || request()->filled('major')
                                    || request()->filled('year_of_enrollment');
                            @endphp

                            <p class="text-gray-400 italic">
                                {{ $hasActiveFilters ? 'No users matched your current filters.' : 'No users found.' }}
                            </p>

                            @if($hasActiveFilters)
                                <button type="button" @click="resetFilters()" class="inline-flex items-center px-4 py-2 text-sm font-bold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
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

<div class="mt-8 pagination-ajax">
    {{ $users->appends(request()->query())->links() }}
</div>
