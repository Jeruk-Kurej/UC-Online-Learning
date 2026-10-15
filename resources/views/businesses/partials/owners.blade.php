{{-- --- Business page side column: owner card (photo, academics, contacts) and the other people who run it --- --}}
<x-people.profile-card :person="$owner" title-start="Owned" title-end="By" :status="$owner->student_status"
                       :career="$owner->current_status ?: ($owner->role_label ?: '-')" />

{{-- --- Additional Owners Section (Title & Grid) --- --}}
@if ($coOwners->isNotEmpty())
    <div class="flex flex-col gap-3">
        <div class="relative">
            <div class="flex items-center gap-2">
                <span class="h-6 w-1.5 flex-shrink-0 rounded-full bg-gradient-to-b from-[#2563eb] to-[#60a5fa]"></span>
                <h4 class="text-base font-black uppercase tracking-[0.15em] text-gray-700">Also Managed <span class="uco-text-gradient-blue">By</span></h4>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            @foreach ($coOwners as $addOwner)
                <a href="{{ route('users.show', $addOwner) }}" class="group flex flex-col items-center justify-center gap-2.5 rounded-2xl border border-gray-100 bg-white p-4 text-center shadow-[0_4px_20px_rgb(0,0,0,0.03)] transition-all hover:border-orange-200 hover:shadow-[0_8px_30px_rgb(247,147,30,0.08)]">
                    @if ($addOwner->profile_photo_mini_url)
                        <img src="{{ $addOwner->profile_photo_mini_url }}" alt="{{ $addOwner->name }}"
                             class="h-12 w-12 rounded-sm object-contain transition-transform group-hover:scale-105">
                    @else
                        <div class="flex h-12 w-12 items-center justify-center rounded-sm bg-gradient-to-br from-gray-50 to-gray-100 text-base font-black text-gray-400 transition-transform group-hover:scale-105 group-hover:from-orange-50 group-hover:to-orange-100 group-hover:text-orange-600">
                            {{ $addOwner->initial }}
                        </div>
                    @endif
                    <div class="w-full min-w-0">
                        <p class="truncate text-xs font-bold leading-tight text-gray-900 transition-colors group-hover:text-orange-600">{{ $addOwner->name }}</p>
                        <p class="mt-0.5 truncate text-[10px] font-medium capitalize text-gray-500">{{ $addOwner->pivot->position ?? ($addOwner->role ?? 'Member') }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif
