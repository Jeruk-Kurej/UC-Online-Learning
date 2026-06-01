{{-- Users Table --}}
<div class="overflow-x-auto bg-white border rounded-xl shadow-sm reveal-on-scroll" style="transition-delay: 350ms;">
    <table class="w-full text-left">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em]">Name</th>
                <th class="px-4 py-3 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em]">Email</th>
                <th class="px-4 py-3 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em] whitespace-nowrap">Status</th>
                <th class="px-4 py-3 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em]">Major</th>
                <th class="px-4 py-3 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em] text-center whitespace-nowrap">Flags</th>
                <th class="px-4 py-3 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em] text-center whitespace-nowrap">Biz</th>
                <th class="px-4 py-3 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em] text-right whitespace-nowrap w-px">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($users as $user)
                @php
                    $resolvedPhoto = $user->profile_photo_url;
                    $hasRealPhoto  = $resolvedPhoto && !str_contains($resolvedPhoto, 'ui-avatars.com');
                @endphp
                <tr class="hover:bg-orange-50/30 transition">

                    {{-- Name + Avatar --}}
                    <td class="px-4 py-3 max-w-[180px]">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg overflow-hidden flex-shrink-0 shadow-sm border border-gray-100">
                                @if($hasRealPhoto)
                                    <img src="{{ $resolvedPhoto }}" alt="{{ $user->name }}" class="w-full h-full object-contain bg-slate-50">
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
                                    <p class="text-[10px] text-gray-400 font-medium">{{ $user->nis }}</p>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Email (truncated) --}}
                    <td class="px-4 py-3 max-w-[180px]">
                        <span class="block text-sm text-gray-500 truncate" title="{{ $user->email }}">{{ $user->email }}</span>
                    </td>

                    {{-- Student Status Badge --}}
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase {{ $user->student_status === 'alumni' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $user->student_status }}
                        </span>
                    </td>

                    {{-- Major (truncated) --}}
                    <td class="px-4 py-3 max-w-[120px]">
                        <span class="block text-sm text-gray-500 truncate" title="{{ $user->major ?: '-' }}">{{ $user->major ?: '-' }}</span>
                    </td>

                    {{-- Flags: Visible dot + Featured star (combined column) --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">
                            {{-- Visible dot --}}
                            <div class="relative group flex-shrink-0">
                                <span class="w-2.5 h-2.5 rounded-full inline-block {{ $user->is_visible ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-30 flex flex-col items-center">
                                    <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap">{{ $user->is_visible ? 'Visible' : 'Hidden' }}</div>
                                    <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5"></div>
                                </div>
                            </div>

                            {{-- Featured star toggle --}}
                            <form action="{{ route('users.toggle-featured', $user) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    {{ !$user->is_visible ? 'disabled' : '' }}
                                    class="relative group w-6 h-6 rounded-full inline-flex items-center justify-center transition-all border
                                        {{ !$user->is_visible
                                            ? 'bg-gray-50 border-gray-100 text-gray-200 cursor-not-allowed'
                                            : ($user->is_featured
                                                ? 'bg-uco-yellow-400 border-uco-yellow-500 text-white hover:bg-uco-yellow-500'
                                                : 'bg-white border-gray-200 text-gray-300 hover:text-uco-yellow-400 hover:border-uco-yellow-300') }}">
                                    <i class="bi bi-star-fill text-[9px]"></i>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-30 flex flex-col items-center">
                                        <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap uppercase tracking-wider">
                                            {{ !$user->is_visible ? 'Need visible' : ($user->is_featured ? 'Unfeature' : 'Feature') }}
                                        </div>
                                        <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5"></div>
                                    </div>
                                </button>
                            </form>
                        </div>
                    </td>

                    {{-- Businesses count --}}
                    <td class="px-4 py-3 text-center">
                        <span class="text-sm font-bold text-gray-700">{{ $user->businesses_count }}</span>
                    </td>

                    {{-- Actions: icon-only buttons with tooltips --}}
                    <td class="px-4 py-3 whitespace-nowrap w-px">
                        <div class="flex items-center justify-end gap-1.5">
                            {{-- View --}}
                            <a href="{{ route('users.show', $user) }}"
                               class="relative group w-8 h-8 inline-flex items-center justify-center rounded-md text-white transition-colors"
                               style="background-color: #198754;"
                               onmouseover="this.style.backgroundColor='#157347'" onmouseout="this.style.backgroundColor='#198754'">
                                <i class="bi bi-eye-fill text-xs"></i>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-30 flex flex-col items-center">
                                    <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap">View</div>
                                    <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5"></div>
                                </div>
                            </a>

                            @if(auth()->user()?->isAdmin())
                                {{-- Edit --}}
                                <a href="{{ route('users.edit', $user) }}"
                                   class="relative group w-8 h-8 inline-flex items-center justify-center rounded-md text-white transition-colors"
                                   style="background-color: #6c757d;"
                                   onmouseover="this.style.backgroundColor='#5c636a'" onmouseout="this.style.backgroundColor='#6c757d'">
                                    <i class="bi bi-pencil-fill text-xs"></i>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-30 flex flex-col items-center">
                                        <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap">Edit</div>
                                        <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5"></div>
                                    </div>
                                </a>

                                @if(auth()->id() !== $user->id)
                                    {{-- Disable / Enable --}}
                                    <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit"
                                                class="relative group w-8 h-8 inline-flex items-center justify-center rounded-md text-white transition-colors"
                                                style="background-color: {{ $user->is_visible ? '#dc3545' : '#198754' }};"
                                                onmouseover="this.style.backgroundColor='{{ $user->is_visible ? '#bb2d3b' : '#157347' }}'"
                                                onmouseout="this.style.backgroundColor='{{ $user->is_visible ? '#dc3545' : '#198754' }}'">
                                            <i class="bi {{ $user->is_visible ? 'bi-person-x-fill' : 'bi-person-check-fill' }} text-xs"></i>
                                            <div class="absolute bottom-full right-0 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-30 flex flex-col items-end">
                                                <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap">{{ $user->is_visible ? 'Disable' : 'Enable' }}</div>
                                                <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5 mr-2"></div>
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
                    <td colspan="7" class="px-6 py-16 text-center">
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

<style>
    /* Force page numbers to show on mobile viewports for Laravel Tailwind Pagination */
    .pagination-ajax nav > div:first-child {
        display: none !important; /* Hide the simple 'Previous/Next' mobile fallback */
    }
    .pagination-ajax nav > div:last-child {
        display: flex !important; /* Force the desktop version containing page numbers to show */
        flex-direction: column !important;
        align-items: center !important;
        gap: 12px !important;
    }
    @media (min-width: 640px) {
        .pagination-ajax nav > div:last-child {
            flex-direction: row !important;
            justify-content: space-between !important;
        }
    }
    /* Force all page numbers inside the inline-flex block to show on mobile */
    .pagination-ajax nav > div:last-child div:last-child > span {
        display: inline-flex !important;
    }
    .pagination-ajax nav > div:last-child div:last-child > span > * {
        display: inline-flex !important;
    }
</style>
