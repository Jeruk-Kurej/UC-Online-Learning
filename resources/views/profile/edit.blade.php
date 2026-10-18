<x-app-layout>
    @push('styles')
        @vite('resources/css/pages/profile-edit.css')
    @endpush

    <div class="settings-page">

        {{-- --- Header --- --}}
        <div class="profile-header">
            <div>
                <h1 class="settings-title">Profile Settings</h1>
                <p class="settings-subtitle">Manage your personal information and academic records.</p>
            </div>
        </div>

        <form data-enter-blurs method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="profile-layout">

                {{-- --- Sidebar --- --}}
                <div class="profile-sidebar">
                    <div class="profile-card">
                        <div x-data="removablePhoto(@js($user->profile_photo_url))" class="flex flex-col items-center">
                            <div class="photo-box mb-3 h-[180px] w-[140px]">
                                <img :src="previewUrl" class="photo-box__image" x-show="hasPhoto && !photoDeleted">

                                <div class="photo-box__initials uco-initials-bg-deep" x-show="!hasPhoto || photoDeleted">
                                    <span class="profile-initials">{{ $user->initials }}</span>
                                </div>

                                <label for="profile_photo" class="absolute inset-0 z-20 cursor-pointer">
                                    <input type="file" name="profile_photo" id="profile_photo" class="hidden" accept="image/*" x-ref="photoInput" @change="pick($event)">
                                </label>
                                <div class="photo-box__overlay">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                                        <circle cx="12" cy="13" r="4" />
                                    </svg>
                                    <span class="profile-photo-hint">Update</span>
                                </div>
                            </div>

                            <input type="hidden" name="delete_profile_photo" :value="photoDeleted ? '1' : '0'">

                            <template x-if="hasPhoto && !photoDeleted && !newPhotoSelected">
                                <button type="button" @click="photoDeleted = true"
                                        class="mb-2 flex items-center gap-1 text-xs font-bold text-red-500 transition-all hover:text-red-700">
                                    <i class="bi bi-trash3 text-sm"></i>
                                    <span>Remove Photo</span>
                                </button>
                            </template>

                            <template x-if="photoDeleted">
                                <button type="button" @click="photoDeleted = false"
                                        class="mb-2 flex items-center gap-1 text-xs font-bold text-blue-500 transition-all hover:text-blue-700">
                                    <i class="bi bi-arrow-counterclockwise text-sm"></i>
                                    <span>Undo Delete</span>
                                </button>
                            </template>

                            <template x-if="newPhotoSelected">
                                <button type="button" @click="cancelNew()"
                                        class="mb-2 flex items-center gap-1 text-xs font-bold text-amber-600 transition-all hover:text-amber-700">
                                    <i class="bi bi-x-circle text-sm"></i>
                                    <span>Cancel New Photo</span>
                                </button>
                            </template>
                        </div>
                        <h3 class="profile-name">{{ $user->prefix_title ? $user->prefix_title . ' ' : '' }}{{ $user->name }}{{ $user->suffix_title ? ', ' . $user->suffix_title : '' }}</h3>
                        <p class="profile-email">{{ $user->email }}</p>
                        <div class="profile-badges">
                            <span class="profile-badge">{{ $user->student_status ?? 'Active' }}</span>
                        </div>
                        <div class="profile-divider"></div>
                        <p class="profile-joined">Joined {{ $user->created_at->format('M Y') }}</p>
                    </div>
                </div>

                {{-- --- Sections --- --}}
                <div class="profile-sections">

                    <x-profile.section title="Identity Information">
                        <div class="profile-row mb-[25px] items-start">
                            <label class="profile-row-label mt-[15px]">Full Name</label>
                            <div class="profile-stack gap-3">
                                <x-profile.field compact label="Prefix Title (e.g. Prof. Dr.)" name="prefix_title" :value="$user->prefix_title" placeholder="Prefix" />
                                <x-profile.field compact label="Full Name" name="name" :value="$user->name" placeholder="Full Name" />
                                <x-profile.field compact label="Suffix Title (e.g. S.Kom, M.T.)" name="suffix_title" :value="$user->suffix_title" placeholder="Suffix" />
                            </div>
                        </div>
                        <div class="profile-row items-center">
                            <label class="profile-row-label">Personal Email</label>
                            <input type="email" name="personal_email" value="{{ old('personal_email', $user->personal_email) }}" class="profile-input profile-input-grow">
                        </div>
                    </x-profile.section>

                    <x-profile.section title="Contact Details">
                        <x-slot:actions>
                            <label class="group relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" name="show_contact_details" value="1" class="peer sr-only" {{ old('show_contact_details', $user->show_contact_details) ? 'checked' : '' }}>
                                <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300"></div>
                                <span class="ml-3 text-xs font-bold uppercase tracking-wider text-slate-600 transition-colors group-hover:text-slate-900">Show contacts publicly</span>
                            </label>
                        </x-slot:actions>
                        <div class="profile-grid">
                            <x-profile.field label="Phone Number" name="phone_number" :value="$user->phone_number" />
                            <x-profile.field label="Mobile Number" name="mobile_number" :value="$user->mobile_number" />
                            <x-profile.field label="WhatsApp" name="whatsapp" :value="$user->whatsapp" />
                            <x-profile.field label="LinkedIn Username" name="linkedin" :value="$user->linkedin" placeholder="e.g. johndoe" />
                        </div>
                    </x-profile.section>

                    <x-profile.section title="Academic Records">
                        <div class="profile-grid">
                            <x-profile.readonly-field label="Student ID (NIS)" :value="$user->nis" />
                            <div class="profile-select-field">
                                <label class="profile-label">Current Status</label>
                                <select name="current_status" class="profile-input profile-select">
                                    <option value="Entrepreneur" {{ old('current_status', $user->current_status) == 'Entrepreneur' ? 'selected' : '' }}>Entrepreneur</option>
                                    <option value="Intrapreneur" {{ old('current_status', $user->current_status) == 'Intrapreneur' ? 'selected' : '' }}>Intrapreneur</option>
                                </select>
                                <div class="profile-select-icon"><svg width="10" height="10" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.5 4.5L6 8L9.5 4.5" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></div>
                            </div>
                            <x-profile.field label="Enrollment Year" name="year_of_enrollment" :value="$user->year_of_enrollment" />
                            <x-profile.field label="Graduate Year" name="graduate_year" :value="$user->graduate_year" />
                        </div>
                        <x-profile.readonly-field class="mt-[25px]" label="Major" :value="$user->major" />
                    </x-profile.section>

                    <x-profile.section title="Additional Documents">
                        <div class="flex flex-col gap-5" x-data="activityFiles(@js($user->activity_files))">
                            <div class="profile-row items-start">
                                <div class="flex w-[140px] shrink-0 flex-col gap-2">
                                    <label class="profile-row-label mt-1">Activities Docs</label>
                                    <x-files.add-files name="activities_files" class="self-start" />
                                </div>
                                <div class="profile-stack gap-[15px]">
                                    <x-files.activity-previews blur-overlay />
                                </div>
                            </div>
                        </div>
                    </x-profile.section>
                </div>
            </div>

            {{-- --- Footer (spacer keeps the button aligned with the sections) --- --}}
            <div class="profile-footer">
                <div class="profile-footer-spacer"></div>
                <button type="submit" class="btn-uco btn-uco-primary">
                    <i class="bi bi-file-earmark-check-fill text-base"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
