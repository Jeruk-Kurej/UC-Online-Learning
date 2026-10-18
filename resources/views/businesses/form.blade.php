<x-app-layout>
    @section('title', $business ? 'Edit ' . $business->name : 'Register New Business')

    @push('styles')
        @vite('resources/css/vendor/tom-select.css')
    @endpush
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ $business ? route('businesses.show', $business) : $cancelUrl }}" class="btn btn-secondary">
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
        @if ($business)
            @method('PUT')
        @endif

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <h2 class="mb-6 border-b-2 border-gray-100 pb-3 text-lg font-bold text-gray-900">
                Core Information
            </h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-forms.field look="business" name="name" label="Business Name" required>
                    <input type="text" name="name" id="name" value="{{ old('name', $business?->name) }}" required
                           class="form-input-custom @error('name') border-red-500 @enderror">
                </x-forms.field>

                <x-forms.field look="business" name="category_id" label="Business Category" required>
                    <select name="category_id" id="category_id" data-tom-select="Select Category" required class="block w-full">
                        <option value="" disabled @unless ($business) selected @endunless>Select Category</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $business?->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </x-forms.field>

                <x-forms.field look="business" name="business_mode" label="Offering Type" required>
                    <select name="business_mode" id="business_mode" data-tom-select="Select Mode" required class="block w-full">
                        <option value="product" {{ old('business_mode', $business ? $business->offering_type : 'both') === 'product' ? 'selected' : '' }}>Product Only</option>
                        <option value="service" {{ old('business_mode', $business ? $business->offering_type : 'both') === 'service' ? 'selected' : '' }}>Service Only</option>
                        <option value="both" {{ old('business_mode', $business ? $business->offering_type : 'both') === 'both' ? 'selected' : '' }}>Product & Service</option>
                    </select>
                </x-forms.field>

                <x-forms.field look="business" name="established_date" label="Established Date">
                    <input type="date" name="established_date" id="established_date" value="{{ old('established_date', $business?->established_date?->format('Y-m-d')) }}"
                           class="form-input-custom @error('established_date') border-red-500 @enderror">
                </x-forms.field>

                <x-forms.field look="business" name="description" label="Description" required class="md:col-span-2">
                    <textarea name="description" id="description" rows="3" required
                              class="form-input-custom">{{ old('description', $business?->description) }}</textarea>
                </x-forms.field>

                @if ($business)
                    <x-forms.removable-image class="pb-5 md:col-span-2" label="Business Logo" name="logo" delete-name="delete_logo"
                                             :url="$business->logo_url" :has-image="$business->has_logo" :initial="$business->initial" noun="Logo"
                                             hint="JPG, PNG, WEBP — Max 20MB" box-class="w-[120px] h-[120px] rounded-xl bg-[#f8fafc] border-[1.5px] border-[#e2e8f0] group shadow-sm flex items-center justify-center p-2"
                                             actions-class="h-[120px]" image-class="">
                        @error('logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </x-forms.removable-image>
                @else
                    <x-forms.file-drop class="pb-5 md:col-span-2" label="Business Logo" name="logo" accept="image/*"
                                       prompt="Upload or drop a logo" formats="JPG, PNG, WEBP — Max 10MB">
                        <x-slot:selection>
                            <x-forms.picked-image label="Selected Logo" image-class="max-h-full max-w-full object-contain"
                                                  frame-class="flex h-20 w-20 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-gray-50 p-2 shadow-sm" />
                        </x-slot:selection>
                        @error('logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </x-forms.file-drop>
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <h2 class="mb-6 border-b-2 border-gray-100 pb-3 text-lg font-bold text-gray-900">
                Location & Contact Details
            </h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-forms.region-fields look="business" select-class="block w-full" :provinces="$provinces" :cities="$availableCities"
                                       :selected-province-id="$selectedProvinceId" :record="$business" />

                <x-forms.field look="business" name="address" label="Full Address" :show-error="false" class="md:col-span-2">
                    <textarea name="address" id="address" rows="2"
                              class="form-input-custom">{{ old('address', $business?->address) }}</textarea>
                </x-forms.field>

                <x-forms.field look="business" name="phone" label="Business Phone Number" :show-error="false">
                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $business?->phone_number) }}"
                           class="form-input-custom">
                </x-forms.field>

                <x-forms.field look="business" name="whatsapp_number" label="Business WhatsApp Number" :show-error="false">
                    <input type="tel" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $business?->whatsapp) }}"
                           class="form-input-custom" placeholder="e.g. 62812...">
                </x-forms.field>

                <x-forms.field look="business" name="email" label="Business Email" :show-error="false">
                    <input type="email" name="email" id="email" value="{{ old('email', $business?->email) }}"
                           class="form-input-custom">
                </x-forms.field>

                <x-forms.field look="business" name="website" label="Website URL" :show-error="false">
                    <input type="url" name="website" id="website" value="{{ old('website', $business?->website) }}"
                           class="form-input-custom" placeholder="https://...">
                </x-forms.field>

                <x-forms.field look="business" name="instagram_handle" label="Instagram Handle" :show-error="false">
                    <input type="text" name="instagram_handle" id="instagram_handle" value="{{ old('instagram_handle', $business?->instagram) }}"
                           class="form-input-custom" placeholder="username">
                </x-forms.field>
            </div>
        </div>

        @if (auth()->check() && auth()->user()->isAdmin())
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="mb-6 border-b-2 border-gray-100 pb-3 text-lg font-bold text-gray-900">
                    Ownership Management
                </h2>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <x-forms.field look="business" name="user_id" label="Primary Owner (Admin Only)" :show-error="false">
                        <select name="user_id" id="user_id" data-tom-select="Select Primary Owner" class="block w-full">
                            <option value="">Select Primary Owner</option>
                            @foreach ($users as $ownerUser)
                                <option value="{{ $ownerUser->id }}" {{ old('user_id', $business?->user_id) == $ownerUser->id ? 'selected' : '' }}>
                                    {{ $ownerUser->name }} ({{ $ownerUser->email }})
                                </option>
                            @endforeach
                        </select>
                    </x-forms.field>

                    <x-forms.field look="business" name="owner_ids" label="Additional Owners (Optional)" :show-error="false">
                        <select name="owner_ids[]" id="owner_ids" data-tom-select="Select Additional Owners" multiple class="block w-full">
                            @foreach ($users as $ownerUser)
                                <option value="{{ $ownerUser->id }}" {{ in_array($ownerUser->id, old('owner_ids', $memberIds)) ? 'selected' : '' }}>
                                    {{ $ownerUser->name }} ({{ $ownerUser->email }})
                                </option>
                            @endforeach
                        </select>
                    </x-forms.field>
                </div>
            </div>
        @endif

        {{-- --- Action Buttons --- --}}
        <div class="flex items-center justify-between pb-6">
            <a href="{{ $cancelUrl }}"
               class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle-fill"></i>
                {{ $business ? 'Update' : 'Register' }} Business
            </button>
        </div>
    </form>
</x-app-layout>
