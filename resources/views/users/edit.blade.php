<x-app-layout>
    @push('styles')
        @vite('resources/css/pages/user-form.css')
    @endpush

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('users.show', $userToEdit) }}" class="btn-uco btn-uco-secondary">
            <i class="bi bi-arrow-left"></i>
            Back
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
            <p class="text-sm text-gray-600">Update user information strictly aligned with the new database columns</p>
        </div>
    </div>

    <form data-user-form data-enter-blurs method="POST" action="{{ route('users.update', $userToEdit) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Account & Auth Info
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Full Name --}}
                <div>
                    <label for="name" class="form-label-custom">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $userToEdit->name) }}" required
                           class="form-input-custom @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Email Address --}}
                <div>
                    <label for="email" class="form-label-custom">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email', $userToEdit->email) }}" required
                           class="form-input-custom @error('email') border-red-500 @enderror">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="form-label-custom">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select name="role" id="role" required class="block w-full">
                        <option value="user" {{ old('role', $userToEdit->role) === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $userToEdit->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Student Status --}}
                <div id="student_status_container">
                    <label for="student_status" class="form-label-custom">
                        Student Status <span class="text-red-500">*</span>
                    </label>
                    <select name="student_status" id="student_status" data-tom-select="Select Status" required class="block w-full">
                        <option value="student aktif" {{ old('student_status', $userToEdit->student_status) === 'student aktif' ? 'selected' : '' }}>Student Aktif</option>
                        <option value="student non aktif" {{ old('student_status', $userToEdit->student_status) === 'student non aktif' ? 'selected' : '' }}>Student Non Aktif</option>
                        <option value="student cuti" {{ old('student_status', $userToEdit->student_status) === 'student cuti' ? 'selected' : '' }}>Student Cuti</option>
                        <option value="alumni aktif" {{ old('student_status', $userToEdit->student_status) === 'alumni aktif' ? 'selected' : '' }}>Alumni Aktif</option>
                        <option value="alumni non aktif" {{ old('student_status', $userToEdit->student_status) === 'alumni non aktif' ? 'selected' : '' }}>Alumni Non Aktif</option>
                        <option value="alumni cuti" {{ old('student_status', $userToEdit->student_status) === 'alumni cuti' ? 'selected' : '' }}>Alumni Cuti</option>
                    </select>
                    @error('student_status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="form-label-custom">
                        Password (leave blank to keep existing)
                    </label>
                    <input type="password" name="password" id="password"
                           class="form-input-custom @error('password') border-red-500 @enderror">
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Password Confirmation --}}
                <div>
                    <label for="password_confirmation" class="form-label-custom">
                        Confirm Password
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-input-custom">
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100" id="identity_contact_section">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Identity & Contact
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Prefix Title --}}
                <div>
                    <label for="prefix_title" class="form-label-custom">Prefix Title</label>
                    <input type="text" name="prefix_title" id="prefix_title" value="{{ old('prefix_title', $userToEdit->prefix_title) }}"
                           class="form-input-custom">
                </div>

                {{-- Suffix Title --}}
                <div>
                    <label for="suffix_title" class="form-label-custom">Suffix Title</label>
                    <input type="text" name="suffix_title" id="suffix_title" value="{{ old('suffix_title', $userToEdit->suffix_title) }}"
                           class="form-input-custom">
                </div>

                {{-- Personal Email --}}
                <div>
                    <label for="personal_email" class="form-label-custom">Personal Email</label>
                    <input type="email" name="personal_email" id="personal_email" value="{{ old('personal_email', $userToEdit->personal_email) }}"
                           class="form-input-custom @error('personal_email') border-red-500 @enderror">
                    @error('personal_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Phone Number --}}
                <div>
                    <label for="phone_number" class="form-label-custom">Phone Number</label>
                    <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number', $userToEdit->phone_number) }}"
                           class="form-input-custom">
                </div>

                {{-- Mobile Number --}}
                <div>
                    <label for="mobile_number" class="form-label-custom">Mobile Number</label>
                    <input type="tel" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $userToEdit->mobile_number) }}"
                           class="form-input-custom">
                </div>

                {{-- WhatsApp --}}
                <div>
                    <label for="whatsapp" class="form-label-custom">WhatsApp Number</label>
                    <input type="tel" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $userToEdit->whatsapp) }}"
                           class="form-input-custom">
                </div>

                {{-- LinkedIn --}}
                <div>
                    <label for="linkedin" class="form-label-custom">LinkedIn URL</label>
                    <input type="url" name="linkedin" id="linkedin" value="{{ old('linkedin', $userToEdit->linkedin) }}"
                           class="form-input-custom">
                </div>

                {{-- Profile Photo --}}
                <x-forms.removable-image label="Profile Photo" name="profile_photo_url" delete-name="delete_profile_photo_url"
                    :url="$userToEdit->profile_photo_url" :initial="strtoupper(substr($userToEdit->name, 0, 1))" noun="Photo"
                    hint="JPG, PNG, WEBP — Max 5MB" box-class="w-[110px] h-[140px] group" actions-class="h-[140px]">
                    @error('profile_photo_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </x-forms.removable-image>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100" id="academic_extra_section">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Academic & Extra
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- NIS --}}
                <div>
                    <label for="nis" class="form-label-custom">NIS (Student ID)</label>
                    <input type="text" name="nis" id="nis" value="{{ old('nis', $userToEdit->nis) }}"
                           class="form-input-custom">
                </div>

                {{-- Year of Enrollment --}}
                <div>
                    <label for="year_of_enrollment" class="form-label-custom">Year of Enrollment</label>
                    <input type="text" name="year_of_enrollment" id="year_of_enrollment" value="{{ old('year_of_enrollment', $userToEdit->year_of_enrollment) }}"
                           class="form-input-custom" placeholder="e.g. 2023">
                </div>

                {{-- Graduate Year --}}
                <div>
                    <label for="graduate_year" class="form-label-custom">Graduate Year</label>
                    <input type="text" name="graduate_year" id="graduate_year" value="{{ old('graduate_year', $userToEdit->graduate_year) }}"
                           class="form-input-custom" placeholder="e.g. 2027">
                </div>

                {{-- Major --}}
                <div>
                    <label for="major" class="form-label-custom">Peminatan</label>
                    <input type="text" name="major" id="major" value="{{ old('major', $userToEdit->major) }}"
                           class="form-input-custom">
                </div>

                {{-- Current Status --}}
                <div>
                    <label for="current_status" class="form-label-custom">Current Career Status</label>
                    <select name="current_status" id="current_status" data-tom-select="Select Career Status" class="block w-full">
                        <option value="">Select Status</option>
                        <option value="Entrepreneur" {{ old('current_status', $userToEdit->current_status) == 'Entrepreneur' ? 'selected' : '' }}>Entrepreneur</option>
                        <option value="Intrapreneur" {{ old('current_status', $userToEdit->current_status) == 'Intrapreneur' ? 'selected' : '' }}>Intrapreneur</option>
                    </select>
                </div>                {{-- Activities Documentation Files --}}
                <div class="md:col-span-2 relative" x-data="activityFiles(@js($userToEdit->activity_files))">
                    <div class="flex items-center justify-between mb-3">
                        <label class="form-label-custom mb-0">Activities Documentation Files</label>
                        <div class="relative">
                            <input type="file" name="activities_docs[]" id="activities_docs" multiple accept="image/*,application/pdf" x-ref="activityInput"
                                   class="hidden"
                                   @change="handleNewFiles($event)">
                            <button type="button" @click="pickFiles()"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-600 hover:text-blue-700 text-[10px] font-extrabold rounded-lg transition shadow-sm select-none cursor-pointer">
                                <i class="bi bi-plus-lg text-[9px]"></i>
                                <span>Add Files</span>
                            </button>
                        </div>
                    </div>
                    
                    <x-files.activity-previews />
                    </div>
                    @error('activities_docs')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Testimony --}}
                <div class="md:col-span-2">
                    <label for="testimony" class="form-label-custom">Student Testimony</label>
                    <textarea name="testimony" id="testimony" rows="3"
                               class="form-textarea-custom">{{ old('testimony', $userToEdit->testimony) }}</textarea>
                </div>

                {{-- Activities Caption --}}
                <div class="md:col-span-2">
                    <label for="activities_caption" class="form-label-custom">Activities Caption</label>
                    <input type="text" name="activities_caption" id="activities_caption" value="{{ old('activities_caption', $userToEdit->activities_caption) }}"
                           class="form-input-custom" placeholder="e.g. Brief caption describing student activities">
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100" id="business_assignment_section">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Business Transfers & Assignments
            </h2>
            <div class="grid grid-cols-1 gap-6">
                {{-- Transfer Owned Businesses --}}
                <div>
                    <label for="owned_businesses" class="form-label-custom">
                        Owned Businesses to assign directly:
                    </label>
                    <select name="owned_businesses[]" id="owned_businesses" data-tom-select="Select Businesses" multiple class="block w-full">
                        @foreach ($availableBusinesses as $b)
                            <option value="{{ $b->id }}" {{ in_array($b->id, old('owned_businesses', $userToEdit->businesses->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                                {{ $b->name }} (Current owner: {{ $b->user->name ?? 'None' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-between pb-10">
            <a href="{{ route('users.show', $userToEdit) }}" class="btn-uco btn-uco-neutral">
                Cancel
            </a>
            <button type="submit" class="btn-uco btn-uco-primary">
                <i class="bi bi-check-circle-fill"></i>
                Update User
            </button>
        </div>
    </form>
</x-app-layout>