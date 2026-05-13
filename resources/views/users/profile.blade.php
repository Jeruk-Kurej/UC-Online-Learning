@use('Illuminate\Support\Facades\Storage')

<x-app-layout>
    {{-- Back Button Wrapper --}}
    <div class="mb-8 px-4 sm:px-0">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">

            <div class="flex-1 flex flex-row items-center sm:items-start gap-4 sm:gap-5">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-soft-gray-900 tracking-tight leading-tight">
                        User Profile
                    </h1>
                </div>
            </div>
        </div>
    </div>

    {{-- 35/65 Grid Layout Styles --}}
    <style>
        @media (min-width: 768px) {
            #profile-layout-grid {
                grid-template-columns: 35fr 65fr;
            }
        }
    </style>

    <div class="grid grid-cols-1 gap-6 md:gap-8 items-start" id="profile-layout-grid">
        
        {{-- ═══ LEFT COLUMN: Owner Profile (35%) ═══ --}}
        <div class="md:sticky md:top-6 space-y-4">
            @php
                $owner      = $user;
                $ownerAcad  = $owner->academic_data ?? [];
                $ownerGrad  = $owner->graduation_data ?? [];
                $ownerPerso = $owner->personal_data ?? [];
                $ownerRoleLabel = match ($owner->role ?? '') {
                    'admin'   => 'Administrator',
                    'alumni'  => 'UCO Alumni',
                    'student' => 'UCO Student',
                    default   => ucfirst($owner->role ?? 'Member'),
                };
                // The model accessor now correctly resolves the path via HasImage::resolveImage
                // It returns either the real photo URL, a Google Drive URL, or ui-avatars fallback
                $resolvedPhotoUrl = $owner->profile_photo_url;
                $isRealPhoto = $resolvedPhotoUrl && !str_contains($resolvedPhotoUrl, 'ui-avatars.com');
            @endphp

            {{-- ✨ Premium Owner Card --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">

                {{-- Top section: Photo + Name/Info side by side --}}
                <div class="p-5 flex items-start gap-4">
                    {{-- Left: Avatar --}}
                    <div class="flex-shrink-0">
                        @if ($isRealPhoto)
                            <img src="{{ $resolvedPhotoUrl }}" alt="{{ $owner->name }}"
                                class="w-20 h-20 rounded-2xl object-cover shadow-md border-2 border-gray-100">
                        @else
                            <div class="w-20 h-20 rounded-2xl flex items-center justify-center shadow-md border-2 border-orange-100"
                                style="background: linear-gradient(135deg, #f7931e, #fdb913);">
                                <span class="text-white text-3xl font-black select-none" style="text-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                                    {{ strtoupper(substr($owner->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Right: Name, role, major --}}
                    <div class="flex-1 min-w-0 pt-1">
                        {{-- Name --}}
                        <h3 class="text-lg font-extrabold text-gray-900 leading-tight truncate">
                            {{ $owner->name }}
                        </h3>



                        @if ($owner->student_status)
                            <p class="mt-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                {{ $owner->student_status }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-100 mx-5"></div>

                {{-- Academic Details --}}
                <div class="px-5 py-4">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Academic Details</h4>
                    <div class="space-y-2 text-xs">
                        @if($owner->major)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 font-medium">Major</span>
                                <span class="text-gray-900 font-bold">{{ $owner->major }}</span>
                            </div>
                        @endif
                        @if($owner->year_of_enrollment)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 font-medium">Batch</span>
                                <span class="text-gray-900 font-bold">{{ $owner->year_of_enrollment }}</span>
                            </div>
                        @endif
                        @if($owner->current_status)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 font-medium">Focus</span>
                                <span class="text-gray-900 font-bold capitalize">{{ $owner->current_status }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Contact section --}}
                @if($owner->whatsapp || ($ownerPerso['instagram'] ?? false) || ($ownerGrad['official_email'] ?? false))
                    <div class="border-t border-gray-100 mx-5"></div>
                    <div class="px-5 py-4 space-y-1">
                        @if ($owner->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $owner->whatsapp) }}"
                                target="_blank"
                                class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 hover:bg-green-50">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm flex-shrink-0 transition-all duration-200
                                            bg-green-100 text-green-600 group-hover:bg-green-500 group-hover:text-white">
                                    <i class="bi bi-whatsapp"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[9px] font-black uppercase tracking-[0.12em] leading-none text-gray-400">WhatsApp</p>
                                    <p class="text-xs font-bold truncate text-gray-700 mt-0.5">{{ $owner->whatsapp }}</p>
                                </div>
                                <i class="bi bi-arrow-up-right text-[10px] text-gray-300 group-hover:text-green-500"></i>
                            </a>
                        @endif

                        @if ($ownerPerso['instagram'] ?? false)
                            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm flex-shrink-0 bg-pink-100 text-pink-600">
                                    <i class="bi bi-instagram"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[9px] font-black uppercase tracking-[0.12em] leading-none text-gray-400">Instagram</p>
                                    <p class="text-xs font-bold truncate text-gray-700 mt-0.5">@{{ $ownerPerso['instagram'] }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($ownerGrad['official_email'] ?? false)
                            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm flex-shrink-0 bg-blue-100 text-blue-600">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[9px] font-black uppercase tracking-[0.12em] leading-none text-gray-400">Email</p>
                                    <p class="text-xs font-bold truncate text-gray-700 mt-0.5">{{ $ownerGrad['official_email'] }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- ═══ RIGHT COLUMN: Businesses Catalog (65%) ═══ --}}
        <div class="space-y-8 min-w-0">
            
            {{-- Owned Businesses Section --}}
            <div class="bg-white shadow-lg sm:rounded-3xl overflow-hidden border border-soft-gray-100 p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shadow-inner">
                        <i class="bi bi-briefcase-fill text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 leading-tight">Owned Businesses</h2>
                        <p class="text-xs text-gray-500 font-medium">Businesses directly founded or owned by this profile.</p>
                    </div>
                </div>

                @if($ownedBusinesses->isEmpty())
                    <div class="bg-gray-50 rounded-2xl p-8 text-center border border-gray-100 border-dashed">
                        <div class="inline-flex w-16 h-16 rounded-full bg-white items-center justify-center shadow-sm border border-gray-100 text-gray-300 mb-3">
                            <i class="bi bi-inbox text-2xl"></i>
                        </div>
                        <h3 class="text-sm font-bold text-gray-600">No businesses yet</h3>
                        <p class="text-xs text-gray-400 mt-1">This user hasn't created any businesses.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @foreach($ownedBusinesses as $biz)
                            <a href="{{ route('businesses.show', $biz) }}" class="group block bg-white border border-gray-100 rounded-2xl overflow-hidden hover:border-orange-300 hover:shadow-xl transition-all duration-300 relative">
                                <div class="absolute inset-0 bg-gradient-to-br from-orange-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                
                                <div class="p-5 relative">
                                    <div class="flex items-start gap-4">
                                        <div class="w-16 h-16 bg-gray-50 rounded-xl flex items-center justify-center border border-gray-100 group-hover:border-orange-100 transition-colors flex-shrink-0 shadow-sm overflow-hidden">
                                            @if($biz->logo_url)
                                                <img src="{{ storage_image_url($biz->logo_url, ['width' => 128, 'height' => 128, 'crop' => 'fill']) }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-xl font-black text-gray-300">{{ substr($biz->name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0 pt-1">
                                            <h3 class="font-bold text-gray-900 text-base leading-tight truncate group-hover:text-orange-600 transition-colors">
                                                {{ $biz->name }}
                                            </h3>
                                            @if($biz->category)
                                                <span class="inline-block mt-1 text-[9px] font-black uppercase tracking-wider text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                                                    {{ $biz->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 flex items-center justify-between border-t border-gray-50 pt-3">
                                        <div class="flex items-center gap-1.5 text-xs text-gray-500 font-medium">
                                            <i class="bi bi-geo-alt text-gray-400"></i>
                                            <span class="truncate max-w-[120px]">{{ $biz->city ?? 'Location N/A' }}</span>
                                        </div>
                                        <div class="w-6 h-6 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center group-hover:bg-orange-500 group-hover:text-white transition-colors">
                                            <i class="bi bi-arrow-right text-xs"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Member / Managed Businesses Section --}}
            @if($memberBusinesses->isNotEmpty())
            <div class="bg-white shadow-lg sm:rounded-3xl overflow-hidden border border-soft-gray-100 p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shadow-inner">
                        <i class="bi bi-people-fill text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 leading-tight">Also Manages</h2>
                        <p class="text-xs text-gray-500 font-medium">Businesses where this profile is a team member.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach($memberBusinesses as $biz)
                        <a href="{{ route('businesses.show', $biz) }}" class="group block bg-white border border-gray-100 rounded-2xl overflow-hidden hover:border-blue-300 hover:shadow-xl transition-all duration-300 relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            
                            <div class="p-5 relative">
                                <div class="flex items-start gap-4">
                                    <div class="w-16 h-16 bg-gray-50 rounded-xl flex items-center justify-center border border-gray-100 group-hover:border-blue-100 transition-colors flex-shrink-0 shadow-sm overflow-hidden">
                                        @if($biz->logo_url)
                                            <img src="{{ storage_image_url($biz->logo_url, ['width' => 128, 'height' => 128, 'crop' => 'fill']) }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-xl font-black text-gray-300">{{ substr($biz->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0 pt-1">
                                        <h3 class="font-bold text-gray-900 text-base leading-tight truncate group-hover:text-blue-600 transition-colors">
                                            {{ $biz->name }}
                                        </h3>
                                        @if($biz->pivot && $biz->pivot->position)
                                            <span class="inline-block mt-1 text-[9px] font-black uppercase tracking-wider text-blue-600 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-full">
                                                {{ $biz->pivot->position }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mt-4 flex items-center justify-between border-t border-gray-50 pt-3">
                                    <div class="flex items-center gap-1.5 text-xs text-gray-500 font-medium">
                                        <i class="bi bi-geo-alt text-gray-400"></i>
                                        <span class="truncate max-w-[120px]">{{ $biz->city ?? 'Location N/A' }}</span>
                                    </div>
                                    <div class="w-6 h-6 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white transition-colors">
                                        <i class="bi bi-arrow-right text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

    </div>
</x-app-layout>
