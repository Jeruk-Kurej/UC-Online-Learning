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
        @if($userToEdit)
            @method('PUT')
        @endif

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Account & Auth Info
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- --- Full Name --- --}}
                <div>
                    <label for="name" class="form-label-custom">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $userToEdit?->name) }}" required
                           class="form-input-custom @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- --- Email Address --- --}}
                <div>
                    <label for="email" class="form-label-custom">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email', $userToEdit?->email) }}" required
                           class="form-input-custom @error('email') border-red-500 @enderror">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- --- Role --- --}}
                <div>
                    <label for="role" class="form-label-custom">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select name="role" id="role" required class="block w-full">
                        <option value="user" {{ old('role', $userToEdit?->role) === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $userToEdit?->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- --- Student Status --- --}}
                <div id="student_status_container">
                    <label for="student_status" class="form-label-custom">
                        Student Status <span class="text-red-500">*</span>
                    </label>
                    <select name="student_status" id="student_status" data-tom-select="Select Status" required class="block w-full">
                        <option value="student aktif" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'student aktif' ? 'selected' : '' }}>Student Aktif</option>
                        <option value="student non aktif" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'student non aktif' ? 'selected' : '' }}>Student Non Aktif</option>
                        <option value="student cuti" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'student cuti' ? 'selected' : '' }}>Student Cuti</option>
                        <option value="alumni aktif" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'alumni aktif' ? 'selected' : '' }}>Alumni Aktif</option>
                        <option value="alumni non aktif" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'alumni non aktif' ? 'selected' : '' }}>Alumni Non Aktif</option>
                        <option value="alumni cuti" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'alumni cuti' ? 'selected' : '' }}>Alumni Cuti</option>
                    </select>
                    @error('student_status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- --- Password --- --}}
                <div>
                    <label for="password" class="form-label-custom">
                        @if($userToEdit)
                            Password (leave blank to keep existing)
                        @else
                            Password <span class="text-red-500">*</span>
                        @endif
                    </label>
                    <input type="password" name="password" id="password" @unless($userToEdit) required @endunless
                           class="form-input-custom @error('password') border-red-500 @enderror">
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- --- Password Confirmation --- --}}
                <div>
                    <label for="password_confirmation" class="form-label-custom">
                        Confirm Password @unless($userToEdit) <span class="text-red-500">*</span> @endunless
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" @unless($userToEdit) required @endunless
                           class="form-input-custom">
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100" id="identity_contact_section">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Identity & Contact
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- --- Prefix Title --- --}}
                <div>
                    <label for="prefix_title" class="form-label-custom">Prefix Title</label>
                    <input type="text" name="prefix_title" id="prefix_title" value="{{ old('prefix_title', $userToEdit?->prefix_title) }}"
                           class="form-input-custom">
                </div>

                {{-- --- Suffix Title --- --}}
                <div>
                    <label for="suffix_title" class="form-label-custom">Suffix Title</label>
                    <input type="text" name="suffix_title" id="suffix_title" value="{{ old('suffix_title', $userToEdit?->suffix_title) }}"
                           class="form-input-custom">
                </div>

                {{-- --- Personal Email --- --}}
                <div>
                    <label for="personal_email" class="form-label-custom">Personal Email</label>
                    <input type="email" name="personal_email" id="personal_email" value="{{ old('personal_email', $userToEdit?->personal_email) }}"
                           class="form-input-custom @error('personal_email') border-red-500 @enderror">
                    @error('personal_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- --- Phone Number --- --}}
                <div>
                    <label for="phone_number" class="form-label-custom">Phone Number</label>
                    <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number', $userToEdit?->phone_number) }}"
                           class="form-input-custom">
                </div>

                {{-- --- Mobile Number --- --}}
                <div>
                    <label for="mobile_number" class="form-label-custom">Mobile Number</label>
                    <input type="tel" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $userToEdit?->mobile_number) }}"
                           class="form-input-custom">
                </div>

                {{-- --- WhatsApp --- --}}
                <div>
                    <label for="whatsapp" class="form-label-custom">WhatsApp Number</label>
                    <input type="tel" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $userToEdit?->whatsapp) }}"
                           class="form-input-custom">
                </div>

                {{-- --- LinkedIn --- --}}
                <div>
                    <label for="linkedin" class="form-label-custom">LinkedIn URL</label>
                    <input type="url" name="linkedin" id="linkedin" value="{{ old('linkedin', $userToEdit?->linkedin) }}"
                           class="form-input-custom">
                </div>

                {{-- --- Profile Photo --- --}}
                @if($userToEdit)
                    <x-forms.removable-image label="Profile Photo" name="profile_photo_url" delete-name="delete_profile_photo_url"
                        :url="$userToEdit->profile_photo_url" :initial="strtoupper(substr($userToEdit->name, 0, 1))" noun="Photo"
                        hint="JPG, PNG, WEBP — Max 5MB" box-class="w-[110px] h-[140px] group" actions-class="h-[140px]">
                        @error('profile_photo_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </x-forms.removable-image>
                @else
                    <x-forms.file-drop label="Profile Photo" name="profile_photo_url" accept="image/*"
                        prompt="Upload or drop a photo" formats="JPG, PNG, WEBP">
                        <x-slot:selection>
                            <div x-show="selected" class="flex items-center gap-4 mb-3">
                                <div class="w-16 h-20 rounded-sm overflow-hidden flex-shrink-0">
                                    <img :src="fileUrl" class="w-full h-full object-contain">
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider" x-text="fileName">Selected Photo</span>
                                    <button type="button" @click="clear()"
                                            class="inline-flex items-center gap-1.5 text-xs text-red-500 hover:text-red-700 font-bold transition-all">
                                        <i class="bi bi-x-circle text-sm"></i>
                                        <span>Clear Selection</span>
                                    </button>
                                </div>
                            </div>
                        </x-slot:selection>
                        @error('profile_photo_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </x-forms.file-drop>
                @endif
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100" id="academic_extra_section">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Academic & Extra
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- --- Nis --- --}}
                <div>
                    <label for="nis" class="form-label-custom">NIS (Student ID)</label>
                    <input type="text" name="nis" id="nis" value="{{ old('nis', $userToEdit?->nis) }}"
                           class="form-input-custom">
                </div>

                {{-- --- Year of Enrollment --- --}}
                <div>
                    <label for="year_of_enrollment" class="form-label-custom">Year of Enrollment</label>
                    <input type="text" name="year_of_enrollment" id="year_of_enrollment" value="{{ old('year_of_enrollment', $userToEdit?->year_of_enrollment) }}"
                           class="form-input-custom" placeholder="e.g. 2023">
                </div>

                {{-- --- Graduate Year --- --}}
                <div>
                    <label for="graduate_year" class="form-label-custom">Graduate Year</label>
                    <input type="text" name="graduate_year" id="graduate_year" value="{{ old('graduate_year', $userToEdit?->graduate_year) }}"
                           class="form-input-custom" placeholder="e.g. 2027">
                </div>

                {{-- --- Major --- --}}
                <div>
                    <label for="major" class="form-label-custom">{{ $userToEdit ? 'Peminatan' : 'Major' }}</label>
                    <input type="text" name="major" id="major" value="{{ old('major', $userToEdit?->major) }}"
                           class="form-input-custom">
                </div>

                {{-- --- Current Status --- --}}
                <div>
                    <label for="current_status" class="form-label-custom">Current Career Status</label>
                    <select name="current_status" id="current_status" data-tom-select="Select Career Status" class="block w-full">
                        <option value="">Select Status</option>
                        <option value="Entrepreneur" {{ old('current_status', $userToEdit?->current_status) == 'Entrepreneur' ? 'selected' : '' }}>Entrepreneur</option>
                        <option value="Intrapreneur" {{ old('current_status', $userToEdit?->current_status) == 'Intrapreneur' ? 'selected' : '' }}>Intrapreneur</option>
                    </select>
                </div>

                {{-- --- Activities Documentation Files --- --}}
                @if($userToEdit)
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
                @else
                    <x-forms.file-drop label="Activities Documentation File" name="activities_doc_url" accept=".pdf"
                        prompt="Upload or drop a file" formats="PDF ONLY" icon="bi-folder2-open">
                        <x-slot:selection>
                            <div x-show="selected"
                                 class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200 rounded-xl shadow-sm mb-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center flex-shrink-0 text-red-500">
                                        <i class="bi bi-file-earmark-pdf-fill text-xl"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-700 truncate max-w-[240px]" x-text="fileName">Selected File</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">PDF document selected</p>
                                    </div>
                                </div>
                                <button type="button" @click="clear()"
                                        class="flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-slate-200 text-red-500 hover:text-red-700 transition-colors shadow-sm"
                                        title="Clear Selection">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </x-slot:selection>
                        @error('activities_doc_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </x-forms.file-drop>
                @endif

                {{-- --- Testimony --- --}}
                <div class="md:col-span-2">
                    <label for="testimony" class="form-label-custom">Student Testimony</label>
                    <textarea name="testimony" id="testimony" rows="3"
                               class="form-textarea-custom">{{ old('testimony', $userToEdit?->testimony) }}</textarea>
                </div>

                {{-- --- Activities Caption --- --}}
                <div class="md:col-span-2">
                    <label for="activities_caption" class="form-label-custom">Activities Caption</label>
                    <input type="text" name="activities_caption" id="activities_caption" value="{{ old('activities_caption', $userToEdit?->activities_caption) }}"
                           class="form-input-custom" placeholder="e.g. Brief caption describing student activities">
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100" id="business_assignment_section">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Business Transfers & Assignments
            </h2>
            <div class="grid grid-cols-1 gap-6">
                {{-- --- Transfer Owned Businesses --- --}}
                <div>
                    <label for="owned_businesses" class="form-label-custom">
                        Owned Businesses to assign directly:
                    </label>
                    <select name="owned_businesses[]" id="owned_businesses" data-tom-select="Select Businesses" multiple class="block w-full">
                        @foreach ($availableBusinesses as $b)
                            <option value="{{ $b->id }}" {{ in_array($b->id, old('owned_businesses', $ownedBusinessIds)) ? 'selected' : '' }}>
                                {{ $b->name }} (Current owner: {{ $b->user->name ?? 'None' }})
                            </option>
                        @endforeach
                    </select>
                </div>
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