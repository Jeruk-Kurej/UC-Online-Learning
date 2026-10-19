<x-app-layout>
    @section('title', $business ? 'Edit ' . $business->name : 'Register a business')

    @push('styles')
        @vite('resources/css/vendor/tom-select.css')
    @endpush

    <div class="mx-auto max-w-3xl space-y-6">
        <x-ui.page-header :title="$business ? 'Edit business' : 'Register a business'" :subtitle="$business ? $business->name : 'Tell people what your business does. It appears in the directory after the admin team approves it.'" />

        <form method="POST" action="{{ $business ? route('businesses.update', $business) : route('businesses.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if ($business)
                @method('PUT')
            @endif

            <x-forms.section title="About the business">
                <x-forms.input name="name" label="Business name" :value="$business?->name" required />

                <x-forms.field name="category_id" label="Category" required>
                    <select name="category_id" id="category_id" data-tom-select="Choose a category" required class="block w-full">
                        <option value="" disabled @unless ($business) selected @endunless>Choose a category</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $business?->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </x-forms.field>

                <x-forms.field name="business_mode" label="What do you offer?" required>
                    <select name="business_mode" id="business_mode" data-tom-select="Choose one" required class="block w-full">
                        <option value="product" {{ old('business_mode', $business ? $business->offering_type : 'both') === 'product' ? 'selected' : '' }}>Products</option>
                        <option value="service" {{ old('business_mode', $business ? $business->offering_type : 'both') === 'service' ? 'selected' : '' }}>Services</option>
                        <option value="both" {{ old('business_mode', $business ? $business->offering_type : 'both') === 'both' ? 'selected' : '' }}>Products and services</option>
                    </select>
                </x-forms.field>

                <x-forms.input name="established_date" label="Date founded" type="date" :value="$business?->established_date?->format('Y-m-d')" />

                <x-forms.field name="description" label="Description" required class="md:col-span-2">
                    <textarea name="description" id="description" rows="4" required class="field-input">{{ old('description', $business?->description) }}</textarea>
                </x-forms.field>

                @if ($business)
                    <x-forms.removable-image class="md:col-span-2" label="Logo" name="logo" delete-name="delete_logo" :url="$business->logo_url" :has-image="$business->has_logo" :initial="$business->initial" noun="Logo" hint="JPG, PNG or WEBP, up to 20 MB.">
                        @error('logo')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </x-forms.removable-image>
                @else
                    <x-forms.file-drop class="md:col-span-2" label="Logo" name="logo" accept="image/*" prompt="Choose a logo or drop it here" formats="JPG, PNG or WEBP, up to 10 MB">
                        <x-slot:selection>
                            <x-forms.picked-image label="Selected logo" />
                        </x-slot:selection>
                        @error('logo')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </x-forms.file-drop>
                @endif
            </x-forms.section>

            <x-forms.section title="Location and contact">
                <x-forms.region-fields select-class="block w-full" :provinces="$provinces" :cities="$availableCities" :selected-province-id="$selectedProvinceId" :record="$business" />

                <x-forms.field name="address" label="Full address" :show-error="false" class="md:col-span-2">
                    <textarea name="address" id="address" rows="2" class="field-input">{{ old('address', $business?->address) }}</textarea>
                </x-forms.field>

                <x-forms.input name="phone" label="Phone number" type="tel" :value="$business?->phone_number" />
                <x-forms.input name="whatsapp_number" label="WhatsApp number" type="tel" :value="$business?->whatsapp" placeholder="For example 62812..." />
                <x-forms.input name="email" label="Email" type="email" :value="$business?->email" />
                <x-forms.input name="website" label="Website" type="url" :value="$business?->website" placeholder="https://..." />
                <x-forms.input name="instagram_handle" label="Instagram username" :value="$business?->instagram" />
            </x-forms.section>

            @if (auth()->check() && auth()->user()->isAdmin())
                <x-forms.section title="Owners">
                    <x-forms.field name="user_id" label="Main owner" required>
                        <select name="user_id" id="user_id" data-tom-select="Choose the main owner" required class="block w-full">
                            <option value="">Choose the main owner</option>
                            @foreach ($users as $ownerUser)
                                <option value="{{ $ownerUser->id }}" {{ old('user_id', $business?->user_id) == $ownerUser->id ? 'selected' : '' }}>{{ $ownerUser->name }} ({{ $ownerUser->email }})</option>
                            @endforeach
                        </select>
                    </x-forms.field>

                    <x-forms.field name="owner_ids" label="Other owners (optional)" :show-error="false">
                        <select name="owner_ids[]" id="owner_ids" data-tom-select="Choose other owners" multiple class="block w-full">
                            @foreach ($users as $ownerUser)
                                <option value="{{ $ownerUser->id }}" {{ in_array($ownerUser->id, old('owner_ids', $memberIds)) ? 'selected' : '' }}>{{ $ownerUser->name }} ({{ $ownerUser->email }})</option>
                            @endforeach
                        </select>
                    </x-forms.field>
                </x-forms.section>
            @endif

            <x-forms.actions>
                <a href="{{ $cancelUrl }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ $business ? 'Save changes' : 'Register business' }}</button>
            </x-forms.actions>
        </form>
    </div>
</x-app-layout>
