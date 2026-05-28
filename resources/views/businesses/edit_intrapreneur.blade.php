<x-app-layout>
    @push('styles')
        <style>
            .form-label-custom {
                display: block; 
                font-size: 11px; 
                font-weight: 800; 
                color: #475569; 
                text-transform: uppercase; 
                margin-bottom: 8px;
                letter-spacing: 0.5px;
            }

            .form-input-custom {
                width: 100%; 
                height: 44px; 
                padding: 0 15px; 
                border: 1.5px solid #e2e8f0; 
                border-radius: 8px; 
                font-size: 13px; 
                font-weight: 600; 
                outline: none; 
                box-sizing: border-box;
                transition: all 0.2s ease;
                background: white;
            }

            .form-input-custom:focus {
                border-color: #f7931e !important;
                box-shadow: 0 0 0 3px rgba(247, 147, 30, 0.1) !important;
            }

            .form-textarea-custom {
                width: 100%; 
                padding: 12px 15px; 
                border: 1.5px solid #e2e8f0; 
                border-radius: 8px; 
                font-size: 13px; 
                font-weight: 600; 
                outline: none; 
                box-sizing: border-box;
                transition: all 0.2s ease;
                min-height: 120px;
            }

            .form-textarea-custom:focus {
                border-color: #f7931e !important;
                box-shadow: 0 0 0 3px rgba(247, 147, 30, 0.1) !important;
            }
        </style>
    @endpush

    <div class="max-w-4xl mx-auto px-4 py-8">
        {{-- Header Section --}}
        <div class="mb-8 flex items-center gap-4 reveal-on-scroll">
            <a href="{{ route('intrapreneurs.show', $company) }}" class="btn-uco btn-uco-secondary">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>
            <div class="flex-1">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Edit Work Profile</h1>
                <p class="text-sm text-gray-500 mt-1">Update your professional employment listings in the UCO directory.</p>
            </div>
        </div>

        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6 sm:p-8 reveal-on-scroll">
            <form method="POST" action="{{ route('intrapreneurs.update', $company) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Company Name --}}
                    <div class="md:col-span-2">
                        <label for="name" class="form-label-custom">Company Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" required
                               class="form-input-custom @error('name') border-red-500 @enderror"
                               placeholder="e.g. PT Metal Smeltindo Selaras">
                        @error('name')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category_id" class="form-label-custom">Industry Category <span class="text-red-500">*</span></label>
                        <select name="category_id" id="category_id" required 
                                class="form-input-custom @error('category_id') border-red-500 @enderror">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $company->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Position / Job Role --}}
                    <div>
                        <label for="position" class="form-label-custom">Your Position / Job Title <span class="text-red-500">*</span></label>
                        <input type="text" name="position" id="position" value="{{ old('position', $company->position) }}" required
                               class="form-input-custom @error('position') border-red-500 @enderror"
                               placeholder="e.g. Inspection Laboratory Foreman">
                        @error('position')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Level --}}
                    <div>
                        <label for="level_position" class="form-label-custom">Job Role Level</label>
                        <select name="level_position" id="level_position" 
                                class="form-input-custom @error('level_position') border-red-500 @enderror">
                            <option value="">Select Level</option>
                            <option value="Intern" {{ old('level_position', $company->level_position) === 'Intern' ? 'selected' : '' }}>Intern / Magang</option>
                            <option value="Staff" {{ old('level_position', $company->level_position) === 'Staff' ? 'selected' : '' }}>Staff / Officer</option>
                            <option value="Supervisor" {{ old('level_position', $company->level_position) === 'Supervisor' ? 'selected' : '' }}>Supervisor / Team Leader</option>
                            <option value="Manager" {{ old('level_position', $company->level_position) === 'Manager' ? 'selected' : '' }}>Manager / Head of Dept</option>
                            <option value="Director" {{ old('level_position', $company->level_position) === 'Director' ? 'selected' : '' }}>Director / C-Level</option>
                        </select>
                        @error('level_position')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Company Scale --}}
                    <div>
                        <label for="company_scale" class="form-label-custom">Company Scale</label>
                        <select name="company_scale" id="company_scale" 
                                class="form-input-custom @error('company_scale') border-red-500 @enderror">
                            <option value="">Select Scale</option>
                            <option value="Perusahaan swasta lokal/regional" {{ old('company_scale', $company->company_scale) === 'Perusahaan swasta lokal/regional' ? 'selected' : '' }}>Swasta Lokal / Regional</option>
                            <option value="Perusahaan swasta nasional" {{ old('company_scale', $company->company_scale) === 'Perusahaan swasta nasional' ? 'selected' : '' }}>Swasta Nasional</option>
                            <option value="Perusahaan swasta multinasional/internasional" {{ old('company_scale', $company->company_scale) === 'Perusahaan swasta multinasional/internasional' ? 'selected' : '' }}>Swasta Internasional / BUMN</option>
                        </select>
                        @error('company_scale')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Year Started Working --}}
                    <div>
                        <label for="year_started_working" class="form-label-custom">Year Started Working</label>
                        <input type="number" name="year_started_working" id="year_started_working" 
                               value="{{ old('year_started_working', $company->year_started_working) }}" 
                               min="1900" max="{{ date('Y') + 1 }}"
                               class="form-input-custom @error('year_started_working') border-red-500 @enderror"
                               placeholder="e.g. 2021">
                        @error('year_started_working')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Logo Upload with Live Preview & Remove Option (Tactile Card Layout) --}}
                    <div class="md:col-span-2 relative pb-5" x-data="{
                        hasLogo: {{ $company->logo_url && !str_contains($company->logo_url, 'ui-avatars.com') ? 'true' : 'false' }},
                        logoDeleted: false,
                        newLogoSelected: false
                    }">
                        <label class="form-label-custom">Company Logo</label>
                        <input type="hidden" name="delete_logo" :value="logoDeleted ? '1' : '0'">

                        <div class="flex items-start gap-5">
                            <!-- Clickable Logo Box -->
                            <div id="logo-container" style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; position: relative; background: #f8fafc; border: 1.5px solid #e2e8f0; cursor: pointer; transition: 0.3s;"
                                 class="group hover:border-[#f7931e] shadow-sm flex items-center justify-center p-2"
                                 onmouseover="this.querySelector('.photo-overlay').style.opacity='1'"
                                 onmouseout="this.querySelector('.photo-overlay').style.opacity='0'">
                                
                                <!-- Current / New Logo Preview -->
                                <img id="preview-image-logo" src="{{ $company->logo_url }}" 
                                     style="max-w-full max-h-full object-contain;"
                                     x-show="hasLogo && !logoDeleted">
                                
                                <!-- Initials Placeholder -->
                                <div id="logo-placeholder" style="width: 100%; height: 100%; background: linear-gradient(135deg, #f7931e, #fdb913); display: flex; align-items: center; justify-content: center; color: white;"
                                     x-show="!hasLogo || logoDeleted">
                                    <span class="text-3xl font-black text-white select-none">{{ strtoupper(substr($company->name, 0, 1)) }}</span>
                                </div>

                                <!-- Click to pick trigger -->
                                <label for="logo_url" class="absolute inset-0 cursor-pointer z-20">
                                    <input type="file" name="logo_url" id="logo_url" accept="image/*" class="hidden"
                                           @change="const [file] = $event.target.files; if (file) { 
                                               document.getElementById('preview-image-logo').src = URL.createObjectURL(file);
                                               hasLogo = true;
                                               logoDeleted = false;
                                               newLogoSelected = true;
                                           }">
                                </label>

                                <!-- Hover Overlay -->
                                <div class="photo-overlay" style="position: absolute; inset: 0; background: rgba(15,23,42,0.6); display: flex; flex-direction: column; align-items: center; justify-content: center; opacity: 0; transition: 0.3s ease; pointer-events: none; backdrop-filter: blur(2px);">
                                    <i class="bi bi-camera-fill text-white text-lg"></i>
                                    <span class="text-white text-[8px] font-black uppercase tracking-widest mt-1">Change</span>
                                </div>
                            </div>

                            <!-- Action Buttons and Help Text -->
                            <div class="flex flex-col justify-center h-[120px]">
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-2">JPG, PNG, WEBP — Max 5MB</p>
                                
                                <template x-if="hasLogo && !logoDeleted && !newLogoSelected">
                                    <button type="button" @click="logoDeleted = true" 
                                            class="inline-flex items-center gap-1.5 text-xs text-red-500 hover:text-red-700 font-bold transition-all">
                                        <i class="bi bi-trash3 text-sm"></i>
                                        <span>Remove Logo</span>
                                    </button>
                                </template>
                                
                                <template x-if="logoDeleted">
                                    <button type="button" @click="logoDeleted = false" 
                                            class="inline-flex items-center gap-1.5 text-xs text-blue-500 hover:text-blue-700 font-bold transition-all">
                                        <i class="bi bi-arrow-counterclockwise text-sm"></i>
                                        <span>Undo Delete</span>
                                    </button>
                                </template>

                                <template x-if="newLogoSelected">
                                    <button type="button" @click="
                                        document.getElementById('logo_url').value = '';
                                        newLogoSelected = false;
                                        hasLogo = {{ $company->logo_url ? 'true' : 'false' }};
                                        logoDeleted = false;
                                        document.getElementById('preview-image-logo').src = '{{ $company->logo_url }}';
                                    " 
                                            class="inline-flex items-center gap-1.5 text-xs text-amber-600 hover:text-amber-700 font-bold transition-all">
                                        <i class="bi bi-x-circle text-sm"></i>
                                        <span>Cancel New Logo</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        @error('logo_url')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Job Description --}}
                    <div class="md:col-span-2">
                        <label for="job_description" class="form-label-custom">Job Description & Responsibilities <span class="text-red-500">*</span></label>
                        <textarea name="job_description" id="job_description" required
                                  class="form-textarea-custom @error('job_description') border-red-500 @enderror"
                                  placeholder="Describe your job roles, day-to-day operations, and career contributions...">{{ old('job_description', $company->job_description) }}</textarea>
                        @error('job_description')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between border-t border-slate-100 pt-6 mt-8">
                    <a href="{{ route('intrapreneurs.show', $company) }}" class="btn-uco btn-uco-neutral">
                        Cancel
                    </a>
                    <button type="submit" class="btn-uco btn-uco-primary">
                        <i class="bi bi-check-circle-fill"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
