{{-- Users Table --}}
<div class="bg-white border rounded-xl overflow-hidden shadow-sm reveal-on-scroll" style="transition-delay: 350ms;">
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
                @php
                    $resolvedPhoto = $user->profile_photo_url;
                    $hasRealPhoto  = $resolvedPhoto && !str_contains($resolvedPhoto, 'ui-avatars.com');
                @endphp
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-3">
                            {{-- Avatar: real photo or gradient initials --}}
                            <div class="w-9 h-9 rounded-xl overflow-hidden flex-shrink-0 shadow-sm border border-gray-100">
                                @if($hasRealPhoto)
                                    <img src="{{ $resolvedPhoto }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-white text-xs font-black"
                                         style="background: linear-gradient(135deg, #f7931e, #fdb913);">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-gray-900 text-sm leading-tight truncate">{{ $user->name }}</p>
                                @if($user->nis)
                                    <p class="text-[10px] text-gray-400 font-medium">NIS: {{ $user->nis }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-500">{{ $user->email }}</td>
                    <td class="px-6 py-3">
                        <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase {{ $user->student_status === 'alumni' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $user->student_status }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-500">{{ $user->major }}</td>
                    <td class="px-6 py-3 text-center">
                        <span class="w-3 h-3 rounded-full inline-block {{ $user->is_visible ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <form action="{{ route('users.toggle-featured', $user) }}" method="POST">
                            @csrf
                            <button type="submit"
                                {{ !$user->is_visible ? 'disabled' : '' }}
                                class="relative group w-7 h-7 rounded-full inline-flex items-center justify-center transition-all border
                                    {{ !$user->is_visible 
                                        ? 'bg-gray-50 border-gray-100 text-gray-200 cursor-not-allowed'
                                        : ($user->is_featured
                                            ? 'bg-uco-yellow-400 border-uco-yellow-500 text-white hover:bg-uco-yellow-500'
                                            : 'bg-white border-gray-200 text-gray-300 hover:text-uco-yellow-400 hover:border-uco-yellow-300') }}">
                                <i class="bi bi-star-fill text-[10px]"></i>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-200 z-30 flex flex-col items-center">
                                    <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap uppercase tracking-wider">
                                        {{ !$user->is_visible ? 'Requires visible user' : ($user->is_featured ? 'Remove featured' : 'Make featured') }}
                                    </div>
                                    <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5"></div>
                                </div>
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-3 text-center font-bold text-gray-900">{{ $user->businesses_count }}</td>
                    <td class="px-6 py-3 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('users.show', $user) }}"
                               class="relative group w-9 h-9 rounded-xl flex items-center justify-center bg-green-100 text-green-600 hover:bg-green-500 hover:text-white transition-all duration-200 shadow-sm">
                                <i class="bi bi-eye-fill text-sm"></i>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-200 z-30 flex flex-col items-center">
                                    <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap uppercase tracking-wider">
                                        View Profile
                                    </div>
                                    <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5"></div>
                                </div>
                            </a>
                            @if(auth()->user()?->isAdmin())
                                <a href="{{ route('users.edit', $user) }}"
                                   class="relative group w-9 h-9 rounded-xl flex items-center justify-center bg-gray-100 text-gray-500 hover:bg-gray-800 hover:text-white transition-all duration-200 shadow-sm">
                                    <i class="bi bi-pencil-fill text-sm"></i>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-200 z-30 flex flex-col items-center">
                                        <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap uppercase tracking-wider">
                                            Edit User
                                        </div>
                                        <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5"></div>
                                    </div>
                                </a>
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('users.toggle-status', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="relative group w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200 shadow-sm
                                                    {{ $user->is_visible 
                                                        ? 'bg-blue-100 text-blue-600 hover:bg-blue-500 hover:text-white' 
                                                        : 'bg-red-100 text-red-600 hover:bg-red-500 hover:text-white' }}">
                                            <i class="bi {{ $user->is_visible ? 'bi-person-x-fill' : 'bi-person-check-fill' }} text-sm"></i>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-200 z-30 flex flex-col items-center">
                                                <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap uppercase tracking-wider">
                                                    {{ $user->is_visible ? 'Deactivate' : 'Activate' }}
                                                </div>
                                                <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5"></div>
                                            </div>
                                        </button>
                                    </form>
                                @endif
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
