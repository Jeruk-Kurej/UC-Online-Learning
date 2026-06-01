<x-app-layout>
    {{-- UCO Profile Edit Page - V36: Clean Identity & Optimized Sidebar --}}
    <div style="max-width: 1200px; margin: 40px auto; padding: 0 25px; font-family: 'Inter', -apple-system, sans-serif; color: #1e293b;" x-data="{ activeTab: 'identity' }">
        
        {{-- Header --}}
        <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <h1 style="font-size: 30px; font-weight: 800; letter-spacing: -1.2px; margin: 0; color: #0f172a;">Profile Settings</h1>
                <p style="color: #64748b; font-size: 13px; margin-top: 4px; font-weight: 500;">Manage your personal information and academic records.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div style="display: flex; gap: 35px; align-items: flex-start;">
                
                {{-- Left Sidebar --}}
                <div style="width: 260px; flex-shrink: 0; position: sticky; top: 100px;">
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 30px; display: flex; flex-direction: column; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <div x-data="{ 
                            hasPhoto: {{ $user->profile_photo_url ? 'true' : 'false' }}, 
                            photoDeleted: false,
                            newPhotoSelected: false
                        }" class="flex flex-col items-center">
                            <div id="photo-container" style="width: 140px; height: 180px; border-radius: 10px; overflow: hidden; position: relative; background: #f8fafc; border: 1.5px solid #e2e8f0; margin-bottom: 12px; cursor: pointer; transition: 0.3s;" 
                                 onmouseover="this.querySelector('.photo-overlay').style.opacity='1'" 
                                 onmouseout="this.querySelector('.photo-overlay').style.opacity='0'">
                                
                                <img id="preview-image" src="{{ $user->profile_photo_url }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     x-show="hasPhoto && !photoDeleted">
                                
                                <div id="initials-placeholder" style="width: 100%; height: 100%; background: linear-gradient(135deg, #f97316, #ea580c); display: flex; align-items: center; justify-content: center; color: white;"
                                     x-show="!hasPhoto || photoDeleted">
                                    <span style="font-size: 48px; font-weight: 800; letter-spacing: -2px;">{{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}</span>
                                </div>

                                <label for="profile_photo" style="position: absolute; inset: 0; cursor: pointer; z-index: 20;">
                                    <input type="file" name="profile_photo" id="profile_photo" style="display: none;" 
                                           accept="image/*"
                                           @change="const [file] = $event.target.files; if (file) { 
                                               document.getElementById('preview-image').src = URL.createObjectURL(file);
                                               hasPhoto = true;
                                               photoDeleted = false;
                                               newPhotoSelected = true;
                                           }">
                                </label>
                                <div class="photo-overlay" style="position: absolute; inset: 0; background: rgba(15,23,42,0.6); display: flex; flex-direction: column; align-items: center; justify-content: center; opacity: 0; transition: 0.3s ease; pointer-events: none; backdrop-filter: blur(2px);">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                                    <span style="color: white; font-size: 9px; font-weight: 800; text-transform: uppercase; margin-top: 6px; letter-spacing: 0.5px;">Update</span>
                                </div>
                            </div>

                            <input type="hidden" name="delete_profile_photo" :value="photoDeleted ? '1' : '0'">

                            <template x-if="hasPhoto && !photoDeleted && !newPhotoSelected">
                                <button type="button" @click="photoDeleted = true" 
                                        class="text-xs text-red-500 hover:text-red-700 font-bold transition-all flex items-center gap-1 mb-2">
                                    <i class="bi bi-trash3 text-sm"></i>
                                    <span>Remove Photo</span>
                                </button>
                            </template>
                            
                            <template x-if="photoDeleted">
                                <button type="button" @click="photoDeleted = false" 
                                        class="text-xs text-blue-500 hover:text-blue-700 font-bold transition-all flex items-center gap-1 mb-2">
                                    <i class="bi bi-arrow-counterclockwise text-sm"></i>
                                    <span>Undo Delete</span>
                                </button>
                            </template>

                            <template x-if="newPhotoSelected">
                                <button type="button" @click="
                                    document.getElementById('profile_photo').value = '';
                                    newPhotoSelected = false;
                                    hasPhoto = {{ $user->profile_photo_url ? 'true' : 'false' }};
                                    photoDeleted = false;
                                    document.getElementById('preview-image').src = '{{ $user->profile_photo_url }}';
                                " 
                                        class="text-xs text-amber-600 hover:text-amber-700 font-bold transition-all flex items-center gap-1 mb-2">
                                    <i class="bi bi-x-circle text-sm"></i>
                                    <span>Cancel New Photo</span>
                                </button>
                            </template>
                        </div>
                        <h3 style="font-size: 17px; font-weight: 800; text-align: center; margin: 0 0 4px 0; color: #0f172a; letter-spacing: -0.5px; line-height: 1.3;">{{ $user->prefix_title ? $user->prefix_title . ' ' : '' }}{{ $user->name }}{{ $user->suffix_title ? ', ' . $user->suffix_title : '' }}</h3>
                        <p style="font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 12px; text-align: center; word-break: break-all;">{{ $user->email }}</p>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                            <span style="padding: 3px 8px; background: #f1f5f9; border-radius: 5px; font-size: 9px; font-weight: 800; color: #475569; text-transform: uppercase;">{{ $user->student_status ?? 'Active' }}</span>
                        </div>
                        <div style="width: 100%; height: 1px; background: #f1f5f9; margin: 12px 0;"></div>
                        <p style="font-size: 11px; color: #94a3b8; text-align: center; line-height: 1.6; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Joined {{ $user->created_at->format('M Y') }}</p>
                    </div>
                </div>

                {{-- Main Form Sections --}}
                <div style="flex: 1; display: flex; flex-direction: column; gap: 30px;">
                    
                    {{-- Section 1: Identity --}}
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 35px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f1f5f9; letter-spacing: -0.5px;">Identity Information</h2>
                        
                        <div style="margin-bottom: 25px; display: flex; align-items: flex-start; gap: 25px;">
                            <label style="width: 140px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 15px;">Full Name</label>
                            <div style="flex: 1; display: flex; flex-direction: column; gap: 12px;">
                                <div>
                                    <label style="display: block; font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; letter-spacing: 0.5px;">Prefix Title (e.g. Prof. Dr.)</label>
                                    <input type="text" name="prefix_title" value="{{ old('prefix_title', $user->prefix_title) }}" placeholder="Prefix" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; letter-spacing: 0.5px;">Full Name</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Full Name" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; letter-spacing: 0.5px;">Suffix Title (e.g. S.Kom, M.T.)</label>
                                    <input type="text" name="suffix_title" value="{{ old('suffix_title', $user->suffix_title) }}" placeholder="Suffix" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;">
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 25px;">
                            <label style="width: 140px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Personal Email</label>
                            <input type="email" name="personal_email" value="{{ old('personal_email', $user->personal_email) }}" style="flex: 1; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;">
                        </div>
                    </div>

                    {{-- Section 2: Contact --}}
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 35px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #f1f5f9;">
                            <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.5px;">Contact Details</h2>
                            <label class="relative inline-flex items-center cursor-pointer group">
                                <input type="checkbox" name="show_contact_details" value="1" class="sr-only peer" {{ old('show_contact_details', $user->show_contact_details) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ml-3 text-xs font-bold text-slate-600 uppercase tracking-wider group-hover:text-slate-900 transition-colors">Show contacts publicly</span>
                            </label>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                            <div><label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Phone Number</label><input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;"></div>
                            <div><label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Mobile Number</label><input type="text" name="mobile_number" value="{{ old('mobile_number', $user->mobile_number) }}" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;"></div>
                            <div><label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">WhatsApp</label><input type="text" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;"></div>
                            <div><label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">LinkedIn Username</label><input type="text" name="linkedin" value="{{ old('linkedin', $user->linkedin) }}" placeholder="e.g. johndoe" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;"></div>
                        </div>
                    </div>

                    {{-- Section 3: Academic --}}
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 35px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f1f5f9; letter-spacing: -0.5px;">Academic Records</h2>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                            <div><label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Student ID (NIS)</label><div style="width: 100%; height: 44px; padding: 0 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 700; color: #64748b; display: flex; align-items: center; box-sizing: border-box;">{{ $user->nis }}</div></div>
                            <div style="position: relative;">
                                <label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Current Status</label>
                                <select name="current_status" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; color: #0f172a; outline: none; appearance: none; background: white; cursor: pointer; box-sizing: border-box;">
                                    <option value="Entrepreneur" {{ old('current_status', $user->current_status) == 'Entrepreneur' ? 'selected' : '' }}>Entrepreneur</option>
                                    <option value="Intrapreneur" {{ old('current_status', $user->current_status) == 'Intrapreneur' ? 'selected' : '' }}>Intrapreneur</option>
                                </select>
                                <div style="position: absolute; right: 15px; top: 41px; pointer-events: none;"><svg width="10" height="10" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.5 4.5L6 8L9.5 4.5" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                            </div>
                            <div><label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Enrollment Year</label><input type="text" name="year_of_enrollment" value="{{ old('year_of_enrollment', $user->year_of_enrollment) }}" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;"></div>
                            <div><label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Graduate Year</label><input type="text" name="graduate_year" value="{{ old('graduate_year', $user->graduate_year) }}" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;"></div>
                        </div>
                        <div style="margin-top: 25px;"><label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Major</label><div style="width: 100%; height: 44px; padding: 0 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 700; color: #64748b; display: flex; align-items: center; box-sizing: border-box;">{{ $user->major }}</div></div>
                    </div>

                    {{-- Section 4: Extras --}}
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 35px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f1f5f9; letter-spacing: -0.5px;">Additional Documents</h2>
                        
                        @php
                            $activitiesList = [];
                            if ($user->activities_doc_url) {
                                $rawUrls = array_filter(array_map('trim', preg_split('/[;,]+/', $user->activities_doc_url)));
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

                        <div style="display: flex; flex-direction: column; gap: 20px;" x-data="{
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
                            <div style="display: flex; align-items: flex-start; gap: 25px;">
                                <div style="width: 140px; display: flex; flex-direction: column; gap: 8px; flex-shrink: 0;">
                                    <label style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">Activities Docs</label>
                                    <div class="relative self-start">
                                        <input type="file" name="activities_files[]" id="activities_files" multiple accept="image/*,application/pdf"
                                               class="hidden"
                                               @change="handleNewFiles($event)">
                                        <button type="button" @click="document.getElementById('activities_files').click()"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-600 hover:text-blue-700 text-[10px] font-extrabold rounded-lg transition shadow-sm select-none cursor-pointer">
                                            <i class="bi bi-plus-lg text-[9px]"></i>
                                            <span>Add Files</span>
                                        </button>
                                    </div>
                                </div>
                                <div style="flex: 1; display: flex; flex-direction: column; gap: 15px;">
                                    
                                    {{-- Hidden input for deleted existing files --}}
                                    <template x-for="url in deletedFiles" :key="url">
                                        <input type="hidden" name="delete_activities_files[]" :value="url">
                                    </template>

                                    {{-- Existing Files Grid --}}
                                    <div x-show="existingFiles.length > 0" class="w-full">
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-2">Existing Uploaded Documents</p>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                            <template x-for="item in existingFiles" :key="item.url">
                                                <div class="relative group rounded-xl border border-slate-200 overflow-hidden shadow-sm aspect-video flex items-center justify-center bg-slate-50 transition-all duration-200 hover:shadow-md hover:border-slate-300">
                                                    
                                                    {{-- Image or PDF Thumbnail Preview --}}
                                                    <template x-if="item.previewUrl">
                                                        <img :src="item.previewUrl" class="w-full h-full object-cover">
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
                                    <div x-show="newFiles.length > 0" class="w-full bg-slate-50/50 rounded-xl border border-slate-200 p-4">
                                        <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-200">
                                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider flex items-center gap-1.5">
                                                <i class="bi bi-check2-circle text-green-500 text-sm"></i>
                                                <span x-text="newFiles.length + ' New Files Selected'"></span>
                                            </span>
                                            <button type="button" @click="newFiles = []; document.getElementById('activities_files').value = '';" class="text-[10px] text-amber-600 hover:text-amber-700 font-extrabold flex items-center gap-1 transition-colors">
                                                <i class="bi bi-trash3-fill"></i>
                                                <span>Clear Selection</span>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                            <template x-for="(file, idx) in newFiles" :key="idx">
                                                <div class="relative rounded-xl border border-slate-200 overflow-hidden shadow-sm aspect-video flex items-center justify-center bg-white">
                                                    
                                                    {{-- Image Preview --}}
                                                    <template x-if="file.preview">
                                                        <img :src="file.preview" class="w-full h-full object-cover">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Action - Outside Flex Container to limit Sidebar --}}
            <div style="margin-top: 30px; margin-bottom: 50px; display: flex; justify-content: flex-end; align-items: center; max-width: 1200px;">
                <div style="width: 260px; margin-right: 35px; flex-shrink: 0;"></div> {{-- Spacer matching sidebar width --}}
                <button type="submit" class="btn-uco btn-uco-primary">
                    <i class="bi bi-file-earmark-check-fill text-base"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') {
                event.preventDefault();
                event.target.blur();
            }
        });
    </script>
</x-app-layout>
