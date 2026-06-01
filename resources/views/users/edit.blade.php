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
        <a href="{{ route('users.show', $userToEdit) }}" class="btn-uco btn-uco-secondary">
            <i class="bi bi-arrow-left"></i>
            Back
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
            <p class="text-sm text-gray-600">Update user information strictly aligned with the new database columns</p>
        </div>
    </div>

    <form method="POST" action="{{ route('users.update', $userToEdit) }}" enctype="multipart/form-data" class="space-y-6">
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
                    <select name="student_status" id="student_status" required class="block w-full">
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
                <div class="relative" x-data="{
                    hasPhoto: {{ $userToEdit->profile_photo_url ? 'true' : 'false' }},
                    photoDeleted: false,
                    newPhotoSelected: false
                }">
                    <label class="form-label-custom">Profile Photo</label>
                    <input type="hidden" name="delete_profile_photo_url" :value="photoDeleted ? '1' : '0'">

                    <div class="flex items-start gap-5">
                        <!-- Clickable Photo Box -->
                        <div id="pp-container" style="width: 110px; height: 140px; border-radius: 10px; overflow: hidden; position: relative; background: #f8fafc; border: 1.5px solid #e2e8f0; cursor: pointer; transition: 0.3s;"
                             class="group hover:border-blue-500 shadow-sm"
                             onmouseover="this.querySelector('.photo-overlay').style.opacity='1'"
                             onmouseout="this.querySelector('.photo-overlay').style.opacity='0'">
                            
                            <!-- Current / New Photo Preview -->
                            <img id="preview-image-pp" src="{{ $userToEdit->profile_photo_url }}" 
                                 style="width: 100%; height: 100%; object-fit: contain;"
                                 x-show="hasPhoto && !photoDeleted">
                            
                            <!-- Initials Placeholder -->
                            <div id="initials-placeholder" style="width: 100%; height: 100%; background: linear-gradient(135deg, #f97316, #ea580c); display: flex; align-items: center; justify-content: center; color: white;"
                                 x-show="!hasPhoto || photoDeleted">
                                <span class="text-3xl font-black text-white select-none">{{ strtoupper(substr($userToEdit->name, 0, 1)) }}</span>
                            </div>

                            <!-- Click to pick trigger -->
                            <label for="profile_photo_url" class="absolute inset-0 cursor-pointer z-20">
                                <input type="file" name="profile_photo_url" id="profile_photo_url" accept="image/*" class="hidden"
                                       @change="const [file] = $event.target.files; if (file) { 
                                           document.getElementById('preview-image-pp').src = URL.createObjectURL(file);
                                           hasPhoto = true;
                                           photoDeleted = false;
                                           newPhotoSelected = true;
                                       }">
                            </label>

                            <!-- Hover Overlay -->
                            <div class="photo-overlay" style="position: absolute; inset: 0; background: rgba(15,23,42,0.6); display: flex; flex-direction: column; align-items: center; justify-content: center; opacity: 0; transition: 0.3s ease; pointer-events: none; backdrop-filter: blur(2px);">
                                <i class="bi bi-camera-fill text-white text-lg"></i>
                                <span class="text-white text-[8px] font-black uppercase tracking-widest mt-1">Change</span>
                            </div>
                        </div>

                        <!-- Action Buttons and Help Text -->
                        <div class="flex flex-col justify-center h-[140px]">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-2">JPG, PNG, WEBP — Max 5MB</p>
                            
                            <template x-if="hasPhoto && !photoDeleted && !newPhotoSelected">
                                <button type="button" @click="photoDeleted = true" 
                                        class="inline-flex items-center gap-1.5 text-xs text-red-500 hover:text-red-700 font-bold transition-all">
                                    <i class="bi bi-trash3 text-sm"></i>
                                    <span>Remove Photo</span>
                                </button>
                            </template>
                            
                            <template x-if="photoDeleted">
                                <button type="button" @click="photoDeleted = false" 
                                        class="inline-flex items-center gap-1.5 text-xs text-blue-500 hover:text-blue-700 font-bold transition-all">
                                    <i class="bi bi-arrow-counterclockwise text-sm"></i>
                                    <span>Undo Delete</span>
                                </button>
                            </template>

                            <template x-if="newPhotoSelected">
                                <button type="button" @click="
                                    document.getElementById('profile_photo_url').value = '';
                                    newPhotoSelected = false;
                                    hasPhoto = {{ $userToEdit->profile_photo_url ? 'true' : 'false' }};
                                    photoDeleted = false;
                                    document.getElementById('preview-image-pp').src = '{{ $userToEdit->profile_photo_url }}';
                                " 
                                        class="inline-flex items-center gap-1.5 text-xs text-amber-600 hover:text-amber-700 font-bold transition-all">
                                    <i class="bi bi-x-circle text-sm"></i>
                                    <span>Cancel New Photo</span>
                                </button>
                            </template>
                        </div>
                    </div>
                    @error('profile_photo_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
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
                    <select name="current_status" id="current_status" class="block w-full">
                        <option value="">Select Status</option>
                        <option value="Entrepreneur" {{ old('current_status', $userToEdit->current_status) == 'Entrepreneur' ? 'selected' : '' }}>Entrepreneur</option>
                        <option value="Intrapreneur" {{ old('current_status', $userToEdit->current_status) == 'Intrapreneur' ? 'selected' : '' }}>Intrapreneur</option>
                    </select>
                </div>                {{-- Activities Documentation Files --}}
                @php
                    $activitiesList = [];
                    if ($userToEdit->activities_doc_url) {
                        $rawUrls = array_filter(array_map('trim', preg_split('/[;,]+/', $userToEdit->activities_doc_url)));
                        foreach ($rawUrls as $rawUrl) {
                            $isGoogleDrive = str_contains($rawUrl, 'drive.google.com') || str_contains($rawUrl, 'docs.google.com');
                            $previewUrl = null;
                            $name = basename(parse_url($rawUrl, PHP_URL_PATH) ?? $rawUrl);

                            if ($isGoogleDrive) {
                                if (preg_match('/(?:id=|\/d\/)([a-zA-Z0-9-_]{25,})/', $rawUrl, $matches)) {
                                    $id = $matches[1];
                                    $previewUrl = "https://drive.google.com/thumbnail?sz=w400&id=" . $id;
                                }
                            } else {
                                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                    $previewUrl = $rawUrl;
                                }
                            }

                            $activitiesList[] = [
                                'url' => $rawUrl,
                                'previewUrl' => $previewUrl,
                                'name' => $name
                            ];
                        }
                    }
                @endphp
                <div class="md:col-span-2 relative" x-data="{
                    existingFiles: @js(array_values($activitiesList)),
                    deletedFiles: [],
                    newFiles: [],
                    deleteFile(url) {
                        if (!this.deletedFiles.includes(url)) {
                            this.deletedFiles.push(url);
                        }
                    },
                    undoDeleteFile(url) {
                        this.deletedFiles = this.deletedFiles.filter(item => item !== url);
                    },
                    isDeleted(url) {
                        return this.deletedFiles.includes(url);
                    },
                    handleNewFiles(event) {
                        const files = Array.from(event.target.files);
                        this.newFiles = [];
                        files.forEach(file => {
                            const fileObj = {
                                name: file.name,
                                size: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                                type: file.type,
                                preview: null
                            };
                            if (file.type.startsWith('image/')) {
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    fileObj.preview = e.target.result;
                                };
                                reader.readAsDataURL(file);
                            }
                            this.newFiles.push(fileObj);
                        });
                    }
                }">
                    <div class="flex items-center justify-between mb-3">
                        <label class="form-label-custom mb-0">Activities Documentation Files</label>
                        <div class="relative">
                            <input type="file" name="activities_docs[]" id="activities_docs" multiple accept="image/*,application/pdf"
                                   class="hidden"
                                   @change="handleNewFiles($event)">
                            <button type="button" @click="document.getElementById('activities_docs').click()"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-600 hover:text-blue-700 text-[10px] font-extrabold rounded-lg transition shadow-sm select-none cursor-pointer">
                                <i class="bi bi-plus-lg text-[9px]"></i>
                                <span>Add Files</span>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Hidden input for deleted existing files --}}
                    <template x-for="url in deletedFiles" :key="url">
                        <input type="hidden" name="delete_activities_files[]" :value="url">
                    </template>

                    <div class="flex flex-col gap-4">
                        {{-- Existing Files Grid --}}
                        <div x-show="existingFiles.length > 0" class="w-full">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-2">Existing Uploaded Documents</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <template x-for="item in existingFiles" :key="item.url">
                                    <div class="relative group rounded-xl border border-slate-200 overflow-hidden shadow-sm aspect-video flex items-center justify-center bg-slate-50 transition-all duration-200 hover:shadow-md hover:border-slate-300">
                                        
                                        {{-- Image or PDF Thumbnail Preview --}}
                                        <template x-if="item.previewUrl">
                                            <div class="w-full h-full relative overflow-hidden bg-slate-950/5 flex items-center justify-center">
                                                <img :src="item.previewUrl" class="absolute inset-0 w-full h-full object-cover blur-xl opacity-40 scale-110 pointer-events-none" aria-hidden="true" referrerpolicy="no-referrer">
                                                <img :src="item.previewUrl" class="relative z-10 max-w-full max-h-full object-contain" referrerpolicy="no-referrer">
                                            </div>
                                        </template>

                                        {{-- Local PDF generic Card --}}
                                        <template x-if="!item.previewUrl">
                                            <div class="flex flex-col items-center justify-center p-3 text-center">
                                                <div class="w-10 h-10 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center text-red-500 mb-1">
                                                    <i class="bi bi-file-earmark-pdf-fill text-xl"></i>
                                                </div>
                                                <span class="text-[10px] font-bold text-slate-600 truncate w-full max-w-[120px]" x-text="item.name"></span>
                                                <span class="text-[8px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">PDF</span>
                                            </div>
                                        </template>

                                        {{-- Hover Delete Overlay --}}
                                        <div x-show="!isDeleted(item.url)" class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center backdrop-blur-[1px]">
                                            <button type="button" @click="deleteFile(item.url)" class="w-8 h-8 rounded-lg bg-red-500 text-white hover:bg-red-600 flex items-center justify-center shadow-md transition-colors" title="Delete file">
                                                <i class="bi bi-trash-fill text-sm"></i>
                                            </button>
                                        </div>

                                        {{-- Deleted Overlay --}}
                                        <div x-show="isDeleted(item.url)" class="absolute inset-0 bg-red-50/90 flex flex-col items-center justify-center p-2 text-center transition-all duration-200">
                                            <i class="bi bi-trash3 text-red-500 text-lg mb-1"></i>
                                            <span class="text-[9px] font-bold text-red-600 uppercase tracking-wider mb-1.5">Flagged to Delete</span>
                                            <button type="button" @click="undoDeleteFile(item.url)" class="px-2 py-1 bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 font-bold text-[9px] rounded border border-slate-200 shadow-sm flex items-center gap-1 transition-colors">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                                <span>Undo</span>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- New Files Selection Previews --}}
                        <div x-show="newFiles.length > 0" class="w-full bg-slate-50 rounded-xl border border-slate-200 p-4">
                            <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-200">
                                <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="bi bi-check2-circle text-green-500 text-sm"></i>
                                    <span x-text="newFiles.length + ' New Files Selected'"></span>
                                </span>
                                <button type="button" @click="newFiles = []; document.getElementById('activities_docs').value = '';" class="text-[10px] text-amber-600 hover:text-amber-700 font-extrabold flex items-center gap-1 transition-colors">
                                    <i class="bi bi-trash3-fill"></i>
                                    <span>Clear Selection</span>
                                </button>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <template x-for="(file, idx) in newFiles" :key="idx">
                                    <div class="relative rounded-xl border border-slate-200 overflow-hidden shadow-sm aspect-video flex items-center justify-center bg-white">
                                        
                                        <template x-if="file.preview">
                                            <div class="w-full h-full relative overflow-hidden bg-slate-950/5 flex items-center justify-center">
                                                <img :src="file.preview" class="absolute inset-0 w-full h-full object-cover blur-xl opacity-40 scale-110 pointer-events-none" aria-hidden="true" referrerpolicy="no-referrer">
                                                <img :src="file.preview" class="relative z-10 max-w-full max-h-full object-contain" referrerpolicy="no-referrer">
                                            </div>
                                        </template>

                                        {{-- PDF Preview --}}
                                        <template x-if="!file.preview">
                                            <div class="flex flex-col items-center justify-center p-3 text-center">
                                                <div class="w-8 h-8 rounded-lg bg-red-50 border border-red-100 flex items-center justify-center text-red-500 mb-1">
                                                    <i class="bi bi-file-earmark-pdf-fill text-lg"></i>
                                                </div>
                                                <span class="text-[9px] font-bold text-slate-700 truncate w-full max-w-[120px]" x-text="file.name"></span>
                                                <span class="text-[7px] text-slate-400 font-extrabold uppercase mt-0.5" x-text="file.size"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    @error('activities_docs')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Testimony --}}
                <div class="md:col-span-2">
                    <label for="testimony" class="form-label-custom">Student Testimony</label>
                    <textarea name="testimony" id="testimony" rows="3"
                               class="form-textarea-custom">{{ old('testimony', $userToEdit->testimony) }}</textarea>
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
                    <select name="owned_businesses[]" id="owned_businesses" multiple class="block w-full">
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

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const roleSelect = document.getElementById('role');
                const studentStatusSelect = document.getElementById('student_status');
                const studentStatusContainer = document.getElementById('student_status_container');
                const identityContactSection = document.getElementById('identity_contact_section');
                const academicExtraSection = document.getElementById('academic_extra_section');
                const businessAssignmentSection = document.getElementById('business_assignment_section');

                function toggleStudentFields(role) {
                    if (role === 'admin') {
                        if (studentStatusContainer) studentStatusContainer.classList.add('hidden');
                        if (identityContactSection) identityContactSection.classList.add('hidden');
                        if (academicExtraSection) academicExtraSection.classList.add('hidden');
                        if (businessAssignmentSection) businessAssignmentSection.classList.add('hidden');
                        if (studentStatusSelect) studentStatusSelect.removeAttribute('required');
                    } else {
                        if (studentStatusContainer) studentStatusContainer.classList.remove('hidden');
                        if (identityContactSection) identityContactSection.classList.remove('hidden');
                        if (academicExtraSection) academicExtraSection.classList.remove('hidden');
                        if (businessAssignmentSection) businessAssignmentSection.classList.remove('hidden');
                        if (studentStatusSelect) studentStatusSelect.setAttribute('required', 'required');
                    }
                }

                if (window.TomSelect) {
                    new TomSelect('#role', { 
                        create: false, 
                        placeholder: "Select Role", 
                        searchField: ["text"],
                        onChange: function(value) {
                            toggleStudentFields(value);
                        }
                    });
                    new TomSelect('#student_status', { create: false, placeholder: "Select Status", searchField: ["text"] });
                    new TomSelect('#current_status', { create: false, placeholder: "Select Career Status", searchField: ["text"] });
                    new TomSelect('#owned_businesses', { create: false, placeholder: "Select Businesses", searchField: ["text"] });
                }

                // Initial run based on current role select value
                if (roleSelect) {
                    toggleStudentFields(roleSelect.value);
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