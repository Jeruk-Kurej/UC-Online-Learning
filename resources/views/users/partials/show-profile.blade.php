{{-- --- Profile page left column: photo, name, edit / collab buttons, academics, contacts, certificates and activities --- --}}
<div class="space-y-6 lg:sticky lg:top-6 lg:col-span-4">
    {{-- --- Profile details card --- --}}
    <div class="flex flex-col space-y-6 overflow-hidden rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.02)] md:p-8">
        {{-- --- Profile Photo & Meta Side-by-Side --- --}}
        <div class="flex flex-col items-center gap-5 sm:flex-row sm:items-start">
            <div class="relative flex h-28 w-28 flex-shrink-0 items-center justify-center overflow-hidden md:h-32 md:w-32">
                <x-premium-image :src="$user->profile_photo_url" :alt="$user->name" class="size-full">
                    <x-slot name="fallback">
                        <span class="select-none text-4xl font-bold text-gray-600">{{ $user->initial }}</span>
                    </x-slot>
                </x-premium-image>
            </div>

            <div class="flex-1 pt-2 text-center sm:text-left">
                <h1 class="mb-1 text-2xl font-bold leading-tight text-slate-900">{{ $user->name }}</h1>

                {{-- --- Edit Button (Admin only) --- --}}
                @if (Auth::check() && Auth::user()->isAdmin())
                    <div class="mt-3">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary">
                            <i class="bi bi-pencil"></i>
                            <span>Edit User</span>
                        </a>
                    </div>
                @endif

                {{-- --- Collab Button --- --}}
                @if (Auth::check() && Auth::id() !== $user->id)
                    <div class="mt-3">
                        @if ($collabStatus === 'accepted')
                            <button disabled class="btn flex cursor-default items-center gap-2 rounded-xl border border-green-200 bg-green-50 px-4 py-2 text-sm font-bold text-green-700">
                                <i class="bi bi-person-check-fill"></i>
                                <span>Connected</span>
                            </button>
                        @elseif($collabStatus === 'pending')
                            <button disabled class="btn flex cursor-default items-center gap-2 rounded-xl border border-orange-200 bg-orange-50 px-4 py-2 text-sm font-bold text-orange-700">
                                <i class="bi bi-clock-history"></i>
                                <span>Pending Collab</span>
                            </button>
                        @else
                            <form action="{{ route('collabs.store', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn btn-primary flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-bold transition hover:scale-105">
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
            <x-people.detail-row label="Major">{{ $user->major ?: '-' }}</x-people.detail-row>
            <x-people.detail-row label="Join UC Online">{{ $user->year_of_enrollment ?: '-' }}</x-people.detail-row>
            <x-people.detail-row label="Focus" class="capitalize">{{ $user->focus_label }}</x-people.detail-row>
        </div>

        {{-- --- Contact Details Block --- --}}
        @if ($user->hasContactDetails() && $user->contactsVisibleTo(auth()->user()))
            <div class="space-y-1.5 border-t border-slate-100 pt-4">
                @if ($user->whatsapp)
                    <x-people.contact-row :href="$user->whatsapp_link" icon="bi-whatsapp" label="WhatsApp" tone="green" new-tab>
                        {{ $user->whatsapp }}
                    </x-people.contact-row>
                @endif
                @if ($user->email)
                    <x-people.contact-row :href="'mailto:' . $user->email" icon="bi-envelope" label="Email" tone="blue">
                        {{ $user->email }}
                    </x-people.contact-row>
                @endif
                @if ($user->linkedin)
                    <x-people.contact-row :href="$user->linkedin_url" icon="bi-linkedin" label="LinkedIn" tone="indigo" new-tab>
                        {{ $user->linkedin }}
                    </x-people.contact-row>
                @endif
            </div>
        @elseif ($user->hasContactDetails())
            <div class="border-t border-slate-100 pt-4">
                <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 text-center">
                    <p class="text-xs font-bold leading-relaxed text-gray-600">
                        <i class="bi bi-lock-fill mr-1"></i> Contact details hidden by user
                    </p>
                </div>
            </div>
        @endif

        {{-- --- Core Competencies Block --- --}}
        @if ($user->skills->isNotEmpty())
            <div class="space-y-2 border-t border-slate-100 pt-4">
                <div class="text-xs font-bold text-gray-600">Core Competencies</div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($user->skills as $skill)
                        <span class="rounded-md border border-slate-200/40 bg-slate-50 px-2 py-0.5 text-xs font-bold text-slate-600">
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
