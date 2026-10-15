{{-- --- Profile page left column: photo, name, edit / collab buttons, academics, contacts, certificates and activities --- --}}
<div class="space-y-6 lg:sticky lg:top-6 lg:col-span-4">
    {{-- --- Profile details card --- --}}
    <div class="flex flex-col space-y-6 overflow-hidden rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.02)] md:p-8">
        {{-- --- Profile Photo & Meta Side-by-Side --- --}}
        <div class="flex flex-col items-center gap-5 sm:flex-row sm:items-start">
            <div class="relative flex h-28 w-28 flex-shrink-0 items-center justify-center overflow-hidden md:h-32 md:w-32">
                <x-premium-image :src="$user->profile_photo_url" :alt="$user->name" class="size-full">
                    <x-slot name="fallback">
                        <span class="select-none text-4xl font-black text-slate-400">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </x-slot>
                </x-premium-image>
            </div>

            <div class="flex-1 pt-2 text-center sm:text-left">
                <h1 class="mb-1 text-2xl font-black leading-tight tracking-tight text-slate-900">{{ $user->name }}</h1>

                {{-- --- Edit Button (Admin only) --- --}}
                @if (Auth::check() && Auth::user()->isAdmin())
                    <div class="mt-3">
                        <a href="{{ route('users.edit', $user) }}" class="btn-uco btn-uco-secondary">
                            <i class="bi bi-pencil"></i>
                            <span>Edit User</span>
                        </a>
                    </div>
                @endif

                {{-- --- Collab Button --- --}}
                @if (Auth::check() && Auth::id() !== $user->id)
                    <div class="mt-3">
                        @if ($collabStatus === 'accepted')
                            <button disabled class="btn-uco flex cursor-default items-center gap-2 rounded-xl border border-green-200 bg-green-50 px-4 py-2 text-sm font-bold text-green-700">
                                <i class="bi bi-person-check-fill"></i>
                                <span>Connected</span>
                            </button>
                        @elseif($collabStatus === 'pending')
                            <button disabled class="btn-uco flex cursor-default items-center gap-2 rounded-xl border border-orange-200 bg-orange-50 px-4 py-2 text-sm font-bold text-orange-700">
                                <i class="bi bi-clock-history"></i>
                                <span>Pending Collab</span>
                            </button>
                        @else
                            <form action="{{ route('collabs.store', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn-uco btn-uco-primary flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-bold transition hover:scale-105">
                                    <i class="bi bi-person-plus-fill"></i>
                                    <span>Collab</span>
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- --- Academic Details Block --- --}}
        <div class="space-y-1.5 border-t border-slate-100 pt-4">
            <div class="-mx-2 flex items-center justify-between rounded-xl border border-transparent p-2 text-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Major</span>
                <span class="ml-2 text-right font-extrabold text-slate-700">{{ $user->major ?: '-' }}</span>
            </div>
            <div class="-mx-2 flex items-center justify-between rounded-xl border border-transparent p-2 text-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Join UC Online</span>
                <span class="ml-2 text-right font-extrabold text-slate-700">{{ $user->year_of_enrollment ?: '-' }}</span>
            </div>
            <div class="-mx-2 flex items-center justify-between rounded-xl border border-transparent p-2 text-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Focus</span>
                <span class="ml-2 text-right font-extrabold capitalize text-slate-700">{{ $user->current_status ?: (ucfirst($user->role) ?: '-') }}</span>
            </div>
        </div>

        {{-- --- Contact Details Block --- --}}
        @if (($user->whatsapp || $user->email || $user->linkedin) && ($user->show_contact_details || (Auth::check() && Auth::user()->isAdmin())))
            <div class="space-y-1.5 border-t border-slate-100 pt-4">
                @if ($user->whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->whatsapp) }}" target="_blank"
                       class="group -mx-2 flex items-center justify-between rounded-xl border border-transparent p-2 text-xs transition duration-200 hover:border-green-100/50 hover:bg-green-50/50">
                        <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 transition-colors group-hover:text-green-600">
                            <i class="bi bi-whatsapp text-sm text-green-600"></i>
                            WhatsApp
                        </span>
                        <span class="ml-2 break-all text-right font-extrabold text-slate-700 transition-colors group-hover:text-green-700">
                            {{ $user->whatsapp }}
                        </span>
                    </a>
                @endif
                @if ($user->email)
                    <a href="mailto:{{ $user->email }}"
                       class="group -mx-2 flex items-center justify-between rounded-xl border border-transparent p-2 text-xs transition duration-200 hover:border-blue-100/50 hover:bg-blue-50/50">
                        <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 transition-colors group-hover:text-blue-600">
                            <i class="bi bi-envelope text-sm text-blue-600"></i>
                            Email
                        </span>
                        <span class="ml-2 break-all text-right font-extrabold text-slate-700 transition-colors group-hover:text-blue-700">
                            {{ $user->email }}
                        </span>
                    </a>
                @endif
                @if ($user->linkedin)
                    <a href="{{ str_starts_with($user->linkedin, 'http') ? $user->linkedin : 'https://linkedin.com/in/' . $user->linkedin }}" target="_blank"
                       class="group -mx-2 flex items-center justify-between rounded-xl border border-transparent p-2 text-xs transition duration-200 hover:border-indigo-100/50 hover:bg-indigo-50/50">
                        <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 transition-colors group-hover:text-indigo-600">
                            <i class="bi bi-linkedin text-sm text-indigo-600"></i>
                            LinkedIn
                        </span>
                        <span class="ml-2 break-all text-right font-extrabold text-slate-700 transition-colors group-hover:text-indigo-700">
                            {{ $user->linkedin }}
                        </span>
                    </a>
                @endif
            </div>
        @elseif($user->whatsapp || $user->email || $user->linkedin)
            <div class="border-t border-slate-100 pt-4">
                <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 text-center">
                    <p class="text-[10px] font-bold uppercase leading-relaxed tracking-wider text-slate-400">
                        <i class="bi bi-lock-fill mr-1"></i> Contact details hidden by user
                    </p>
                </div>
            </div>
        @endif

        {{-- --- Core Competencies Block --- --}}
        @if ($user->skills->count() > 0)
            <div class="space-y-2 border-t border-slate-100 pt-4">
                <div class="text-[9px] font-black uppercase tracking-widest text-slate-400">Core Competencies</div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($user->skills as $skill)
                        <span class="rounded-md border border-slate-200/40 bg-slate-50 px-2 py-0.5 text-[9px] font-bold text-slate-600">
                            {{ $skill->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        @include('users.partials.show-certificates')

        {{-- --- Activities / Testimony Section --- --}}
        @include('users.partials.show-activities')

    </div>
</div>
