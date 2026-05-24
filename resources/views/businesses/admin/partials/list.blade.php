<div class="bg-white border rounded-xl overflow-hidden shadow-sm reveal-on-scroll">
    <table class="w-full text-left">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em]">Business</th>
                <th class="px-4 py-3 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em]">Owner</th>
                <th class="px-4 py-3 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em]">Location</th>
                <th class="px-4 py-3 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em] whitespace-nowrap">Status</th>
                <th class="px-4 py-3 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em] text-center whitespace-nowrap">Visible</th>
                <th class="px-4 py-3 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em] text-center whitespace-nowrap">Featured</th>
                <th class="px-4 py-3 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em] text-right whitespace-nowrap w-px">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($businesses as $b)
                <tr class="hover:bg-orange-50/30 transition">
                    <td class="px-4 py-3 max-w-[220px]">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0 shadow-sm border border-gray-100 overflow-hidden">
                                @if($b->logo_url)
                                    <img src="{{ $b->logo_url }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-white text-xs font-black"
                                         style="background: linear-gradient(135deg, #f7931e, #fdb913);">
                                        {{ strtoupper(substr($b->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-gray-900 text-sm leading-tight truncate">{{ $b->name }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 max-w-[180px]">
                        <div class="flex items-center gap-2">
                            @php
                                $ownerName = optional($b->user)->name ?? 'Unknown';
                                $ownerPhoto = optional($b->user)->profile_photo_url;
                                $hasRealPhoto = $ownerPhoto && !str_contains($ownerPhoto, 'ui-avatars.com');
                            @endphp
                            <div class="w-6 h-6 rounded-full overflow-hidden flex-shrink-0 shadow-sm border border-gray-100">
                                @if($hasRealPhoto)
                                    <img src="{{ $ownerPhoto }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-white text-[10px] font-black"
                                         style="background: linear-gradient(135deg, #f7931e, #fdb913);">
                                        {{ strtoupper(substr($ownerName, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <span class="font-bold text-gray-700 text-xs truncate">{{ $ownerName }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 max-w-[150px]">
                        <span class="block text-sm text-gray-500 truncate" title="{{ $b->city }}, {{ $b->province }}">{{ $b->city }}, {{ $b->province }}</span>
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $statusClasses = match($b->status) {
                                'approved' => 'bg-green-100 text-green-700',
                                'pending' => 'bg-amber-100 text-amber-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                'need_revision' => 'bg-blue-100 text-blue-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <span class="whitespace-nowrap px-2 py-1 rounded-md text-[10px] font-bold uppercase {{ $statusClasses }}">
                            {{ $b->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="relative group flex-shrink-0 inline-block">
                            <span class="w-2.5 h-2.5 rounded-full inline-block {{ $b->is_visible ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-30 flex flex-col items-center">
                                <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap">{{ $b->is_visible ? 'Visible' : 'Hidden' }}</div>
                                <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form action="{{ route('businesses.toggle-featured', $b) }}" method="POST" class="inline-block toggle-featured-form">
                             @csrf
                             <button type="submit"
                                     {{ !$b->is_visible ? 'disabled' : '' }}
                                     class="relative group w-6 h-6 rounded-full inline-flex items-center justify-center transition-all border
                                         {{ !$b->is_visible 
                                             ? 'bg-gray-50 border-gray-100 text-gray-200 cursor-not-allowed'
                                             : ($b->is_featured
                                                 ? 'bg-uco-yellow-400 border-uco-yellow-500 text-white hover:bg-uco-yellow-500'
                                                 : 'bg-white border-gray-200 text-gray-300 hover:text-uco-yellow-400 hover:border-uco-yellow-300') }}">
                                 <i class="bi bi-star-fill text-[9px]"></i>
                                 <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-30 flex flex-col items-center">
                                     <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap uppercase tracking-wider tooltip-text">
                                         {{ !$b->is_visible ? 'Need visible' : ($b->is_featured ? 'Unfeature' : 'Feature') }}
                                     </div>
                                     <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5"></div>
                                 </div>
                             </button>
                         </form>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap w-px">
                        <div class="flex items-center justify-end gap-1.5">
                             <a href="{{ route('businesses.show', $b) }}" 
                                class="relative group w-8 h-8 inline-flex items-center justify-center rounded-md text-white transition-colors"
                                style="background-color: #198754;"
                                onmouseover="this.style.backgroundColor='#157347'" onmouseout="this.style.backgroundColor='#198754'">
                                 <i class="bi bi-eye-fill text-xs"></i>
                                 <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-30 flex flex-col items-center">
                                     <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap">View Details</div>
                                     <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5"></div>
                                 </div>
                             </a>
                             
                             <button type="button" 
                                     onclick="openStatusModal({{ json_encode(['id' => $b->id, 'name' => $b->name, 'status' => $b->status, 'reason' => $b->rejection_reason]) }})"
                                     class="relative group w-8 h-8 inline-flex items-center justify-center rounded-md text-white transition-colors"
                                     style="background-color: #6c757d;"
                                     onmouseover="this.style.backgroundColor='#5c636a'" onmouseout="this.style.backgroundColor='#6c757d'">
                                 <i class="bi bi-pencil-square text-xs"></i>
                                 <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-30 flex flex-col items-center">
                                     <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap">Change Status</div>
                                     <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5"></div>
                                 </div>
                             </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center">
                        <p class="text-gray-400 italic">No businesses found matching the criteria.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
</div>

@if($businesses->hasPages())
    <div class="mt-8 pagination-ajax">
        {{ $businesses->links() }}
    </div>
@endif
