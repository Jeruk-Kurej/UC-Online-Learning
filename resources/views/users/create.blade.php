<x-app-layout>
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css" rel="stylesheet">
        <style>
            .ts-wrapper {
                width: 100% !important;
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box !important;
            }

            .ts-wrapper .ts-control {
                border: 1px solid #e2e8f0 !important;
                border-radius: 0.75rem !important;
                padding: 10px 16px !important; 
                min-height: 42px !important;
                width: 100% !important;
                box-sizing: border-box !important;
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
                background: white !important;
                display: flex !important;
                align-items: center !important;
            }

            .ts-wrapper.focus .ts-control {
                border-color: #111827 !important;
                box-shadow: 0 0 0 4px rgba(17, 24, 39, 0.05) !important;
                ring: none !important;
            }

            .ts-dropdown {
                background-color: white !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 1rem !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
                margin-top: 6px !important;
                padding: 6px !important;
                z-index: 1000 !important;
            }

            .ts-dropdown .option {
                padding: 8px 12px !important;
                font-size: 13px !important;
                color: #475569 !important;
                border-radius: 0.75rem !important;
                margin-bottom: 2px !important;
                transition: all 0.15s ease !important;
            }

            .ts-dropdown .option.active {
                background-color: #fff7ed !important;
                color: #f97316 !important;
                font-weight: 600 !important;
            }

            .ts-wrapper .ts-control>input {
                font-size: 14px !important;
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
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('email') border-red-500 @enderror">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
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
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" id="password" required
                           class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('password') border-red-500 @enderror">
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Password Confirmation --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900">
                </div>

                {{-- Student Status --}}
                <div>
                    <label for="student_status" class="block text-sm font-medium text-gray-700 mb-2">
                        Student Status <span class="text-red-500">*</span>
                    </label>
                    <select name="student_status" id="student_status" required class="block w-full">
                        <option value="active" {{ old('student_status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('student_status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="cuti" {{ old('student_status') === 'cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="alumni" {{ old('student_status') === 'alumni' ? 'selected' : '' }}>Alumni</option>
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
                    <label for="prefix_title" class="block text-sm font-medium text-gray-700 mb-2">Prefix Title</label>
                    <input type="text" name="prefix_title" id="prefix_title" value="{{ old('prefix_title') }}"
                           class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900">
                </div>

                {{-- Full Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Suffix Title --}}
                <div>
                    <label for="suffix_title" class="block text-sm font-medium text-gray-700 mb-2">Suffix Title</label>
                    <input type="text" name="suffix_title" id="suffix_title" value="{{ old('suffix_title') }}"
                           class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900">
                </div>

                {{-- Personal Email --}}
                <div>
                    <label for="personal_email" class="block text-sm font-medium text-gray-700 mb-2">Personal Email</label>
                    <input type="email" name="personal_email" id="personal_email" value="{{ old('personal_email') }}"
                           class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('personal_email') border-red-500 @enderror">
                    @error('personal_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Phone Number --}}
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                           class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900">
                </div>

                {{-- Mobile Number --}}
                <div>
                    <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                    <input type="tel" name="mobile_number" id="mobile_number" value="{{ old('mobile_number') }}"
                           class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900">
                </div>

                {{-- WhatsApp --}}
                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-2">WhatsApp Number</label>
                    <input type="tel" name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}"
                           class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900">
                </div>

                {{-- LinkedIn --}}
                <div>
                    <label for="linkedin" class="block text-sm font-medium text-gray-700 mb-2">LinkedIn URL</label>
                    <input type="url" name="linkedin" id="linkedin" value="{{ old('linkedin') }}"
                           class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900">
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
                    <label for="nis" class="block text-sm font-medium text-gray-700 mb-2">NIS (Student ID)</label>
                    <input type="text" name="nis" id="nis" value="{{ old('nis') }}"
                           class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900">
                </div>

                {{-- Year of Enrollment --}}
                <div>
                    <label for="year_of_enrollment" class="block text-sm font-medium text-gray-700 mb-2">Year of Enrollment</label>
                    <input type="text" name="year_of_enrollment" id="year_of_enrollment" value="{{ old('year_of_enrollment') }}"
                           class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900" placeholder="e.g. 2023">
                </div>

                {{-- Graduate Year --}}
                <div>
                    <label for="graduate_year" class="block text-sm font-medium text-gray-700 mb-2">Graduate Year</label>
                    <input type="text" name="graduate_year" id="graduate_year" value="{{ old('graduate_year') }}"
                           class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900" placeholder="e.g. 2027">
                </div>

                {{-- Major --}}
                <div>
                    <label for="major" class="block text-sm font-medium text-gray-700 mb-2">Major</label>
                    <input type="text" name="major" id="major" value="{{ old('major') }}"
                           class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900">
                </div>

                {{-- Current Status --}}
                <div>
                    <label for="current_status" class="block text-sm font-medium text-gray-700 mb-2">Current Career Status</label>
                    <input type="text" name="current_status" id="current_status" value="{{ old('current_status') }}"
                           class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900" placeholder="e.g. Entrepreneur, Intrapreneur">
                </div>

                {{-- Profile Photo --}}
                <div class="relative">
                    <label for="profile_photo_url" class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                    <div class="relative flex items-center justify-between px-4 py-2.5 border border-gray-200 rounded-xl hover:border-gray-900 transition-all cursor-pointer group bg-white shadow-sm hover:shadow-md">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-gray-900 group-hover:text-white transition-all">
                                <i class="bi bi-cloud-upload text-lg"></i>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-700 group-hover:text-gray-900 transition-colors block" id="profile_photo_label">Upload or drop a file</span>
                                <span class="text-xs text-gray-400">Accepted formats: JPG, PNG, WEBP</span>
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
                    <label for="cv_url" class="block text-sm font-medium text-gray-700 mb-2">CV / Resume File</label>
                    <div class="relative flex items-center justify-between px-4 py-2.5 border border-gray-200 rounded-xl hover:border-gray-900 transition-all cursor-pointer group bg-white shadow-sm hover:shadow-md">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-gray-900 group-hover:text-white transition-all">
                                <i class="bi bi-cloud-upload text-lg"></i>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-700 group-hover:text-gray-900 transition-colors block" id="cv_label">Upload or drop a file</span>
                                <span class="text-xs text-gray-400">Accepted formats: PDF</span>
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
                    <label for="activities_doc_url" class="block text-sm font-medium text-gray-700 mb-2">Activities Documentation File</label>
                    <div class="relative flex items-center justify-between px-4 py-2.5 border border-gray-200 rounded-xl hover:border-gray-900 transition-all cursor-pointer group bg-white shadow-sm hover:shadow-md">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-gray-900 group-hover:text-white transition-all">
                                <i class="bi bi-cloud-upload text-lg"></i>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-700 group-hover:text-gray-900 transition-colors block" id="activities_label">Upload or drop a file</span>
                                <span class="text-xs text-gray-400">Accepted formats: PDF</span>
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
                    <label for="testimony" class="block text-sm font-medium text-gray-700 mb-2">Student Testimony</label>
                    <textarea name="testimony" id="testimony" rows="3"
                              class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-soft-gray-900 focus:border-soft-gray-900">{{ old('testimony') }}</textarea>
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
                    <label for="owned_businesses" class="block text-sm font-medium text-gray-700 mb-2">
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
        <div class="flex items-center justify-between pb-6">
            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 hover:text-gray-900 rounded-xl transition duration-150">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-soft-gray-900 hover:bg-soft-gray-800 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                <i class="bi bi-person-plus-fill me-2"></i>
                Create User
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
                    new TomSelect('#owned_businesses', { create: false, placeholder: "Select Businesses", searchField: ["text"] });
                }
            });
        </script>
    @endpush
</x-app-layout>