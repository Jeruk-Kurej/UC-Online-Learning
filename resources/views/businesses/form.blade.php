<x-app-layout>
    @section('title', $business ? 'Edit ' . $business->name : 'Register New Business')

    @push('styles')
        @vite('resources/css/vendor/tom-select.css')
    @endpush
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ $business ? route('businesses.show', $business) : $cancelUrl }}" class="btn-uco btn-uco-secondary">
            <i class="bi bi-arrow-left"></i>
            Back
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $business ? 'Edit' : 'Register' }} Business</h1>
            <p class="text-sm text-gray-600">{{ $business ? 'Update' : 'Register your' }} business information cleanly aligned with the new database columns</p>
        </div>
    </div>

    <form method="POST" action="{{ $business ? route('businesses.update', $business) : route('businesses.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if($business)
            @method('PUT')
        @endif

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Core Information
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- --- Business Name --- --}}
                <div class="relative pb-5">
                    <label for="name" class="form-label-custom">
                        Business Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $business?->name) }}" required
                           class="form-input-custom @error('name') border-red-500 @enderror">
                    @error('name')<p class="absolute bottom-0 left-0 text-[10px] font-bold text-red-600 uppercase tracking-tight">{{ $message }}</p>@enderror
                </div>

                {{-- --- Business Category --- --}}
                <div class="relative pb-5">
                    <label for="category_id" class="form-label-custom">
                        Business Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" id="category_id" data-tom-select="Select Category" required class="block w-full">
                        <option value="" disabled @unless($business) selected @endunless>Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $business?->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="absolute bottom-0 left-0 text-[10px] font-bold text-red-600 uppercase tracking-tight">{{ $message }}</p>@enderror
                </div>

                {{-- --- Offering Type / Mode --- --}}
                <div class="relative pb-5">
                    <label for="business_mode" class="form-label-custom">
                        Offering Type <span class="text-red-500">*</span>
                    </label>
                    <select name="business_mode" id="business_mode" data-tom-select="Select Mode" required class="block w-full">
                        <option value="product" {{ old('business_mode', $business ? $business->offering_type : 'both') === 'product' ? 'selected' : '' }}>Product Only</option>
                        <option value="service" {{ old('business_mode', $business ? $business->offering_type : 'both') === 'service' ? 'selected' : '' }}>Service Only</option>
                        <option value="both" {{ old('business_mode', $business ? $business->offering_type : 'both') === 'both' ? 'selected' : '' }}>Product & Service</option>
                    </select>
                    @error('business_mode')<p class="absolute bottom-0 left-0 text-[10px] font-bold text-red-600 uppercase tracking-tight">{{ $message }}</p>@enderror
                </div>

                {{-- --- Established Date --- --}}
                <div class="relative pb-5">
                    <label for="established_date" class="form-label-custom">
                        Established Date
                    </label>
                    <input type="date" name="established_date" id="established_date" value="{{ old('established_date', $business?->established_date?->format('Y-m-d')) }}"
                           class="form-input-custom @error('established_date') border-red-500 @enderror">
                    @error('established_date')<p class="absolute bottom-0 left-0 text-[10px] font-bold text-red-600 uppercase tracking-tight">{{ $message }}</p>@enderror
                </div>

                {{-- --- Description --- --}}
                <div class="md:col-span-2 relative pb-5">
                    <label for="description" class="form-label-custom">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="3" required
                              class="form-input-custom">{{ old('description', $business?->description) }}</textarea>
                    @error('description')<p class="absolute bottom-0 left-0 text-[10px] font-bold text-red-600 uppercase tracking-tight">{{ $message }}</p>@enderror
                </div>

                {{-- --- Business Logo --- --}}
                @if($business)
                    <x-forms.removable-image class="md:col-span-2 pb-5" label="Business Logo" name="logo" delete-name="delete_logo"
                        :url="$business->logo_url" :has-image="$business->has_logo" :initial="strtoupper(substr($business->name, 0, 1))" noun="Logo"
                        hint="JPG, PNG, WEBP — Max 20MB" box-class="w-[120px] h-[120px] rounded-xl bg-[#f8fafc] border-[1.5px] border-[#e2e8f0] group shadow-sm flex items-center justify-center p-2"
                        actions-class="h-[120px]" image-class="">
                        @error('logo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </x-forms.removable-image>
                @else
                    <x-forms.file-drop class="md:col-span-2 pb-5" label="Business Logo" name="logo" accept="image/*"
                        prompt="Upload or drop a logo" formats="JPG, PNG, WEBP — Max 10MB">
                        <x-slot:selection>
                            <div x-show="selected" class="flex items-center gap-4 mb-3">
                                <div class="w-20 h-20 rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-gray-50 flex items-center justify-center p-2 flex-shrink-0">
                                    <img :src="fileUrl" class="max-w-full max-h-full object-contain">
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider" x-text="fileName">Selected Logo</span>
                                    <button type="button" @click="clear()"
                                            class="inline-flex items-center gap-1.5 text-xs text-red-500 hover:text-red-700 font-bold transition-all">
                                        <i class="bi bi-x-circle text-sm"></i>
                                        <span>Clear Selection</span>
                                    </button>
                                </div>
                            </div>
                        </x-slot:selection>
                        @error('logo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </x-forms.file-drop>
                @endif
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
            <h2 class="text-lg font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-100">
                Location & Contact Details
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- --- Province --- --}}
                <div>
                    <label for="province" class="form-label-custom">Province</label>
                    <select name="province" id="province" data-region-province data-region-city="#city" class="block w-full">
                        <option value="">Select Province</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}" {{ (old('province', $selectedProvinceId) == $prov->id) ? 'selected' : '' }}>
                                {{ ucwords(strtolower($prov->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- --- City --- --}}
                <div>
                    <label for="city" class="form-label-custom">City / Regency</label>
                    <select name="city" id="city" data-region-city-placeholder="{{ $business ? 'Select City' : 'Select Province First' }}" @unless($business) data-region-city-starts-disabled @endunless class="block w-full">
                        <option value="">Select Province First</option>
                        @foreach($availableCities as $city)
                            <option value="{{ $city->id }}" {{ (old('city', $business?->city) == $city->name) ? 'selected' : '' }}>
                                {{ ucwords(strtolower($city->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- --- Full Address --- --}}
                <div class="md:col-span-2">
                    <label for="address" class="form-label-custom">Full Address</label>
                    <textarea name="address" id="address" rows="2"
                              class="form-input-custom">{{ old('address', $business?->address) }}</textarea>
                </div>

                {{-- --- Phone Number --- --}}
                <div>
                    <label for="phone" class="form-label-custom">Business Phone Number</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $business?->phone_number) }}"
                           class="form-input-custom">
                </div>

                {{-- --- WhatsApp Number --- --}}
                <div>
                    <label for="whatsapp_number" class="form-label-custom">Business WhatsApp Number</label>
                    <input type="tel" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $business?->whatsapp) }}"
                           class="form-input-custom" placeholder="e.g. 62812...">
                </div>

                {{-- --- Email Address --- --}}
                <div>
                    <label for="email" class="form-label-custom">Business Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $business?->email) }}"
                           class="form-input-custom">
                </div>

                {{-- --- Website --- --}}
                <div>
                    <label for="website" class="form-label-custom">Website URL</label>
                    <input type="url" name="website" id="website" value="{{ old('website', $business?->website) }}"
                           class="form-input-custom" placeholder="https://...">
                </div>

                {{-- --- Instagram Handle --- --}}
                <div>
                    <label for="instagram_handle" class="form-label-custom">Instagram Handle</label>
                    <input type="text" name="instagram_handle" id="instagram_handle" value="{{ old('instagram_handle', $business?->instagram) }}"
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
                    {{-- --- Primary Owner --- --}}
                    <div>
                        <label for="user_id" class="form-label-custom">
                            Primary Owner (Admin Only)
                        </label>
                        <select name="user_id" id="user_id" data-tom-select="Select Primary Owner" class="block w-full">
                            <option value="">Select Primary Owner</option>
                            @foreach($users as $ownerUser)
                                <option value="{{ $ownerUser->id }}" {{ old('user_id', $business?->user_id) == $ownerUser->id ? 'selected' : '' }}>
                                    {{ $ownerUser->name }} ({{ $ownerUser->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- --- Additional Owners --- --}}
                    <div>
                        <label for="owner_ids" class="form-label-custom">
                            Additional Owners (Optional)
                        </label>
                        <select name="owner_ids[]" id="owner_ids" data-tom-select="Select Additional Owners" multiple class="block w-full">
                            @foreach($users as $ownerUser)
                                <option value="{{ $ownerUser->id }}" {{ in_array($ownerUser->id, old('owner_ids', $memberIds)) ? 'selected' : '' }}>
                                    {{ $ownerUser->name }} ({{ $ownerUser->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif

        {{-- --- Action Buttons --- --}}
        <div class="flex items-center justify-between pb-6">
            <a href="{{ $cancelUrl }}" 
               class="btn-uco btn-uco-neutral">
                Cancel
            </a>
            <button type="submit" class="btn-uco btn-uco-primary">
                <i class="bi bi-check-circle-fill"></i>
                {{ $business ? 'Update' : 'Register' }} Business
            </button>
        </div>
    </form>
</x-app-layout>
