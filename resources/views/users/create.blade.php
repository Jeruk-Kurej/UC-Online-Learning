<x-app-layout>
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css" rel="stylesheet">
        <style>
            .form-label-custom {
                display: block; 
                font-size: 10px; 
                font-weight: 800; 
                color: #64748b; 
                text-transform: uppercase; 
                margin-bottom: 8px;
                letter-spacing: 0.5px;
            }

            .form-input-custom {
                width: 100%; 
                height: 44px; 
                padding: 0 15px; 
                border: 1.5px solid #e2e8f0; 
                border-radius: 7px; 
                font-size: 13px; 
                font-weight: 600; 
                outline: none; 
                box-sizing: border-box;
                transition: all 0.2s ease;
                background: white;
            }

            .form-input-custom:focus {
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
            }

            .form-file-container-custom {
                position: relative; 
                display: flex; 
                align-items: center; 
                justify-content: space-between; 
                padding: 0 15px; 
                height: 44px;
                border: 1.5px dashed #cbd5e1; 
                border-radius: 7px; 
                background: #f8fafc; 
                transition: all 0.2s ease;
                cursor: pointer;
                overflow: hidden;
            }

            .form-file-container-custom:hover {
                border-color: #3b82f6;
                background: white;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.05);
            }

            .form-textarea-custom {
                width: 100%; 
                padding: 12px 15px; 
                border: 1.5px solid #e2e8f0; 
                border-radius: 7px; 
                font-size: 13px; 
                font-weight: 600; 
                outline: none; 
                box-sizing: border-box;
                transition: all 0.2s ease;
                min-height: 100px;
            }

            .form-textarea-custom:focus {
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
            }

            /* TomSelect Overrides */
            .ts-wrapper .ts-control {
                border: 1.5px solid #e2e8f0 !important;
                border-radius: 7px !important;
                height: 44px !important;
                padding: 0 15px !important;
                display: flex !important;
                align-items: center !important;
                font-size: 13px !important;
                font-weight: 600 !important;
                box-shadow: none !important;
            }

            .ts-wrapper.focus .ts-control {
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
            }
        </style>
    @endpush

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('users.index') }}" 
           class="group inline-flex items-center gap-2.5 px-4 py-2.5 bg-white hover:bg-gray-900 border border-gray-200 hover:border-gray-900 text-gray-700 hover:text-white rounded-xl font-medium text-sm shadow-sm hover:shadow-md transition-all duration-200">
            <i class="bi bi-arrow-left text-base group-hover:-translate-x-0.5 transition-transform duration-200"></i>
            <span>Back</span>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Create New User</h1>
            <p class="text-sm text-gray-600">Add a new user strictly aligned with the new database columns</p>
        </div>
    </div>

    <form method="POST" action="/users" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Account & Auth Info
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Email --}}
                <div>
                    <label for="email" class="form-label-custom">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="form-input-custom @error('email') border-red-500 @enderror">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="form-label-custom">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select name="role" id="role" required class="block w-full">
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="form-label-custom">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" id="password" required
                           class="form-input-custom @error('password') border-red-500 @enderror">
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Password Confirmation --}}
                <div>
                    <label for="password_confirmation" class="form-label-custom">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="form-input-custom">
                </div>

                {{-- Student Status --}}
                <div>
                    <label for="student_status" class="form-label-custom">
                        Student Status <span class="text-red-500">*</span>
                    </label>
                    <select name="student_status" id="student_status" required class="block w-full">
                        <option value="student aktif" {{ old('student_status', 'student aktif') === 'student aktif' ? 'selected' : '' }}>Student Aktif</option>
                        <option value="student non aktif" {{ old('student_status') === 'student non aktif' ? 'selected' : '' }}>Student Non Aktif</option>
                        <option value="student cuti" {{ old('student_status') === 'student cuti' ? 'selected' : '' }}>Student Cuti</option>
                        <option value="alumni aktif" {{ old('student_status') === 'alumni aktif' ? 'selected' : '' }}>Alumni Aktif</option>
                        <option value="alumni non aktif" {{ old('student_status') === 'alumni non aktif' ? 'selected' : '' }}>Alumni Non Aktif</option>
                        <option value="alumni cuti" {{ old('student_status') === 'alumni cuti' ? 'selected' : '' }}>Alumni Cuti</option>
                    </select>
                    @error('student_status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>


            </div>
        </div>

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Identity & Contact
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Prefix Title --}}
                <div>
                    <label for="prefix_title" class="form-label-custom">Prefix Title</label>
                    <input type="text" name="prefix_title" id="prefix_title" value="{{ old('prefix_title') }}"
                           class="form-input-custom">
                </div>

                {{-- Full Name --}}
                <div>
                    <label for="name" class="form-label-custom">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="form-input-custom @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Suffix Title --}}
                <div>
                    <label for="suffix_title" class="form-label-custom">Suffix Title</label>
                    <input type="text" name="suffix_title" id="suffix_title" value="{{ old('suffix_title') }}"
                           class="form-input-custom">
                </div>

                {{-- Personal Email --}}
                <div>
                    <label for="personal_email" class="form-label-custom">Personal Email</label>
                    <input type="email" name="personal_email" id="personal_email" value="{{ old('personal_email') }}"
                           class="form-input-custom @error('personal_email') border-red-500 @enderror">
                    @error('personal_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Phone Number --}}
                <div>
                    <label for="phone_number" class="form-label-custom">Phone Number</label>
                    <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                           class="form-input-custom">
                </div>

                {{-- Mobile Number --}}
                <div>
                    <label for="mobile_number" class="form-label-custom">Mobile Number</label>
                    <input type="tel" name="mobile_number" id="mobile_number" value="{{ old('mobile_number') }}"
                           class="form-input-custom">
                </div>

                {{-- WhatsApp --}}
                <div>
                    <label for="whatsapp" class="form-label-custom">WhatsApp Number</label>
                    <input type="tel" name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}"
                           class="form-input-custom">
                </div>

                {{-- LinkedIn --}}
                <div>
                    <label for="linkedin" class="form-label-custom">LinkedIn URL</label>
                    <input type="url" name="linkedin" id="linkedin" value="{{ old('linkedin') }}"
                           class="form-input-custom">
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Academic & Extra
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- NIS --}}
                <div>
                    <label for="nis" class="form-label-custom">NIS (Student ID)</label>
                    <input type="text" name="nis" id="nis" value="{{ old('nis') }}"
                           class="form-input-custom">
                </div>

                {{-- Year of Enrollment --}}
                <div>
                    <label for="year_of_enrollment" class="form-label-custom">Year of Enrollment</label>
                    <input type="text" name="year_of_enrollment" id="year_of_enrollment" value="{{ old('year_of_enrollment') }}"
                           class="form-input-custom" placeholder="e.g. 2023">
                </div>

                {{-- Graduate Year --}}
                <div>
                    <label for="graduate_year" class="form-label-custom">Graduate Year</label>
                    <input type="text" name="graduate_year" id="graduate_year" value="{{ old('graduate_year') }}"
                           class="form-input-custom" placeholder="e.g. 2027">
                </div>

                {{-- Major --}}
                <div>
                    <label for="major" class="form-label-custom">Major</label>
                    <input type="text" name="major" id="major" value="{{ old('major') }}"
                           class="form-input-custom">
                </div>

                {{-- Current Status --}}
                <div>
                    <label for="current_status" class="form-label-custom">Current Career Status</label>
                    <select name="current_status" id="current_status" class="block w-full">
                        <option value="">Select Status</option>
                        <option value="Entrepreneur" {{ old('current_status') == 'Entrepreneur' ? 'selected' : '' }}>Entrepreneur</option>
                        <option value="Intrapreneur" {{ old('current_status') == 'Intrapreneur' ? 'selected' : '' }}>Intrapreneur</option>
                    </select>
                </div>

                {{-- Profile Photo --}}
                <div class="relative">
                    <label for="profile_photo_url" class="form-label-custom">Profile Photo</label>
                    <div class="form-file-container-custom group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-blue-500 transition-all">
                                <i class="bi bi-cloud-upload text-base"></i>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900 transition-colors block leading-tight" id="profile_photo_label">Upload or drop a file</span>
                                <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">JPG, PNG, WEBP</span>
                            </div>
                        </div>
                        <input type="file" name="profile_photo_url" id="profile_photo_url" accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               onchange="document.getElementById('profile_photo_label').textContent = this.files[0]?.name || 'Upload or drop a file';">
                    </div>
                    @error('profile_photo_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- CV File --}}
                <div class="relative">
                    <label for="cv_url" class="form-label-custom">CV / Resume File</label>
                    <div class="form-file-container-custom group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-blue-500 transition-all">
                                <i class="bi bi-file-earmark-pdf text-base"></i>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900 transition-colors block leading-tight" id="cv_label">Upload or drop a file</span>
                                <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">PDF ONLY</span>
                            </div>
                        </div>
                        <input type="file" name="cv_url" id="cv_url" accept=".pdf"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               onchange="document.getElementById('cv_label').textContent = this.files[0]?.name || 'Upload or drop a file';">
                    </div>
                    @error('cv_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Activities Documentation File --}}
                <div class="relative">
                    <label for="activities_doc_url" class="form-label-custom">Activities Documentation File</label>
                    <div class="form-file-container-custom group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-blue-500 transition-all">
                                <i class="bi bi-folder2-open text-base"></i>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900 transition-colors block leading-tight" id="activities_label">Upload or drop a file</span>
                                <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">PDF ONLY</span>
                            </div>
                        </div>
                        <input type="file" name="activities_doc_url" id="activities_doc_url" accept=".pdf"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               onchange="document.getElementById('activities_label').textContent = this.files[0]?.name || 'Upload or drop a file';">
                    </div>
                    @error('activities_doc_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Testimony --}}
                <div class="md:col-span-2">
                    <label for="testimony" class="form-label-custom">Student Testimony</label>
                    <textarea name="testimony" id="testimony" rows="3"
                              class="form-textarea-custom">{{ old('testimony') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Business Transfers & Assignments
            </h2>
            <div class="grid grid-cols-1 gap-6">
                {{-- Transfer Owned Businesses --}}
                <div>
                    <label for="owned_businesses" class="form-label-custom">
                        Owned Businesses to assign directly:
                    </label>
                    <select name="owned_businesses[]" id="owned_businesses" multiple class="block w-full">
                        @foreach ($availableBusinesses as $b)
                            <option value="{{ $b->id }}" {{ in_array($b->id, old('owned_businesses', [])) ? 'selected' : '' }}>
                                {{ $b->name }} (Current owner: {{ $b->user->name ?? 'None' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-between pb-10">
            <a href="{{ route('users.index') }}" class="inline-flex items-center px-6 py-3 text-sm font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 hover:text-gray-900 rounded-xl transition-all duration-200">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center gap-2.5 px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl shadow-lg shadow-emerald-200 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                <i class="bi bi-person-plus-fill text-lg"></i>
                <span>Create User</span>
            </button>
        </div>
    </form>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.TomSelect) {
                    new TomSelect('#role', { create: false, placeholder: "Select Role", searchField: ["text"] });
                    new TomSelect('#student_status', { create: false, placeholder: "Select Status", searchField: ["text"] });
                    new TomSelect('#current_status', { create: false, placeholder: "Select Career Status", searchField: ["text"] });
                    new TomSelect('#owned_businesses', { create: false, placeholder: "Select Businesses", searchField: ["text"] });
                }

                // Standardized behavior: Enter key blurs the field instead of submitting
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') {
                        event.preventDefault();
                        event.target.blur();
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>