<x-app-layout>
    @section('title', 'Edit ' . $business->name)

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css" rel="stylesheet">
    @endpush
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('businesses.show', $business) }}" class="btn-uco btn-uco-secondary">
            <i class="bi bi-arrow-left"></i>
            Back
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Edit Business</h1>
            <p class="text-sm text-gray-600">Update business information cleanly aligned with the new database columns</p>
        </div>
    </div>

    <form method="POST" action="{{ route('businesses.update', $business) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Core Information
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Business Name --}}
                <div class="relative pb-5">
                    <label for="name" class="form-label-custom">
                        Business Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $business->name) }}" required
                           class="form-input-custom @error('name') border-red-500 @enderror">
                    @error('name')<p class="absolute bottom-0 left-0 text-[10px] font-bold text-red-600 uppercase tracking-tight">{{ $message }}</p>@enderror
                </div>

                {{-- Business Category --}}
                <div class="relative pb-5">
                    <label for="category_id" class="form-label-custom">
                        Business Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" id="category_id" required class="block w-full">
                        <option value="" disabled>Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $business->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="absolute bottom-0 left-0 text-[10px] font-bold text-red-600 uppercase tracking-tight">{{ $message }}</p>@enderror
                </div>

                {{-- Offering Type / Mode --}}
                <div class="relative pb-5">
                    <label for="business_mode" class="form-label-custom">
                        Offering Type <span class="text-red-500">*</span>
                    </label>
                    <select name="business_mode" id="business_mode" required class="block w-full">
                        <option value="product" {{ old('business_mode', $business->offering_type) === 'product' ? 'selected' : '' }}>Product Only</option>
                        <option value="service" {{ old('business_mode', $business->offering_type) === 'service' ? 'selected' : '' }}>Service Only</option>
                        <option value="both" {{ old('business_mode', $business->offering_type) === 'both' ? 'selected' : '' }}>Product & Service</option>
                    </select>
                    @error('business_mode')<p class="absolute bottom-0 left-0 text-[10px] font-bold text-red-600 uppercase tracking-tight">{{ $message }}</p>@enderror
                </div>

                {{-- Established Date --}}
                <div class="relative pb-5">
                    <label for="established_date" class="form-label-custom">
                        Established Date
                    </label>
                    <input type="date" name="established_date" id="established_date" value="{{ old('established_date', optional($business->established_date)->format('Y-m-d')) }}"
                           class="form-input-custom @error('established_date') border-red-500 @enderror">
                    @error('established_date')<p class="absolute bottom-0 left-0 text-[10px] font-bold text-red-600 uppercase tracking-tight">{{ $message }}</p>@enderror
                </div>

                {{-- Description --}}
                <div class="md:col-span-2 relative pb-5">
                    <label for="description" class="form-label-custom">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="3" required
                              class="form-input-custom">{{ old('description', $business->description) }}</textarea>
                    @error('description')<p class="absolute bottom-0 left-0 text-[10px] font-bold text-red-600 uppercase tracking-tight">{{ $message }}</p>@enderror
                </div>

                {{-- Business Logo --}}
                <div class="md:col-span-2 relative pb-5" x-data="{
                    hasLogo: {{ $business->logo_url && !str_contains($business->logo_url, 'ui-avatars.com') ? 'true' : 'false' }},
                    logoDeleted: false,
                    newLogoSelected: false
                }">
                    <label class="form-label-custom">Business Logo</label>
                    <input type="hidden" name="delete_logo" :value="logoDeleted ? '1' : '0'">

                    <div class="flex items-start gap-5">
                        <!-- Clickable Logo Box -->
                        <div id="logo-container" style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; position: relative; background: #f8fafc; border: 1.5px solid #e2e8f0; cursor: pointer; transition: 0.3s;"
                             class="group hover:border-blue-500 shadow-sm flex items-center justify-center p-2"
                             onmouseover="this.querySelector('.photo-overlay').style.opacity='1'"
                             onmouseout="this.querySelector('.photo-overlay').style.opacity='0'">
                            
                            <!-- Current / New Logo Preview -->
                            <img id="preview-image-logo" src="{{ $business->logo_url }}" 
                                 style="max-w-full max-h-full object-contain;"
                                 x-show="hasLogo && !logoDeleted">
                            
                            <!-- Initials Placeholder -->
                            <div id="logo-placeholder" style="width: 100%; height: 100%; background: linear-gradient(135deg, #f97316, #ea580c); display: flex; align-items: center; justify-content: center; color: white;"
                                 x-show="!hasLogo || logoDeleted">
                                <span class="text-3xl font-black text-white select-none">{{ strtoupper(substr($business->name, 0, 1)) }}</span>
                            </div>

                            <!-- Click to pick trigger -->
                            <label for="logo" class="absolute inset-0 cursor-pointer z-20">
                                <input type="file" name="logo" id="logo" accept="image/*" class="hidden"
                                       @change="const [file] = $event.target.files; if (file) { 
                                           document.getElementById('preview-image-logo').src = URL.createObjectURL(file);
                                           hasPhoto = true; /* just in case */
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
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-2">JPG, PNG, WEBP — Max 20MB</p>
                            
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
                                    document.getElementById('logo').value = '';
                                    newLogoSelected = false;
                                    hasLogo = {{ $business->logo_url ? 'true' : 'false' }};
                                    logoDeleted = false;
                                    document.getElementById('preview-image-logo').src = '{{ $business->logo_url }}';
                                " 
                                        class="inline-flex items-center gap-1.5 text-xs text-amber-600 hover:text-amber-700 font-bold transition-all">
                                    <i class="bi bi-x-circle text-sm"></i>
                                    <span>Cancel New Logo</span>
                                </button>
                            </template>
                        </div>
                    </div>
                    @error('logo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Location & Contact Details
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Province --}}
                <div>
                    <label for="province" class="form-label-custom">Province</label>
                    <select name="province" id="province" class="block w-full">
                        <option value="">Select Province</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}" {{ (old('province', $selectedProvinceId) == $prov->id) ? 'selected' : '' }}>
                                {{ $prov->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- City --}}
                <div>
                    <label for="city" class="form-label-custom">City / Regency</label>
                    <select name="city" id="city" class="block w-full">
                        <option value="">Select Province First</option>
                        @foreach($availableCities as $city)
                            <option value="{{ $city->id }}" {{ (old('city', $business->city) == $city->name) ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Full Address --}}
                <div class="md:col-span-2">
                    <label for="address" class="form-label-custom">Full Address</label>
                    <textarea name="address" id="address" rows="2"
                              class="form-input-custom">{{ old('address', $business->address) }}</textarea>
                </div>

                {{-- Phone Number --}}
                <div>
                    <label for="phone" class="form-label-custom">Business Phone Number</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $business->phone_number) }}"
                           class="form-input-custom">
                </div>

                {{-- WhatsApp Number --}}
                <div>
                    <label for="whatsapp_number" class="form-label-custom">Business WhatsApp Number</label>
                    <input type="tel" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $business->whatsapp) }}"
                           class="form-input-custom" placeholder="e.g. 62812...">
                </div>

                {{-- Email Address --}}
                <div>
                    <label for="email" class="form-label-custom">Business Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $business->email) }}"
                           class="form-input-custom">
                </div>

                {{-- Website --}}
                <div>
                    <label for="website" class="form-label-custom">Website URL</label>
                    <input type="url" name="website" id="website" value="{{ old('website', $business->website) }}"
                           class="form-input-custom" placeholder="https://...">
                </div>

                {{-- Instagram Handle --}}
                <div>
                    <label for="instagram_handle" class="form-label-custom">Instagram Handle</label>
                    <input type="text" name="instagram_handle" id="instagram_handle" value="{{ old('instagram_handle', $business->instagram) }}"
                           class="form-input-custom" placeholder="username">
                </div>
            </div>
        </div>

        @if(auth()->check() && auth()->user()->isAdmin())
            <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                    Ownership Management
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Primary Owner --}}
                    <div>
                        <label for="user_id" class="form-label-custom">
                            Primary Owner (Admin Only)
                        </label>
                        <select name="user_id" id="user_id" class="block w-full">
                            <option value="">Select Primary Owner</option>
                            @foreach($users as $ownerUser)
                                <option value="{{ $ownerUser->id }}" {{ old('user_id', $business->user_id) == $ownerUser->id ? 'selected' : '' }}>
                                    {{ $ownerUser->name }} ({{ $ownerUser->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Additional Owners --}}
                    <div>
                        <label for="owner_ids" class="form-label-custom">
                            Additional Owners (Optional)
                        </label>
                        <select name="owner_ids[]" id="owner_ids" multiple class="block w-full">
                            @foreach($users as $ownerUser)
                                <option value="{{ $ownerUser->id }}" {{ in_array($ownerUser->id, old('owner_ids', $business->members()->pluck('users.id')->toArray() ?? [])) ? 'selected' : '' }}>
                                    {{ $ownerUser->name }} ({{ $ownerUser->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif

        {{-- Action Buttons --}}
        <div class="flex items-center justify-between pb-6">
            <a href="{{ (auth()->check() && auth()->user()->isAdmin()) ? route('businesses.index') : route('businesses.my') }}" 
               class="btn-uco btn-uco-neutral">
                Cancel
            </a>
            <button type="submit" class="btn-uco btn-uco-primary">
                <i class="bi bi-check-circle-fill"></i>
                Update Business
            </button>
        </div>
    </form>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.TomSelect) {
                    new TomSelect('#category_id', { create: false, placeholder: "Select Category", searchField: ["text"] });
                    new TomSelect('#business_mode', { create: false, placeholder: "Select Mode", searchField: ["text"] });
                    
                    const citySelect = new TomSelect('#city', { 
                        create: false, 
                        placeholder: "Select City", 
                        searchField: ["text"],
                        valueField: 'id',
                        labelField: 'name'
                    });

                    // Initial state: disable if no province selected
                    if (!document.getElementById('province').value) {
                        citySelect.disable();
                    }

                    const provinceSelect = new TomSelect('#province', { 
                        create: false, 
                        placeholder: "Select Province", 
                        searchField: ["text"],
                        onChange: function(value) {
                            citySelect.clear();
                            citySelect.clearOptions();

                            if (!value) {
                                citySelect.disable();
                                citySelect.settings.placeholder = "Select Province First";
                                citySelect.inputState();
                                return;
                            }

                            citySelect.disable();
                            citySelect.settings.placeholder = "Loading cities...";
                            citySelect.inputState();

                            fetch(`/api/regencies?province_id=${value}`)
                                .then(response => response.json())
                                .then(data => {
                                    citySelect.addOptions(data);
                                    citySelect.enable();
                                    citySelect.settings.placeholder = "Select City";
                                    citySelect.inputState();
                                });
                        }
                    });

                    @if(auth()->check() && auth()->user()->isAdmin())
                        new TomSelect('#user_id', { create: false, placeholder: "Select Primary Owner", searchField: ["text"] });
                        new TomSelect('#owner_ids', { create: false, placeholder: "Select Additional Owners", searchField: ["text"] });
                    @endif
                }
            });
        </script>
    @endpush
</x-app-layout>
