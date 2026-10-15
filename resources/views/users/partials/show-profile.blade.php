{{-- --- Profile page left column: photo, name, edit / collab buttons, academics, contacts, certificates and activities --- --}}
<div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6">
    <!-- Profile details card -->
    <div class="bg-white rounded-3xl overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.02)] border border-slate-100 p-6 md:p-8 flex flex-col space-y-6">
        <!-- Profile Photo & Meta Side-by-Side -->
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
            <div class="relative w-28 h-28 md:w-32 md:h-32 overflow-hidden flex items-center justify-center flex-shrink-0">
                <x-premium-image :src="$user->profile_photo_url" :alt="$user->name" class="size-full">
                    <x-slot name="fallback">
                        <span class="text-4xl font-black text-slate-400 select-none">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </x-slot>
                </x-premium-image>
            </div>

            <div class="flex-1 text-center sm:text-left pt-2">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-tight mb-1">{{ $user->name }}</h1>

                {{-- Edit Button (Admin only) --}}
                @if(Auth::check() && Auth::user()->isAdmin())
                    <div class="mt-3">
                        <a href="{{ route('users.edit', $user) }}" class="btn-uco btn-uco-secondary">
                            <i class="bi bi-pencil"></i>
                            <span>Edit User</span>
                        </a>
                    </div>
                @endif

                {{-- Collab Button --}}
                @if(Auth::check() && Auth::id() !== $user->id)
                    <div class="mt-3">
                        @if($collabStatus === 'accepted')
                            <button disabled class="btn-uco bg-green-50 text-green-700 border border-green-200 cursor-default px-4 py-2 rounded-xl font-bold text-sm flex items-center gap-2">
                                <i class="bi bi-person-check-fill"></i>
                                <span>Connected</span>
                            </button>
                        @elseif($collabStatus === 'pending')
                            <button disabled class="btn-uco bg-orange-50 text-orange-700 border border-orange-200 cursor-default px-4 py-2 rounded-xl font-bold text-sm flex items-center gap-2">
                                <i class="bi bi-clock-history"></i>
                                <span>Pending Collab</span>
                            </button>
                        @else
                            <form action="{{ route('collabs.store', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn-uco btn-uco-primary px-4 py-2 rounded-xl font-bold text-sm flex items-center gap-2 transition hover:scale-105">
                                    <i class="bi bi-person-plus-fill"></i>
                                    <span>Collab</span>
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Academic Details Block -->
        <div class="space-y-1.5 pt-4 border-t border-slate-100">
            <div class="flex justify-between items-center text-xs p-2 -mx-2 rounded-xl border border-transparent">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Major</span>
                <span class="text-slate-700 font-extrabold text-right ml-2">{{ $user->major ?: '-' }}</span>
            </div>
            <div class="flex justify-between items-center text-xs p-2 -mx-2 rounded-xl border border-transparent">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Join UC Online</span>
                <span class="text-slate-700 font-extrabold text-right ml-2">{{ $user->year_of_enrollment ?: '-' }}</span>
            </div>
            <div class="flex justify-between items-center text-xs p-2 -mx-2 rounded-xl border border-transparent">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Focus</span>
                <span class="text-slate-700 font-extrabold capitalize text-right ml-2">{{ $user->current_status ?: (ucfirst($user->role) ?: '-') }}</span>
            </div>
        </div>

        <!-- Contact Details Block -->
        @if(($user->whatsapp || $user->email || $user->linkedin) && ($user->show_contact_details || (Auth::check() && Auth::user()->isAdmin())))
            <div class="space-y-1.5 pt-4 border-t border-slate-100">
                @if($user->whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->whatsapp) }}" target="_blank" 
                       class="flex justify-between items-center text-xs p-2 -mx-2 rounded-xl hover:bg-green-50/50 border border-transparent hover:border-green-100/50 transition duration-200 group">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] flex items-center gap-1.5 group-hover:text-green-600 transition-colors">
                            <i class="bi bi-whatsapp text-green-600 text-sm"></i>
                            WhatsApp
                        </span>
                        <span class="text-slate-700 font-extrabold break-all ml-2 text-right group-hover:text-green-700 transition-colors">
                            {{ $user->whatsapp }}
                        </span>
                    </a>
                @endif
                @if($user->email)
                    <a href="mailto:{{ $user->email }}" 
                       class="flex justify-between items-center text-xs p-2 -mx-2 rounded-xl hover:bg-blue-50/50 border border-transparent hover:border-blue-100/50 transition duration-200 group">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] flex items-center gap-1.5 group-hover:text-blue-600 transition-colors">
                            <i class="bi bi-envelope text-blue-600 text-sm"></i>
                            Email
                        </span>
                        <span class="text-slate-700 font-extrabold break-all ml-2 text-right group-hover:text-blue-700 transition-colors">
                            {{ $user->email }}
                        </span>
                    </a>
                @endif
                @if($user->linkedin)
                    <a href="{{ str_starts_with($user->linkedin, 'http') ? $user->linkedin : 'https://linkedin.com/in/' . $user->linkedin }}" target="_blank" 
                       class="flex justify-between items-center text-xs p-2 -mx-2 rounded-xl hover:bg-indigo-50/50 border border-transparent hover:border-indigo-100/50 transition duration-200 group">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] flex items-center gap-1.5 group-hover:text-indigo-600 transition-colors">
                            <i class="bi bi-linkedin text-indigo-600 text-sm"></i>
                            LinkedIn
                        </span>
                        <span class="text-slate-700 font-extrabold break-all ml-2 text-right group-hover:text-indigo-700 transition-colors">
                            {{ $user->linkedin }}
                        </span>
                    </a>
                @endif
            </div>
        @elseif($user->whatsapp || $user->email || $user->linkedin)
            <div class="pt-4 border-t border-slate-100">
                <div class="px-3 py-2 bg-slate-50 rounded-xl border border-slate-100 text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider leading-relaxed">
                        <i class="bi bi-lock-fill mr-1"></i> Contact details hidden by user
                    </p>
                </div>
            </div>
        @endif

        <!-- Core Competencies Block -->
        @if($user->skills->count() > 0)
            <div class="space-y-2 pt-4 border-t border-slate-100">
                <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Core Competencies</div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($user->skills as $skill)
                        <span class="px-2 py-0.5 bg-slate-50 text-slate-600 rounded-md text-[9px] font-bold border border-slate-200/40">
                            {{ $skill->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        @include('users.partials.show-certificates')

        <!-- Activities / Testimony Section -->
        @include('users.partials.show-activities')

    </div>
</div>
