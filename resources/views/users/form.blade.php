<x-app-layout>
    @push('styles')
        @vite('resources/css/pages/user-form.css')
    @endpush

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ $userToEdit ? route('users.show', $userToEdit) : route('users.index') }}" class="btn-uco btn-uco-secondary">
            <i class="bi bi-arrow-left"></i>
            Back
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $userToEdit ? 'Edit User' : 'Create New User' }}</h1>
            <p class="text-sm text-gray-600">{{ $userToEdit ? 'Update user information' : 'Add a new user' }} strictly aligned with the new database columns</p>
        </div>
    </div>

    <form data-user-form data-enter-blurs method="POST" action="{{ $userToEdit ? route('users.update', $userToEdit) : route('users.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if ($userToEdit)
            @method('PUT')
        @endif

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <h2 class="mb-6 border-b-2 border-gray-100 pb-3 text-lg font-bold text-gray-900">
                Account & Auth Info
            </h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-forms.field look="user" name="name" label="Full Name" required>
                    <input type="text" name="name" id="name" value="{{ old('name', $userToEdit?->name) }}" required
                           class="form-input-custom @error('name') border-red-500 @enderror">
                </x-forms.field>

                <x-forms.field look="user" name="email" label="Email Address" required>
                    <input type="email" name="email" id="email" value="{{ old('email', $userToEdit?->email) }}" required
                           class="form-input-custom @error('email') border-red-500 @enderror">
                </x-forms.field>

                <x-forms.field look="user" name="role" label="Role" required>
                    <select name="role" id="role" required class="block w-full">
                        <option value="user" {{ old('role', $userToEdit?->role) === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $userToEdit?->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </x-forms.field>

                <x-forms.field look="user" name="student_status" label="Student Status" required id="student_status_container">
                    <select name="student_status" id="student_status" data-tom-select="Select Status" required class="block w-full">
                        <option value="student aktif" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'student aktif' ? 'selected' : '' }}>Student Aktif</option>
                        <option value="student non aktif" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'student non aktif' ? 'selected' : '' }}>Student Non Aktif</option>
                        <option value="student cuti" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'student cuti' ? 'selected' : '' }}>Student Cuti</option>
                        <option value="alumni aktif" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'alumni aktif' ? 'selected' : '' }}>Alumni Aktif</option>
                        <option value="alumni non aktif" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'alumni non aktif' ? 'selected' : '' }}>Alumni Non Aktif</option>
                        <option value="alumni cuti" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'alumni cuti' ? 'selected' : '' }}>Alumni Cuti</option>
                    </select>
                </x-forms.field>

                <x-forms.field look="user" name="password" :label="$userToEdit ? 'Password (leave blank to keep existing)' : 'Password'" :required="!$userToEdit">
                    <input type="password" name="password" id="password" @unless ($userToEdit) required @endunless
                           class="form-input-custom @error('password') border-red-500 @enderror">
                </x-forms.field>

                <x-forms.field look="user" name="password_confirmation" label="Confirm Password" :required="!$userToEdit" :show-error="false">
                    <input type="password" name="password_confirmation" id="password_confirmation" @unless ($userToEdit) required @endunless
                           class="form-input-custom">
                </x-forms.field>
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm" id="identity_contact_section">
            <h2 class="mb-6 border-b-2 border-gray-100 pb-3 text-lg font-bold text-gray-900">
                Identity & Contact
            </h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-forms.field look="user" name="prefix_title" label="Prefix Title" :show-error="false">
                    <input type="text" name="prefix_title" id="prefix_title" value="{{ old('prefix_title', $userToEdit?->prefix_title) }}"
                           class="form-input-custom">
                </x-forms.field>

                <x-forms.field look="user" name="suffix_title" label="Suffix Title" :show-error="false">
                    <input type="text" name="suffix_title" id="suffix_title" value="{{ old('suffix_title', $userToEdit?->suffix_title) }}"
                           class="form-input-custom">
                </x-forms.field>

                <x-forms.field look="user" name="personal_email" label="Personal Email">
                    <input type="email" name="personal_email" id="personal_email" value="{{ old('personal_email', $userToEdit?->personal_email) }}"
                           class="form-input-custom @error('personal_email') border-red-500 @enderror">
                </x-forms.field>

                <x-forms.field look="user" name="phone_number" label="Phone Number" :show-error="false">
                    <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number', $userToEdit?->phone_number) }}"
                           class="form-input-custom">
                </x-forms.field>

                <x-forms.field look="user" name="mobile_number" label="Mobile Number" :show-error="false">
                    <input type="tel" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $userToEdit?->mobile_number) }}"
                           class="form-input-custom">
                </x-forms.field>

                <x-forms.field look="user" name="whatsapp" label="WhatsApp Number" :show-error="false">
                    <input type="tel" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $userToEdit?->whatsapp) }}"
                           class="form-input-custom">
                </x-forms.field>

                <x-forms.field look="user" name="linkedin" label="LinkedIn URL" :show-error="false">
                    <input type="url" name="linkedin" id="linkedin" value="{{ old('linkedin', $userToEdit?->linkedin) }}"
                           class="form-input-custom">
                </x-forms.field>

                {{-- --- Profile Photo --- --}}
                @if ($userToEdit)
                    <x-forms.removable-image label="Profile Photo" name="profile_photo_url" delete-name="delete_profile_photo_url"
                                             :url="$userToEdit->profile_photo_url" :initial="strtoupper(substr($userToEdit->name, 0, 1))" noun="Photo"
                                             hint="JPG, PNG, WEBP — Max 5MB" box-class="w-[110px] h-[140px] group" actions-class="h-[140px]">
                        @error('profile_photo_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </x-forms.removable-image>
                @else
                    <x-forms.file-drop label="Profile Photo" name="profile_photo_url" accept="image/*"
                                       prompt="Upload or drop a photo" formats="JPG, PNG, WEBP">
                        <x-slot:selection>
                            <div x-show="selected" class="mb-3 flex items-center gap-4">
                                <div class="h-20 w-16 flex-shrink-0 overflow-hidden rounded-sm">
                                    <img :src="fileUrl" class="h-full w-full object-contain">
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500" x-text="fileName">Selected Photo</span>
                                    <button type="button" @click="clear()"
                                            class="inline-flex items-center gap-1.5 text-xs font-bold text-red-500 transition-all hover:text-red-700">
                                        <i class="bi bi-x-circle text-sm"></i>
                                        <span>Clear Selection</span>
                                    </button>
                                </div>
                            </div>
                        </x-slot:selection>
                        @error('profile_photo_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </x-forms.file-drop>
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm" id="academic_extra_section">
            <h2 class="mb-6 border-b-2 border-gray-100 pb-3 text-lg font-bold text-gray-900">
                Academic & Extra
            </h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-forms.field look="user" name="nis" label="NIS (Student ID)" :show-error="false">
                    <input type="text" name="nis" id="nis" value="{{ old('nis', $userToEdit?->nis) }}"
                           class="form-input-custom">
                </x-forms.field>

                <x-forms.field look="user" name="year_of_enrollment" label="Year of Enrollment" :show-error="false">
                    <input type="text" name="year_of_enrollment" id="year_of_enrollment" value="{{ old('year_of_enrollment', $userToEdit?->year_of_enrollment) }}"
                           class="form-input-custom" placeholder="e.g. 2023">
                </x-forms.field>

                <x-forms.field look="user" name="graduate_year" label="Graduate Year" :show-error="false">
                    <input type="text" name="graduate_year" id="graduate_year" value="{{ old('graduate_year', $userToEdit?->graduate_year) }}"
                           class="form-input-custom" placeholder="e.g. 2027">
                </x-forms.field>

                <x-forms.field look="user" name="major" :label="$userToEdit ? 'Peminatan' : 'Major'" :show-error="false">
                    <input type="text" name="major" id="major" value="{{ old('major', $userToEdit?->major) }}"
                           class="form-input-custom">
                </x-forms.field>

                <x-forms.field look="user" name="current_status" label="Current Career Status" :show-error="false">
                    <select name="current_status" id="current_status" data-tom-select="Select Career Status" class="block w-full">
                        <option value="">Select Status</option>
                        <option value="Entrepreneur" {{ old('current_status', $userToEdit?->current_status) == 'Entrepreneur' ? 'selected' : '' }}>Entrepreneur</option>
                        <option value="Intrapreneur" {{ old('current_status', $userToEdit?->current_status) == 'Intrapreneur' ? 'selected' : '' }}>Intrapreneur</option>
                    </select>
                </x-forms.field>

                {{-- --- Activities Documentation Files --- --}}
                @if ($userToEdit)
                    <div class="relative md:col-span-2" x-data="activityFiles(@js($userToEdit->activity_files))">
                        <div class="mb-3 flex items-center justify-between">
                            <label class="form-label-custom mb-0">Activities Documentation Files</label>
                            <div class="relative">
                                <input type="file" name="activities_docs[]" id="activities_docs" multiple accept="image/*,application/pdf" x-ref="activityInput"
                                       class="hidden"
                                       @change="handleNewFiles($event)">
                                <button type="button" @click="pickFiles()"
                                        class="inline-flex cursor-pointer select-none items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-[10px] font-extrabold text-blue-600 shadow-sm transition hover:bg-blue-100 hover:text-blue-700">
                                    <i class="bi bi-plus-lg text-[9px]"></i>
                                    <span>Add Files</span>
                                </button>
                            </div>
                        </div>

                        <x-files.activity-previews />
                    </div>
                    @error('activities_docs')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
            </div>
        @else
            <x-forms.file-drop label="Activities Documentation File" name="activities_doc_url" accept=".pdf"
                               prompt="Upload or drop a file" formats="PDF ONLY" icon="bi-folder2-open">
                <x-slot:selection>
                    <div x-show="selected"
                         class="mb-3 flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-3.5 shadow-sm">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl border border-red-100 bg-red-50 text-red-500">
                                <i class="bi bi-file-earmark-pdf-fill text-xl"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="max-w-[240px] truncate text-xs font-bold text-slate-700" x-text="fileName">Selected File</p>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">PDF document selected</p>
                            </div>
                        </div>
                        <button type="button" @click="clear()"
                                class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-red-500 shadow-sm transition-colors hover:text-red-700"
                                title="Clear Selection">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </x-slot:selection>
                @error('activities_doc_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </x-forms.file-drop>
            @endif

            <x-forms.field look="user" name="testimony" label="Student Testimony" :show-error="false" class="md:col-span-2">
                <textarea name="testimony" id="testimony" rows="3"
                          class="form-textarea-custom">{{ old('testimony', $userToEdit?->testimony) }}</textarea>
            </x-forms.field>

            <x-forms.field look="user" name="activities_caption" label="Activities Caption" :show-error="false" class="md:col-span-2">
                <input type="text" name="activities_caption" id="activities_caption" value="{{ old('activities_caption', $userToEdit?->activities_caption) }}"
                       class="form-input-custom" placeholder="e.g. Brief caption describing student activities">
            </x-forms.field>
        </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm" id="business_assignment_section">
            <h2 class="mb-6 border-b-2 border-gray-100 pb-3 text-lg font-bold text-gray-900">
                Business Transfers & Assignments
            </h2>
            <div class="grid grid-cols-1 gap-6">
                <x-forms.field look="user" name="owned_businesses" label="Owned Businesses to assign directly:" :show-error="false">
                    <select name="owned_businesses[]" id="owned_businesses" data-tom-select="Select Businesses" multiple class="block w-full">
                        @foreach ($availableBusinesses as $b)
                            <option value="{{ $b->id }}" {{ in_array($b->id, old('owned_businesses', $ownedBusinessIds)) ? 'selected' : '' }}>
                                {{ $b->name }} (Current owner: {{ $b->user->name ?? 'None' }})
                            </option>
                        @endforeach
                    </select>
                </x-forms.field>
            </div>
        </div>

        {{-- --- Action Buttons --- --}}
        <div class="flex items-center justify-between pb-10">
            <a href="{{ $userToEdit ? route('users.show', $userToEdit) : route('users.index') }}" class="btn-uco btn-uco-neutral">
                Cancel
            </a>
            <button type="submit" class="btn-uco btn-uco-primary">
                <i class="bi {{ $userToEdit ? 'bi-check-circle-fill' : 'bi-person-plus-fill' }}"></i>
                {{ $userToEdit ? 'Update' : 'Create' }} User
            </button>
        </div>
    </form>
</x-app-layout>
