<x-app-layout>
    @section('title', 'Register New Business')

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css" rel="stylesheet">
    @endpush
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ (auth()->check() && auth()->user()->isAdmin()) ? route('businesses.index') : route('businesses.my') }}" 
           class="btn-uco btn-uco-secondary">
            <i class="bi bi-arrow-left"></i>
            Back
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Register Business</h1>
            <p class="text-sm text-gray-600">Register your business information cleanly aligned with the new database columns</p>
        </div>
    </div>

    <form method="POST" action="{{ route('businesses.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

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
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="form-input-custom @error('name') border-red-500 @enderror">
                    @error('name')<p class="absolute bottom-0 left-0 text-[10px] font-bold text-red-600 uppercase tracking-tight">{{ $message }}</p>@enderror
                </div>

                {{-- Business Category --}}
                <div class="relative pb-5">
                    <label for="category_id" class="form-label-custom">
                        Business Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" id="category_id" required class="block w-full">
                        <option value="" disabled selected>Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
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
                        <option value="product" {{ old('business_mode') === 'product' ? 'selected' : '' }}>Product Only</option>
                        <option value="service" {{ old('business_mode') === 'service' ? 'selected' : '' }}>Service Only</option>
                        <option value="both" {{ old('business_mode', 'both') === 'both' ? 'selected' : '' }}>Product & Service</option>
                    </select>
                    @error('business_mode')<p class="absolute bottom-0 left-0 text-[10px] font-bold text-red-600 uppercase tracking-tight">{{ $message }}</p>@enderror
                </div>

                {{-- Established Date --}}
                <div class="relative pb-5">
                    <label for="established_date" class="form-label-custom">
                        Established Date
                    </label>
                    <input type="date" name="established_date" id="established_date" value="{{ old('established_date') }}"
                           class="form-input-custom @error('established_date') border-red-500 @enderror">
                    @error('established_date')<p class="absolute bottom-0 left-0 text-[10px] font-bold text-red-600 uppercase tracking-tight">{{ $message }}</p>@enderror
                </div>

                {{-- Description --}}
                <div class="md:col-span-2 relative pb-5">
                    <label for="description" class="form-label-custom">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="3" required
                              class="form-input-custom">{{ old('description') }}</textarea>
                    @error('description')<p class="absolute bottom-0 left-0 text-[10px] font-bold text-red-600 uppercase tracking-tight">{{ $message }}</p>@enderror
                </div>

                {{-- Business Logo --}}
                <div class="md:col-span-2 relative pb-5" x-data="{
                    newLogoSelected: false,
                    newLogoName: '',
                    newLogoUrl: ''
                }">
                    <label class="form-label-custom">Business Logo</label>

                    <!-- Selection Preview -->
                    <div x-show="newLogoSelected" class="flex items-center gap-4 mb-3">
                        <div class="w-20 h-20 rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-gray-50 flex items-center justify-center p-2 flex-shrink-0">
                            <img :src="newLogoUrl" class="max-w-full max-h-full object-contain">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider" x-text="newLogoName">Selected Logo</span>
                            <button type="button" @click="
                                document.getElementById('logo').value = '';
                                newLogoSelected = false;
                                newLogoName = '';
                                newLogoUrl = '';
                            " 
                                    class="inline-flex items-center gap-1.5 text-xs text-red-500 hover:text-red-700 font-bold transition-all">
                                <i class="bi bi-x-circle text-sm"></i>
                                <span>Clear Selection</span>
                            </button>
                        </div>
                    </div>

                    <!-- Upload Input Container -->
                    <div x-show="!newLogoSelected" class="form-file-container-custom group relative">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-blue-500 transition-all">
                                <i class="bi bi-cloud-upload text-base"></i>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900 transition-colors block leading-tight">Upload or drop a logo</span>
                                <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">JPG, PNG, WEBP — Max 10MB</span>
                            </div>
                        </div>
                        <input type="file" name="logo" id="logo" accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               @change="const [file] = $event.target.files; if (file) { 
                                   newLogoSelected = true; 
                                   newLogoName = file.name; 
                                   newLogoUrl = URL.createObjectURL(file);
                               }">
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
                            <option value="{{ $prov->id }}" {{ old('province') == $prov->id ? 'selected' : '' }}>{{ $prov->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- City --}}
                <div>
                    <label for="city" class="form-label-custom">City / Regency</label>
                    <select name="city" id="city" class="block w-full">
                        <option value="">Select Province First</option>
                    </select>
                </div>

                {{-- Full Address --}}
                <div class="md:col-span-2">
                    <label for="address" class="form-label-custom">Full Address</label>
                    <textarea name="address" id="address" rows="2"
                              class="form-input-custom">{{ old('address') }}</textarea>
                </div>

                {{-- Phone Number --}}
                <div>
                    <label for="phone" class="form-label-custom">Business Phone Number</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                           class="form-input-custom">
                </div>

                {{-- WhatsApp Number --}}
                <div>
                    <label for="whatsapp_number" class="form-label-custom">Business WhatsApp Number</label>
                    <input type="tel" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number') }}"
                           class="form-input-custom" placeholder="e.g. 62812...">
                </div>

                {{-- Email Address --}}
                <div>
                    <label for="email" class="form-label-custom">Business Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="form-input-custom">
                </div>

                {{-- Website --}}
                <div>
                    <label for="website" class="form-label-custom">Website URL</label>
                    <input type="url" name="website" id="website" value="{{ old('website') }}"
                           class="form-input-custom" placeholder="https://...">
                </div>

                {{-- Instagram Handle --}}
                <div>
                    <label for="instagram_handle" class="form-label-custom">Instagram Handle</label>
                    <input type="text" name="instagram_handle" id="instagram_handle" value="{{ old('instagram_handle') }}"
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
                                <option value="{{ $ownerUser->id }}" {{ old('user_id') == $ownerUser->id ? 'selected' : '' }}>
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
                                <option value="{{ $ownerUser->id }}" {{ in_array($ownerUser->id, old('owner_ids', [])) ? 'selected' : '' }}>
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
                Register Business
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
                        placeholder: "Select Province First", 
                        searchField: ["text"],
                        valueField: 'id',
                        labelField: 'name'
                    });
                    citySelect.disable();

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
