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

            /* Standardized Form Styles */
            .form-input-custom {
                width: 100% !important;
                border: 1.5px solid #e2e8f0 !important;
                border-radius: 7px !important;
                font-size: 13px !important;
                font-weight: 600 !important;
                color: #0f172a !important;
                transition: all 0.2s ease !important;
                box-sizing: border-box !important;
            }

            input.form-input-custom, 
            select.form-input-custom {
                height: 44px !important;
                padding: 0 15px !important;
            }

            textarea.form-input-custom {
                padding: 12px 15px !important;
            }

            .form-input-custom:focus {
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
                outline: none !important;
            }

            label.form-label-custom {
                display: block;
                font-size: 11px;
                font-weight: 800;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 8px;
            }
        </style>
    @endpush

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ auth()->user()->role === 'admin' ? route('businesses.index') : route('businesses.my') }}" 
           class="group inline-flex items-center gap-2.5 px-4 py-2.5 bg-white hover:bg-gray-900 border border-gray-200 hover:border-gray-900 text-gray-700 hover:text-white rounded-xl font-medium text-sm shadow-sm hover:shadow-md transition-all duration-200">
            <i class="bi bi-arrow-left text-base group-hover:-translate-x-0.5 transition-transform duration-200"></i>
            <span>Back</span>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Edit Business</h1>
            <p class="text-sm text-gray-600">Update business information cleanly aligned with the new database columns</p>
        </div>
    </div>

    <form method="POST" action="{{ route('businesses.update', $business) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Core Information
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Business Name --}}
                <div>
                    <label for="name" class="form-label-custom">
                        Business Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $business->name) }}" required
                           class="form-input-custom @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Business Category --}}
                <div>
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
                    @error('category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Offering Type / Mode --}}
                <div>
                    <label for="business_mode" class="form-label-custom">
                        Offering Type <span class="text-red-500">*</span>
                    </label>
                    <select name="business_mode" id="business_mode" required class="block w-full">
                        <option value="product" {{ old('business_mode', $business->offering_type) === 'product' ? 'selected' : '' }}>Product Only</option>
                        <option value="service" {{ old('business_mode', $business->offering_type) === 'service' ? 'selected' : '' }}>Service Only</option>
                        <option value="both" {{ old('business_mode', $business->offering_type) === 'both' ? 'selected' : '' }}>Product & Service</option>
                    </select>
                    @error('business_mode')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Established Date --}}
                <div>
                    <label for="established_date" class="form-label-custom">
                        Established Date
                    </label>
                    <input type="date" name="established_date" id="established_date" value="{{ old('established_date', optional($business->established_date)->format('Y-m-d')) }}"
                           class="form-input-custom @error('established_date') border-red-500 @enderror">
                    @error('established_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label for="description" class="form-label-custom">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="3" required
                              class="form-input-custom">{{ old('description', $business->description) }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
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

        @if(auth()->user()->role === 'admin')
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
            <a href="{{ auth()->user()->role === 'admin' ? route('businesses.index') : route('businesses.my') }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 hover:text-gray-900 rounded-xl transition duration-150">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center gap-2 bg-[#198754] hover:bg-[#157347] text-white font-bold rounded-lg shadow-sm hover:shadow-md transition-all duration-200"
                    style="padding: 10px 24px; font-size: 14px;">
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

                    @if(auth()->user()->role === 'admin')
                        new TomSelect('#user_id', { create: false, placeholder: "Select Primary Owner", searchField: ["text"] });
                        new TomSelect('#owner_ids', { create: false, placeholder: "Select Additional Owners", searchField: ["text"] });
                    @endif
                }
            });
        </script>
    @endpush
</x-app-layout>
