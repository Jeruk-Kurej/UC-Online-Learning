{{-- --- Business page side column: owner card (photo, academics, contacts) and the other people who run it --- --}}
<x-people.profile-card :person="$owner" title-start="Owned" title-end="By" :status="$owner->student_status"
    :career="$owner->current_status ?: ($owner->role_label ?: '-')" />

{{-- --- Additional Owners Section (Title & Grid) --- --}}
@if ($coOwners->isNotEmpty())
    <div class="flex flex-col gap-3">
        <div class="relative">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-6 bg-gradient-to-b from-[#2563eb] to-[#60a5fa] rounded-full flex-shrink-0"></span>
                <h4 class="text-base font-black uppercase tracking-[0.15em] text-gray-700">Also Managed <span class="uco-text-gradient-blue">By</span></h4>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            @foreach ($coOwners as $addOwner)
                <a href="{{ route('users.show', $addOwner) }}" class="flex flex-col items-center justify-center gap-2.5 p-4 bg-white rounded-2xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] hover:shadow-[0_8px_30px_rgb(247,147,30,0.08)] hover:border-orange-200 transition-all text-center group">
                    @if ($addOwner->profile_photo_mini_url)
                        <img src="{{ $addOwner->profile_photo_mini_url }}" alt="{{ $addOwner->name }}"
                             class="w-12 h-12 rounded-sm object-contain group-hover:scale-105 transition-transform">
                    @else
                        <div class="w-12 h-12 rounded-sm flex items-center justify-center text-base font-black bg-gradient-to-br from-gray-50 to-gray-100 text-gray-400 group-hover:scale-105 transition-transform group-hover:from-orange-50 group-hover:to-orange-100 group-hover:text-orange-600">
                            {{ strtoupper(substr($addOwner->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0 w-full">
                        <p class="text-xs font-bold text-gray-900 leading-tight truncate group-hover:text-orange-600 transition-colors">{{ $addOwner->name }}</p>
                        <p class="text-[10px] font-medium text-gray-500 capitalize truncate mt-0.5">{{ $addOwner->pivot->position ?? ($addOwner->role ?? 'Member') }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif
