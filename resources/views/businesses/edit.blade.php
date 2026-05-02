<x-app-layout>
<div class="w-full" x-data="{ activeTab: 'basic', businessMode: '{{ old('business_mode', $business->offering_type ?? 'both') }}' }">
{{-- Page Header --}}
<div
class="bg-gradient-to-br from-white via-uco-orange-50/30 to-uco-yellow-50/30 border border-slate-200 rounded-2xl shadow-sm px-4 sm:px-8 py-6 sm:py-8 mb-8">
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
    <a href="{{ auth()->user()->role === 'admin' ? route('businesses.index') : route('businesses.my') }}"
    class="group inline-flex items-center justify-center sm:justify-start gap-2.5 px-4 py-2.5 bg-white hover:bg-gray-900 border border-gray-200 hover:border-gray-900 text-gray-700 hover:text-white rounded-xl font-medium text-sm shadow-sm hover:shadow-md transition-all duration-200 mb-4 sm:mb-0">
    <i
    class="bi bi-arrow-left text-base group-hover:-translate-x-0.5 transition-transform duration-200"></i>
    <span>Back</span>
    </a>
    <div class="flex-1 text-center sm:text-left">
    <p class="text-xs uppercase tracking-widest font-semibold text-uco-orange-600">Business Management
    </p>
    <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mt-1">Edit Business</h1>
    <p class="text-slate-600 mt-2 text-sm sm:text-base">{{ $business->name }}</p>
    <div class="mt-3 flex flex-wrap gap-2 justify-center sm:justify-start">
    <span
    class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-uco-orange-100 text-uco-orange-700">{{ $business->category->name ?? 'Business Type' }}</span>
    <span
    class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700">Mode:
    {{ ucfirst($business->offering_type ?? 'unknown') }}</span>
    </div>
    </div>
    </div>
    
    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex flex-col gap-2 min-w-[200px] w-full sm:w-auto">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Profile Quality</span>
            <span class="text-xl font-black {{ $business->profile_quality_score >= 80 ? 'text-emerald-500' : ($business->profile_quality_score >= 50 ? 'text-uco-orange-500' : 'text-red-500') }}">{{ $business->profile_quality_score }}%</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2">
            <div class="h-2 rounded-full transition-all duration-1000 {{ $business->profile_quality_score >= 80 ? 'bg-emerald-500' : ($business->profile_quality_score >= 50 ? 'bg-uco-orange-500' : 'bg-red-500') }}" style="width: {{ $business->profile_quality_score }}%"></div>
        </div>
        @if($business->profile_quality_score < 100)
            <p class="text-[10px] text-gray-400 font-medium text-right mt-1">Complete missing fields to improve score</p>
        @else
            <p class="text-[10px] text-emerald-500 font-medium text-right mt-1">Outstanding profile!</p>
        @endif
    </div>
</div>
</div>
{{-- Tab Navigation --}}
    <div class="bg-white border border-slate-200 rounded-t-2xl shadow-sm overflow-hidden border-b-0">
        <div class="flex flex-col sm:flex-row bg-slate-50/40">
            <button type="button" @click="activeTab = 'basic'"
                :class="activeTab === 'basic' ?
                    'bg-white text-uco-orange-700 border-b-2 border-uco-orange-500' :
                    'text-slate-400 hover:text-slate-600 hover:bg-slate-100/30 border-b-2 border-transparent'"
                class="flex-1 flex items-center justify-center gap-2.5 px-6 py-4 text-xs sm:text-sm font-bold transition-all duration-200 whitespace-nowrap border-r border-slate-100 last:border-r-0">
                <i class="bi bi-info-circle text-lg" :class="activeTab === 'basic' ? 'text-uco-orange-500' : 'text-slate-300'"></i>
                <span>Basic Information</span>
            </button>
            <button type="button" @click="activeTab = 'products'"
                :class="activeTab === 'products' ?
                    'bg-white text-blue-700 border-b-2 border-blue-500' :
                    'text-slate-400 hover:text-slate-600 hover:bg-slate-100/30 border-b-2 border-transparent'"
                class="flex-1 flex items-center justify-center gap-2.5 px-6 py-4 text-xs sm:text-sm font-bold transition-all duration-200 whitespace-nowrap border-r border-slate-100 last:border-r-0">
                <i class="bi bi-box-seam text-lg" :class="activeTab === 'products' ? 'text-blue-500' : 'text-slate-300'"></i>
                <span>Products & Services</span>
            </button>
            <button type="button" @click="activeTab = 'development'"
                :class="activeTab === 'development' ?
                    'bg-white text-purple-700 border-b-2 border-purple-500' :
                    'text-slate-400 hover:text-slate-600 hover:bg-slate-100/30 border-b-2 border-transparent'"
                class="flex-1 flex items-center justify-center gap-2.5 px-6 py-4 text-xs sm:text-sm font-bold transition-all duration-200 whitespace-nowrap border-r border-slate-100 last:border-r-0">
                <i class="bi bi-graph-up-arrow text-lg" :class="activeTab === 'development' ? 'text-purple-500' : 'text-slate-300'"></i>
                <span>Business Development</span>
            </button>
            <button type="button" @click="activeTab = 'documents'"
                :class="activeTab === 'documents' ?
                    'bg-white text-emerald-700 border-b-2 border-emerald-500' :
                    'text-slate-400 hover:text-slate-600 hover:bg-slate-100/30 border-b-2 border-transparent'"
                class="flex-1 flex items-center justify-center gap-2.5 px-6 py-4 text-xs sm:text-sm font-bold transition-all duration-200 whitespace-nowrap border-r border-slate-100 last:border-r-0">
                <i class="bi bi-file-earmark-check text-lg" :class="activeTab === 'documents' ? 'text-emerald-500' : 'text-slate-300'"></i>
                <span>Documents & Certifications</span>
            </button>
        </div>
    </div>
<form method="POST" action="{{ route('businesses.update', $business) }}" enctype="multipart/form-data"
id="businessEditForm">
@csrf
@method('PUT')
{{-- TAB 1: BASIC INFORMATION --}}
<div x-show="activeTab === 'basic'"
class="bg-white border-x border-b border-slate-200 rounded-b-2xl shadow-sm p-8 space-y-8">
{{-- Business Name --}}
<div>
<label for="name" class="block text-sm font-medium text-gray-700 mb-2">
Business Name <span class="text-red-500">*</span>
</label>
<input type="text" name="name" id="name" value="{{ old('name', $business->name) }}"
required
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('name') border-gray-200 @enderror transition"
placeholder="e.g., Warung Kopi Senja">
@error('name')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>

{{-- Feature Toggle --}}
<div class="rounded-2xl border border-yellow-200 bg-yellow-50/70 p-4">
    <div class="flex items-start gap-3">
        <input type="checkbox"
               name="is_featured"
               id="is_featured"
               value="1"
               {{ old('is_featured', $business->is_featured) ? 'checked' : '' }}
               class="mt-1 h-4 w-4 rounded border-gray-300 text-yellow-500 focus:ring-yellow-400">
        <div class="min-w-0">
            <label for="is_featured" class="block text-sm font-semibold text-gray-900">Feature this business</label>
            <p class="mt-1 text-xs leading-relaxed text-gray-600">
                Featured businesses are prioritized on the homepage showcase and marked with a featured badge.
            </p>
        </div>
    </div>
</div>

{{-- Position --}}
<div>
<label for="position" class="block text-sm font-medium text-gray-700 mb-2">
Your Position
</label>
<input type="text" name="position" id="position"
value="{{ old('position', $business->position) }}"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('position') border-gray-200 @enderror transition"
placeholder="e.g., CEO, Co-Founder, Marketing Manager, Staff">
@error('position')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
{{-- Business Type & Mode --}}
<div class="grid md:grid-cols-2 gap-5">
<div>
<label for="business_type_id" class="block text-sm font-medium text-gray-700 mb-2">
Business Category <span class="text-red-500">*</span>
</label>
<select name="business_type_id" id="business_type_id" required
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('business_type_id') border-gray-200 @enderror transition">
<option value="" disabled {{ old('business_type_id', $business->category_id) ? '' : 'selected' }}>Select Category</option>
@foreach ($businessTypes as $type)
<option value="{{ $type->id }}"
{{ old('business_type_id', $business->category_id) == $type->id || $business->category_id == $type->id ? 'selected' : '' }}>
{{ $type->name }}
</option>
@endforeach
</select>
@error('business_type_id')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
<div>
<label for="business_mode" class="block text-sm font-medium text-gray-700 mb-2">
Offering Type <span class="text-red-500">*</span>
</label>
<select name="business_mode" id="business_mode" required
x-model="businessMode"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('business_mode') border-gray-200 @enderror transition">
<option value="" disabled {{ old('business_mode', $business->offering_type) ? '' : 'selected' }}>Select Offering Type</option>
<option value="product"
{{ old('business_mode', $business->offering_type) == 'product' || $business->offering_type == 'product' ? 'selected' : '' }}>
Product Only</option>
<option value="service"
{{ old('business_mode', $business->offering_type) == 'service' || $business->offering_type == 'service' ? 'selected' : '' }}>
Service Only</option>
<option value="both"
{{ old('business_mode', $business->offering_type) == 'both' || $business->offering_type == 'both' ? 'selected' : '' }}>
Product & Service</option>
</select>
@error('business_mode')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
</div>
{{-- Description --}}
<div>
<label for="description" class="block text-sm font-medium text-gray-700 mb-2">
Description <span class="text-red-500">*</span>
</label>
<textarea name="description" id="description" rows="4" maxlength="1000" required
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('description') border-gray-200 @enderror transition"
placeholder="Describe your business...">{{ old('description', $business->description) }}</textarea>
<p class="mt-1 text-xs text-gray-500">Max 1000 characters.</p>
@error('description')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
<div class="grid md:grid-cols-2 gap-5">
<div>
<label for="province" class="block text-sm font-medium text-gray-700 mb-2">
Province
</label>
<input type="text" name="province" id="province"
value="{{ old('province', $business->province) }}"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('province') border-red-500 @enderror transition"
placeholder="e.g., Jawa Timur">
@error('province')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
<div>
<label for="city" class="block text-sm font-medium text-gray-700 mb-2">
City
</label>
<input type="text" name="city" id="city"
value="{{ old('city', $business->city) }}"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('city') border-red-500 @enderror transition"
placeholder="e.g., Surabaya">
@error('city')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
</div>
{{-- Full Address --}}
<div>
<label for="address" class="block text-sm font-medium text-gray-700 mb-2">
Full Address
</label>
<textarea name="address" id="address" rows="3"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('address') border-gray-200 @enderror transition"
placeholder="Full business address...">{{ old('address', $business->address) }}</textarea>
@error('address')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
{{-- Contact Info --}}
<div class="grid md:grid-cols-2 gap-5">
<div>
<label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
Phone Number
</label>
<input type="text" name="phone" id="phone"
value="{{ old('phone', $business->phone_number) }}"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('phone') border-gray-200 @enderror transition"
placeholder="e.g., 0812-3456-7890">
@error('phone')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
<div>
<label for="email" class="block text-sm font-medium text-gray-700 mb-2">
Email
</label>
<input type="email" name="email" id="email"
value="{{ old('email', $business->email) }}"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('email') border-red-500 @enderror transition"
placeholder="business@example.com">
@error('email')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
</div>
{{-- Logo Upload --}}
<div class="md:col-span-2">
<label class="block text-sm font-medium text-gray-700 mb-4">Logo Business</label>
<input type="file" name="logo" id="logo" accept="image/*" class="hidden">
@php
$currentLogoUrl = $business->logo ? storage_image_url($business->logo) : null;
@endphp
<x-image-preview input-id="logo" preview-id="edit-logo-preview" :max-size="2" shape="square"
:side-by-side="true" :current-image="$currentLogoUrl" current-label="Current" new-label="New Logo"
hint="PNG, JPG, SVG — max 2MB" />
@error('logo')
<p class="mt-2 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
{{-- Website --}}
<div>
<label for="website" class="block text-sm font-medium text-gray-700 mb-2">
Website
</label>
<input type="url" name="website" id="website"
value="{{ old('website', $business->website) }}"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('website') border-gray-200 @enderror transition"
placeholder="https://example.com">
@error('website')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
{{-- Social Media --}}
<div class="grid md:grid-cols-2 gap-5">
<div>
<label for="instagram_handle" class="block text-sm font-medium text-gray-700 mb-2">
Instagram Handle
</label>
<div class="relative">
<span class="absolute left-4 top-3.5 text-slate-400">@</span>
<input type="text" name="instagram_handle" id="instagram_handle"
value="{{ old('instagram_handle', $business->instagram) }}"
class="block w-full pl-8 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('instagram_handle') border-gray-200 @enderror transition"
placeholder="username">
</div>
@error('instagram_handle')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
<div>
<label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">
WhatsApp Number
</label>
<input type="text" name="whatsapp_number" id="whatsapp_number"
value="{{ old('whatsapp_number', $business->whatsapp) }}"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('whatsapp_number') border-gray-200 @enderror transition"
placeholder="628123456789">
@error('whatsapp_number')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
</div>
                @if (auth()->user()->role === 'admin')
                    @php
                        $selectedOwnerIds = old('owner_ids', $business->members()->pluck('users.id')->all());
                    @endphp
                    {{-- Admin Owner Section - Grid for Consistency --}}
                    <div class="grid md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Primary Owner (Admin Only)
                            </label>
                            <select name="user_id" id="user_id"
                                class="block w-full px-4 py-3 focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('user_id') border-red-500 @enderror transition">
                                <option value="">Select primary owner</option>
                                @foreach ($users as $ownerUser)
                                    <option value="{{ $ownerUser->id }}" @selected(old('user_id', $business->user_id) == $ownerUser->id)>
                                        {{ $ownerUser->name }} ({{ $ownerUser->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="owner_ids" class="block text-sm font-medium text-gray-700 mb-2">
                                Additional Owners (Optional)
                            </label>
                            <select name="owner_ids[]" id="owner_ids" multiple
                                class="block w-full px-4 py-3 focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('owner_ids') border-red-500 @enderror transition">
                                @foreach ($users as $ownerUser)
                                    <option value="{{ $ownerUser->id }}" @selected(in_array((string) $ownerUser->id, array_map('strval', $selectedOwnerIds), true))>
                                        {{ $ownerUser->name }} ({{ $ownerUser->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('owner_ids')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif
</div>
{{-- TAB 2: PRODUCTS & SERVICES --}}
<div x-show="activeTab === 'products'"
class="bg-white border-x border-b border-slate-200 rounded-b-2xl shadow-sm p-8 space-y-8">
@php
$existingProducts = old('products');
if ($existingProducts === null) {
$existingProducts = $business->products
->map(
fn($product) => [
'id' => $product->id,
'name' => $product->name,
'description' => $product->description,
'price' => $product->price,
],
)
->toArray();
}
if (empty($existingProducts)) {
$existingProducts = [];
}
$existingServices = old('services', $existingServices ?? []);
if (!is_array($existingServices)) {
    $existingServices = [];
}
if (empty($existingServices)) {
$existingServices = [];
}
@endphp
<div class="bg-gradient-to-br from-blue-50 to-white border border-blue-100 rounded-xl p-4" x-show="['product', 'both'].includes(businessMode)">
<h3 class="text-sm font-semibold text-slate-700">Products (can add multiple)</h3>
<p class="text-xs text-slate-500 mt-1">Edit all products directly here.</p>
<div id="productsContainer" class="space-y-3 mt-4">
@foreach ($existingProducts as $index => $product)
<div class="product-item border border-slate-200 rounded-xl p-4 bg-white">
<input type="hidden" name="products[{{ $index }}][id]"
value="{{ $product['id'] ?? '' }}">
<div class="flex justify-between items-center mb-3">
<p class="text-xs font-semibold text-slate-500">Product #{{ $loop->iteration }}</p>
<button type="button" onclick="this.closest('.product-item').remove()"
class="text-xs px-2 py-1 rounded-lg bg-red-50 text-red-600">Delete</button>
</div>
<div class="grid md:grid-cols-2 gap-3">
<div>
<label class="block text-xs text-slate-600 mb-1">Product Name</label>
<input type="text" name="products[{{ $index }}][name]"
value="{{ $product['name'] ?? '' }}"
class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm"
placeholder="e.g., Arabica Coffee 250gr">
</div>
<div>
<label class="block text-xs text-slate-600 mb-1">Price</label>
<input type="number" step="any" min="0"
name="products[{{ $index }}][price]"
value="{{ $product['price'] ?? '' }}"
class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm"
placeholder="50000">
</div>
</div>
<div class="mt-3">
<label class="block text-xs text-slate-600 mb-1">Description</label>
<textarea name="products[{{ $index }}][description]" rows="2"
class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="Product description...">{{ $product['description'] ?? '' }}</textarea>
</div>
</div>
@endforeach
</div>
<button type="button" onclick="addProductRow()"
class="mt-3 px-4 py-2 bg-slate-100 text-gray-700 rounded-xl text-sm font-medium">+
Add Product</button>
</div>
<div class="bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 rounded-xl p-4" x-show="['service', 'both'].includes(businessMode)">
<h3 class="text-sm font-semibold text-slate-700">Services (can add multiple)</h3>
<p class="text-xs text-slate-500 mt-1">Edit all services directly here.</p>
<div id="servicesContainer" class="space-y-3 mt-4">
@foreach ($existingServices as $index => $service)
<div class="service-item border border-slate-200 rounded-xl p-4 bg-white">
<input type="hidden" name="services[{{ $index }}][id]"
value="{{ $service['id'] ?? '' }}">
<div class="flex justify-between items-center mb-3">
<p class="text-xs font-semibold text-slate-500">Service #{{ $loop->iteration }}
</p>
<button type="button" onclick="this.closest('.service-item').remove()"
class="text-xs px-2 py-1 rounded-lg bg-red-50 text-red-600">Delete</button>
</div>
<div class="grid md:grid-cols-3 gap-3">
<div>
<label class="block text-xs text-slate-600 mb-1">Service Name</label>
<input type="text" name="services[{{ $index }}][name]"
value="{{ $service['name'] ?? '' }}"
class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm"
placeholder="e.g., Branding Consultation">
</div>
<div>
<label class="block text-xs text-slate-600 mb-1">Price Type</label>
<input type="text" name="services[{{ $index }}][price_type]"
value="{{ $service['price_type'] ?? 'fixed' }}"
class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm"
placeholder="fixed / per session / per hour">
</div>
<div>
<label class="block text-xs text-slate-600 mb-1">Price</label>
<input type="number" step="any" min="0"
name="services[{{ $index }}][price]"
value="{{ $service['price'] ?? '' }}"
class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm"
placeholder="150000">
</div>
</div>
<div class="mt-3">
<label class="block text-xs text-slate-600 mb-1">Description</label>
<textarea name="services[{{ $index }}][description]" rows="2"
class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="Service description...">{{ $service['description'] ?? '' }}</textarea>
</div>
</div>
@endforeach
</div>
<button type="button" onclick="addServiceRow()"
class="mt-3 px-4 py-2 bg-slate-100 text-gray-700 rounded-xl text-sm font-medium">+
Add Service</button>
</div>
{{-- Product Name --}}
<div>
<label for="product_name" class="block text-sm font-medium text-gray-700 mb-2">
Primary Product/Service Name
</label>
<input type="text" name="product_name" id="product_name"
value="{{ old('product_name', $business->product_name) }}"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('product_name') border-gray-200 @enderror transition"
placeholder="e.g., Kopi Arabica Premium">
@error('product_name')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
{{-- Product Description --}}
<div>
<label for="product_description" class="block text-sm font-medium text-gray-700 mb-2">
Product/Service Description
</label>
<textarea name="product_description" id="product_description" rows="4"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('product_description') border-gray-200 @enderror transition"
placeholder="Describe the products/services you offer...">{{ old('product_description', $business->product_description) }}</textarea>
@error('product_description')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
{{-- Unique Value Proposition --}}
<div>
<label for="unique_value_proposition" class="block text-sm font-medium text-gray-700 mb-2">
Unique Value Proposition
</label>
<textarea name="unique_value_proposition" id="unique_value_proposition" rows="3"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('unique_value_proposition') border-gray-200 @enderror transition"
placeholder="What makes your product different from competitors?">{{ old('unique_value_proposition', $business->unique_value_proposition) }}</textarea>
@error('unique_value_proposition')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
{{-- Target Market --}}
<div>
<label for="target_market" class="block text-sm font-medium text-gray-700 mb-2">
Target Market
</label>
<input type="text" name="target_market" id="target_market"
value="{{ old('target_market', $business->target_market) }}"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('target_market') border-gray-200 @enderror transition"
placeholder="e.g., Millennial, Young Professionals, Coffee Lovers">
@error('target_market')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
{{-- Customer Base --}}
<div>
<label for="customer_base_size" class="block text-sm font-medium text-gray-700 mb-2">
Customer Base Size
</label>
<input type="number" name="customer_base_size" id="customer_base_size"
value="{{ old('customer_base_size', $business->customer_base_size) }}" min="0"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('customer_base_size') border-gray-200 @enderror transition"
placeholder="e.g., 500">
@error('customer_base_size')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
</div>
{{-- TAB 3: BUSINESS DEVELOPMENT --}}
<div x-show="activeTab === 'development'"
class="bg-white border-x border-b border-slate-200 rounded-b-2xl shadow-sm p-8 space-y-8">
{{-- Establishment Date & Operational Status --}}
<div class="grid md:grid-cols-2 gap-5">
<div>
<label for="established_date" class="block text-sm font-medium text-gray-700 mb-2">
Established Date
</label>
<input type="date" name="established_date" id="established_date"
value="{{ old('established_date', $business->established_date) }}"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('established_date') border-red-500 @enderror transition">
@error('established_date')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
<div>
<label for="operational_status" class="block text-sm font-medium text-gray-700 mb-2">
Operational Status
</label>
<select name="operational_status" id="operational_status"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('operational_status') border-red-500 @enderror transition">
<option value="">Select Status</option>
@php
$currentOpsStatus = old('operational_status', $business->operational_status ?? ($business->additional_data['operational_status'] ?? ''));
@endphp
<option value="active" {{ $currentOpsStatus == 'active' ? 'selected' : '' }}>Active</option>
<option value="inactive" {{ $currentOpsStatus == 'inactive' ? 'selected' : '' }}>Inactive</option>
<option value="seasonal" {{ $currentOpsStatus == 'seasonal' ? 'selected' : '' }}>Seasonal</option>
</select>
@error('operational_status')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
</div>
{{-- Employee Count & Revenue Range --}}
<div class="grid md:grid-cols-2 gap-5">
<div>
<label for="employee_count" class="block text-sm font-medium text-gray-700 mb-2">
Employee Count
</label>
<input type="number" name="employee_count" id="employee_count"
value="{{ old('employee_count', $business->employee_count) }}" min="0"
class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 @error('employee_count') border-red-500 @enderror transition"
placeholder="e.g., 5">
<p class="mt-1 text-[10px] text-gray-400">Total number of permanent & contract staff.</p>
@error('employee_count')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
<div>
<label for="revenue_range" class="block text-sm font-medium text-gray-700 mb-2">
Revenue Range (per month)
</label>
<select name="revenue_range" id="revenue_range" class="w-full">
<option value="">Select Range</option>
<option value="0 - 50 Million" {{ $business->revenue_range == '0 - 50 Million' ? 'selected' : '' }}>0 - 50 Million</option>
<option value="50 - 100 Million" {{ $business->revenue_range == '50 - 100 Million' ? 'selected' : '' }}>50 - 100 Million</option>
<option value="100 - 500 Million" {{ $business->revenue_range == '100 - 500 Million' ? 'selected' : '' }}>100 - 500 Million</option>
<option value="500 Million - 1 Billion" {{ $business->revenue_range == '500 Million - 1 Billion' ? 'selected' : '' }}>500 Million - 1 Billion</option>
<option value="> 1 Billion" {{ $business->revenue_range == '> 1 Billion' ? 'selected' : '' }}>> 1 Billion</option>
@if($business->revenue_range && !in_array($business->revenue_range, ['0 - 50 Million', '50 - 100 Million', '100 - 500 Million', '500 Million - 1 Billion', '> 1 Billion']))
<option value="{{ $business->revenue_range }}" selected>{{ $business->revenue_range }}</option>
@endif
</select>
<p class="mt-1 text-[10px] text-gray-400">Searchable dropdown. You can also type custom values.</p>
@error('revenue_range')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
</div>
{{-- Academic Heritage (NEW SECTION) --}}
<div class="p-5 bg-orange-50/50 border border-orange-100 rounded-2xl space-y-4">
<div class="flex items-center gap-2 mb-1">
<i class="bi bi-mortarboard text-uco-orange-500"></i>
<h4 class="text-sm font-bold text-uco-orange-900 uppercase tracking-wider">Academic Heritage</h4>
</div>
<div class="flex flex-col sm:flex-row gap-6">
<label class="relative flex items-center cursor-pointer group">
<input type="checkbox" name="is_from_college_project" value="1" 
{{ old('is_from_college_project', $business->is_from_college_project) ? 'checked' : '' }}
class="w-5 h-5 rounded border-gray-300 text-uco-orange-600 focus:ring-uco-orange-500 transition">
<span class="ml-3 text-sm font-semibold text-gray-700 group-hover:text-uco-orange-700 transition">Evolution of College Project</span>
</label>
<label class="relative flex items-center cursor-pointer group">
<input type="checkbox" name="is_continued_after_graduation" value="1" 
{{ old('is_continued_after_graduation', $business->is_continued_after_graduation) ? 'checked' : '' }}
class="w-5 h-5 rounded border-gray-300 text-uco-orange-600 focus:ring-uco-orange-500 transition">
<span class="ml-3 text-sm font-semibold text-gray-700 group-hover:text-uco-orange-700 transition">Sustained Post-Graduation</span>
</label>
</div>
<p class="text-[11px] text-uco-orange-600/70 font-medium italic leading-relaxed">
Ticking these boxes helps track the impact of UCO startup incubation programs on alumni career paths.
</p>
</div>
{{-- Business Challenges (Dynamic) --}}
<div>
<label class="block text-sm font-medium text-gray-700 mb-2">
Business Challenges
</label>
<div id="challengesContainer" class="space-y-3">
@if ($business->business_challenges && count($business->business_challenges) > 0)
@foreach ($business->business_challenges as $challenge)
<div class="flex gap-2">
<input type="text" name="business_challenges[]" value="{{ $challenge }}"
class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 transition"
placeholder="e.g., Limited capital">
<button type="button" onclick="this.parentElement.remove()"
class="px-4 py-2 bg-red-100 text-red-700 rounded-xl">
✖
</button>
</div>
@endforeach
@else
<div class="flex gap-2">
<input type="text" name="business_challenges[]"
class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 transition"
placeholder="e.g., Limited capital">
</div>
@endif
</div>
<button type="button" onclick="addChallenge()"
class="mt-3 px-4 py-2 bg-slate-100 text-gray-700 rounded-xl text-sm font-medium">
+ Add Challenge
</button>
@error('business_challenges')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
</div>
{{-- TAB 4: DOCUMENTS & CERTIFICATIONS --}}
<div x-show="activeTab === 'documents'"
class="bg-white border-x border-b border-slate-200 rounded-b-2xl shadow-sm p-8 space-y-8">
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
<!-- Dokumen Legal -->
<div x-data="{
files: [],
handleDrop(e) { e.preventDefault(); if (e.dataTransfer.files.length) this.addFiles(Array.from(e.dataTransfer.files)); },
handleSelect(e) { if (e.target.files.length) this.addFiles(Array.from(e.target.files)); },
addFiles(newFiles) {
const validFiles = newFiles.filter(f => f.size <= 20 * 1024 * 1024);
if (newFiles.length !== validFiles.length) {
showValidationToast('Error: Some files are too large (Max 20MB per file).');
}
this.files = [...this.files, ...validFiles];
this.syncInput();
},
removeFile(index) {
this.files.splice(index, 1);
this.syncInput();
},
syncInput() {
const dt = new DataTransfer();
this.files.forEach(f => dt.items.add(f));
this.$refs.fileInput.files = dt.files;
},
megabytes(b) { return (b / (1024 * 1024)).toFixed(2) + ' MB'; }
}">
<label class="block text-sm font-medium text-gray-700 mb-2">
Legal Documents (Can add multiple)
</label>
@if (!empty($legalDocs))
<div class="mb-3 p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
<p class="text-xs font-semibold text-slate-600">Current documents:</p>
@foreach ($legalDocs as $index => $doc)
@php
$docUrl = storage_image_url($doc['file_path'] ?? null);
@endphp
<label class="flex items-center justify-between gap-3 text-xs">
@if ($docUrl)
<a href="{{ $docUrl }}" target="_blank"
class="text-blue-600 hover:underline truncate">
{{ $doc['original_name'] ?? basename($doc['file_path']) }}
</a>
@else
<span class="truncate text-gray-500">
{{ $doc['original_name'] ?? basename($doc['file_path']) }}
(file not found)
</span>
@endif
<span class="inline-flex items-center gap-2 shrink-0">
<input type="checkbox" name="remove_legal_docs[]"
value="{{ $index }}"
class="rounded border-gray-300 text-red-600">
<span class="text-red-600">Delete</span>
</span>
</label>
@endforeach
</div>
@endif
<div class="relative group" @dragover.prevent @drop="handleDrop">
<label
class="flex flex-col items-center w-full px-4 py-4 bg-white border-2 border-slate-200 border-dashed rounded-xl cursor-pointer hover:border-uco-orange-500 hover:bg-orange-50/30 transition-all duration-300">
<div class="flex flex-row items-center justify-center gap-3 w-full">
<div
class="w-10 h-10 shrink-0 rounded-full bg-slate-50 flex items-center justify-center border border-slate-100 group-hover:bg-uco-orange-100 group-hover:border-uco-orange-200 transition-colors">
<i
class="bi bi-cloud-arrow-up text-lg text-slate-400 group-hover:text-uco-orange-600 transition-colors"></i>
</div>
<div class="flex-1 min-w-0">
<p
class="text-sm font-semibold text-slate-700 group-hover:text-uco-orange-700 truncate">
Select Document
</p>
<p class="text-[10px] text-slate-500 truncate">Format PDF/JPG/PNG (Max 20MB)
</p>
</div>
<div
class="shrink-0 px-3 py-1.5 bg-slate-100 text-slate-600 text-xs font-medium rounded-lg group-hover:bg-uco-orange-500 group-hover:text-white transition-colors shadow-sm">
Browse
</div>
</div>
<input type="file" x-ref="fileInput" name="legal_documents[]" multiple
accept=".pdf,.jpg,.jpeg,.png" class="hidden" @change="handleSelect($event)">
</label>
</div>
<ul x-show="files.length > 0" class="mt-3 space-y-2" x-transition>
<template x-for="(file, index) in files" :key="index">
<li
class="flex items-center justify-between p-2.5 bg-white border border-slate-200 rounded-xl shadow-sm hover:border-slate-300 transition">
<div class="flex items-center gap-2.5 overflow-hidden">
<div
class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
<i class="bi bi-file-earmark-check"></i>
</div>
<div class="min-w-0 pr-2">
<p class="text-xs font-semibold text-slate-700 truncate"
x-text="file.name"></p>
<p class="text-[10px] text-slate-500 font-medium"
x-text="megabytes(file.size)"></p>
</div>
</div>
<button type="button" @click.prevent="removeFile(index)"
class="shrink-0 w-7 h-7 flex items-center justify-center rounded-lg text-red-400 hover:text-white hover:bg-red-500 transition-colors">
<i class="bi bi-x-lg text-sm"></i>
</button>
</li>
</template>
</ul>
@error('legal_documents')
<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
@enderror
@error('legal_documents.*')
<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
@enderror
</div>
<!-- Sertifikasi Produk -->
<div x-data="{
files: [],
handleDrop(e) { e.preventDefault(); if (e.dataTransfer.files.length) this.addFiles(Array.from(e.dataTransfer.files)); },
handleSelect(e) { if (e.target.files.length) this.addFiles(Array.from(e.target.files)); },
addFiles(newFiles) {
const validFiles = newFiles.filter(f => f.size <= 20 * 1024 * 1024);
if (newFiles.length !== validFiles.length) {
showValidationToast('Error: Some files are too large (Max 20MB per file).');
}
this.files = [...this.files, ...validFiles];
this.syncInput();
},
removeFile(index) {
this.files.splice(index, 1);
this.syncInput();
},
syncInput() {
const dt = new DataTransfer();
this.files.forEach(f => dt.items.add(f));
this.$refs.fileInput.files = dt.files;
},
megabytes(b) { return (b / (1024 * 1024)).toFixed(2) + ' MB'; }
}">
<label class="block text-sm font-medium text-gray-700 mb-2">
Product Certification (Can add multiple)
</label>
@if (!empty($certifications))
<div class="mb-3 p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
<p class="text-xs font-semibold text-slate-600">Current certifications:</p>
@foreach ($certifications as $index => $cert)
@php
$certUrl = storage_image_url($cert['file_path'] ?? null);
@endphp
<label class="flex items-center justify-between gap-3 text-xs">
@if ($certUrl)
<a href="{{ $certUrl }}" target="_blank"
class="text-blue-600 hover:underline truncate">
{{ $cert['original_name'] ?? basename($cert['file_path']) }}
</a>
@else
<span class="truncate text-gray-500">
{{ $cert['original_name'] ?? basename($cert['file_path']) }}
(file not found)
</span>
@endif
<span class="inline-flex items-center gap-2 shrink-0">
<input type="checkbox" name="remove_certifications[]"
value="{{ $index }}"
class="rounded border-gray-300 text-red-600">
<span class="text-red-600">Delete</span>
</span>
</label>
@endforeach
</div>
@endif
<div class="relative group" @dragover.prevent @drop="handleDrop">
<label
class="flex flex-col items-center w-full px-4 py-4 bg-white border-2 border-slate-200 border-dashed rounded-xl cursor-pointer hover:border-uco-orange-500 hover:bg-orange-50/30 transition-all duration-300">
<div class="flex flex-row items-center justify-center gap-3 w-full">
<div
class="w-10 h-10 shrink-0 rounded-full bg-slate-50 flex items-center justify-center border border-slate-100 group-hover:bg-uco-orange-100 group-hover:border-uco-orange-200 transition-colors">
<i
class="bi bi-cloud-arrow-up text-lg text-slate-400 group-hover:text-uco-orange-600 transition-colors"></i>
</div>
<div class="flex-1 min-w-0">
<p
class="text-sm font-semibold text-slate-700 group-hover:text-uco-orange-700 truncate">
Select Certification
</p>
<p class="text-[10px] text-slate-500 truncate">Format PDF/JPG/PNG (Max 20MB)
</p>
</div>
<div
class="shrink-0 px-3 py-1.5 bg-slate-100 text-slate-600 text-xs font-medium rounded-lg group-hover:bg-uco-orange-500 group-hover:text-white transition-colors shadow-sm">
Browse
</div>
</div>
<input type="file" x-ref="fileInput" name="product_certifications[]" multiple
accept=".pdf,.jpg,.jpeg,.png" class="hidden" @change="handleSelect($event)">
</label>
</div>
<ul x-show="files.length > 0" class="mt-3 space-y-2" x-transition>
<template x-for="(file, index) in files" :key="index">
<li
class="flex items-center justify-between p-2.5 bg-white border border-slate-200 rounded-xl shadow-sm hover:border-slate-300 transition">
<div class="flex items-center gap-2.5 overflow-hidden">
<div
class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
<i class="bi bi-file-earmark-check"></i>
</div>
<div class="min-w-0 pr-2">
<p class="text-xs font-semibold text-slate-700 truncate"
x-text="file.name"></p>
<p class="text-[10px] text-slate-500 font-medium"
x-text="megabytes(file.size)"></p>
</div>
</div>
<button type="button" @click.prevent="removeFile(index)"
class="shrink-0 w-7 h-7 flex items-center justify-center rounded-lg text-red-400 hover:text-white hover:bg-red-500 transition-colors">
<i class="bi bi-x-lg text-sm"></i>
</button>
</li>
</template>
</ul>
@error('product_certifications')
<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
@enderror
@error('product_certifications.*')
<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
@enderror
</div>
</div>
</div>
{{-- Validation Error Toast (hidden by default) --}}
<div id="validationToast" class="hidden pointer-events-auto fixed top-6 right-6 z-[200] max-w-sm w-full bg-red-600 text-white px-4 py-3 rounded-lg shadow-lg flex items-start justify-between gap-3 transform transition-all duration-300 translate-y-[-8px] opacity-0">
<div class="flex items-center gap-2">
<i class="bi bi-exclamation-triangle-fill text-lg"></i>
<div class="flex flex-col">
<span class="text-sm font-medium">Validation Failed!</span>
<span id="validationToastMsg" class="text-xs text-red-100 font-medium">Ensure all fields are filled correctly.</span>
</div>
</div>
<button type="button" onclick="hideValidationToast()" class="text-white opacity-90 hover:opacity-100 transition-opacity flex-shrink-0 mt-0.5">
<i class="bi bi-x-lg pointer-events-none"></i>
</button>
</div>
</form>
{{-- Action Buttons --}}
<div class="sticky bottom-4 z-40 mt-8">
<div
class="flex items-center justify-between gap-3 p-3 sm:p-4 rounded-2xl border border-slate-200 bg-white/95 backdrop-blur shadow-lg">
<a href="{{ route('businesses.show', $business) }}"
class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 hover:text-gray-900 rounded-xl transition duration-150">
Cancel
</a>
<div class="flex items-center gap-3">
<form method="POST" action="{{ route('businesses.destroy', $business) }}"
onsubmit="return confirm('Are you sure you want to delete this business? This action cannot be undone.');">
@csrf
@method('DELETE')
<button type="submit"
class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-md transition duration-200">
Delete Business
</button>
</form>
<button type="submit" form="businessEditForm"
class="inline-flex items-center gap-2 px-6 py-2.5 bg-soft-gray-900 hover:bg-soft-gray-800 text-white font-semibold rounded-xl shadow-md transition duration-200">
Update Business
</button>
</div>
</div>
</div>
</div>
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
            padding: 12px 16px !important; /* EXACT match for px-4 py-3 */
            min-height: 50px !important;
            width: 100% !important;
            box-sizing: border-box !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            background: white !important;
            display: flex !important;
            align-items: center !important;
        }

        .ts-wrapper.focus .ts-control {
            border-color: #111827 !important; /* Soft Gray 900 */
            box-shadow: 0 0 0 4px rgba(17, 24, 39, 0.05) !important;
            ring: none !important;
        }

        /* Style for the tags/items in multi-select - PREMIUM LOOK */
        /* Increased specificity to override default TomSelect blue */
        html body .ts-wrapper.multi .ts-control>div,
        html body .ts-wrapper.multi.has-items .ts-control>div {
            background: #f8fafc !important; /* Very Light Slate */
            color: #1e293b !important; /* Deep Slate Text */
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            padding: 3px 10px !important;
            margin: 3px 6px 3px 0 !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            display: inline-flex !important;
            align-items: center !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
            transition: all 0.2s ease !important;
        }

        html body .ts-wrapper.multi .ts-control>div:hover {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }

        /* Remove button on tags - Premium styling */
        html body .ts-wrapper.multi .ts-control>div .remove {
            border-left: 1px solid #e2e8f0 !important;
            margin-left: 8px !important;
            padding-left: 8px !important;
            color: #94a3b8 !important;
            font-size: 16px !important;
            line-height: 1 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
        }

        .ts-wrapper.multi .ts-control>div .remove:hover {
            color: #ef4444 !important;
            background: rgba(239, 68, 68, 0.05) !important;
        }

        /* Dropdown Styling */
        .ts-dropdown {
            background-color: white !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 1rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            margin-top: 6px !important;
            padding: 6px !important;
            z-index: 1000 !important;
            animation: ts-dropdown-fade-in 0.2s ease-out;
        }

        @keyframes ts-dropdown-fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .ts-dropdown .option {
            padding: 10px 16px !important;
            font-size: 13px !important;
            color: #475569 !important;
            border-radius: 0.75rem !important;
            margin-bottom: 2px !important;
            transition: all 0.15s ease !important;
        }

        .ts-dropdown .option.active {
            background-color: #fff7ed !important; /* Soft Orange Tint */
            color: #f97316 !important; /* UCO Orange */
            font-weight: 600 !important;
        }

        .ts-dropdown .option.active.create {
            color: #f97316 !important;
        }

        .ts-wrapper .ts-control>input {
            font-size: 14px !important;
            font-family: inherit !important;
        }

        /* Fix for sticky bar layering */
        .ts-dropdown-content {
            max-height: 250px !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    {{-- JavaScript for Dynamic Fields and Client-Side Validation --}}
    <script>
        let productIndex = 0;
        let serviceIndex = 0;



        function addChallenge() {
            const container = document.getElementById('challengesContainer');
            const newChallenge = document.createElement('div');
            newChallenge.className = 'flex gap-2';
            newChallenge.innerHTML = `
                <input type="text" 
                       name="business_challenges[]" 
                       class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-soft-gray-900 focus:border-soft-gray-900 transition"
                       placeholder="Enter challenge">
                <button type="button" 
                        onclick="this.parentElement.remove()"
                        class="px-4 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition">
                    ✖
                </button>
            `;
            container.appendChild(newChallenge);
        }

        function addProductRow(data = {}) {
            const container = document.getElementById('productsContainer');
            const idx = productIndex++;
            const element = document.createElement('div');
            element.className = 'product-item border border-slate-200 rounded-xl p-4 bg-white';
            element.innerHTML = `
                <input type="hidden" name="products[${idx}][id]" value="${data.id || ''}">
                <div class="flex justify-between items-center mb-3">
                    <p class="text-xs font-semibold text-slate-500">New Product</p>
                    <button type="button" onclick="this.closest('.product-item').remove()" class="text-xs px-2 py-1 rounded-lg bg-red-50 text-red-600 hover:bg-red-100">Delete</button>
                </div>
                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">Product Name</label>
                        <input type="text" name="products[${idx}][name]" value="${data.name || ''}" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="e.g., Arabica Coffee 250gr">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">Price</label>
                        <input type="number" step="any" min="0" name="products[${idx}][price]" value="${data.price || ''}" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="50000">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs text-slate-600 mb-1">Description</label>
                    <textarea name="products[${idx}][description]" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="Product description...">${data.description || ''}</textarea>
                </div>
            `;
            container.appendChild(element);
        }

        function addServiceRow(data = {}) {
            const container = document.getElementById('servicesContainer');
            const idx = serviceIndex++;
            const element = document.createElement('div');
            element.className = 'service-item border border-slate-200 rounded-xl p-4 bg-white';
            element.innerHTML = `
                <input type="hidden" name="services[${idx}][id]" value="${data.id || ''}">
                <div class="flex justify-between items-center mb-3">
                    <p class="text-xs font-semibold text-slate-500">New Service</p>
                    <button type="button" onclick="this.closest('.service-item').remove()" class="text-xs px-2 py-1 rounded-lg bg-red-50 text-red-600 hover:bg-red-100">Delete</button>
                </div>
                <div class="grid md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">Service Name</label>
                        <input type="text" name="services[${idx}][name]" value="${data.name || ''}" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="e.g., Branding Consultation">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">Price Type</label>
                        <input type="text" name="services[${idx}][price_type]" value="${data.price_type || 'fixed'}" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="fixed / per session / per hour">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">Price</label>
                        <input type="number" step="any" min="0" name="services[${idx}][price]" value="${data.price || ''}" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="150000">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs text-slate-600 mb-1">Description</label>
                    <textarea name="services[${idx}][description]" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="Service description...">${data.description || ''}</textarea>
                </div>
            `;
            container.appendChild(element);
        }

        // Client-side form validation
        let toastTimeout = null;
        function showValidationToast(msg) {
            const toast = document.getElementById('validationToast');
            const toastMsg = document.getElementById('validationToastMsg');
            if (msg) toastMsg.textContent = msg;
            toast.classList.remove('hidden');
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-[-8px]', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            });
            if (toastTimeout) clearTimeout(toastTimeout);
            toastTimeout = setTimeout(() => hideValidationToast(), 6000);
        }

        function hideValidationToast() {
            const toast = document.getElementById('validationToast');
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-[-8px]', 'opacity-0');
            setTimeout(() => toast.classList.add('hidden'), 300);
        }

            // Region JS removed as provinces table is deleted

            // Initialize TomSelect for Business Type
            const typeSelect = document.getElementById("business_type_id");
            if (typeSelect && window.TomSelect) {
                new TomSelect(typeSelect, {
                    create: false,
                    placeholder: "Select Category",
                    searchField: ["text"],
                });
            }

            // Initialize TomSelect for Offering Type (Business Mode)
            const modeSelect = document.getElementById("business_mode");
            if (modeSelect && window.TomSelect) {
                const modeTS = new TomSelect(modeSelect, {
                    create: false,
                    placeholder: "Select Offering Type",
                    searchField: ["text"],
                });
                // Sync with Alpine.js
                modeTS.on('change', () => {
                    modeSelect.dispatchEvent(new Event('change', { bubbles: true }));
                });
            }

            // Initialize TomSelect for Operational Status
            const statusSelect = document.getElementById("operational_status");
            if (statusSelect && window.TomSelect) {
                new TomSelect(statusSelect, {
                    create: false,
                    placeholder: "Select Status",
                    searchField: ["text"],
                });
            }

            // Initialize TomSelect for owner selects
            const userIdSelect = document.getElementById("user_id");
            const ownerIdsSelect = document.getElementById("owner_ids");

            if (userIdSelect && window.TomSelect) {
                new TomSelect(userIdSelect, {
                    create: false,
                    placeholder: "Select primary owner",
                    searchField: ["text"],
                });
            }

            if (ownerIdsSelect && window.TomSelect) {
                new TomSelect(ownerIdsSelect, {
                    create: false,
                    placeholder: "Select additional owners",
                    plugins: ["remove_button"],
                    searchField: ["text"],
                });
            }

            // Initialize TomSelect for Revenue Range
            const revenueSelect = document.getElementById("revenue_range");
            if (revenueSelect && window.TomSelect) {
                new TomSelect(revenueSelect, {
                    create: true,
                    placeholder: "Select or type revenue range",
                });
            }

            // Form Submit Logic
            document.getElementById('businessEditForm').addEventListener('submit', function(e) {
                this.querySelectorAll('.validation-error-border').forEach(el => {
                    el.classList.remove('validation-error-border', 'border-red-400', 'ring-2', 'ring-red-100');
                });
                this.querySelectorAll('.validation-error-msg').forEach(el => el.remove());

                const requiredFields = this.querySelectorAll('[required]');
                let firstInvalid = null;
                const emptyLabels = [];

                requiredFields.forEach(field => {
                    if (field.type === 'file') return;
                    const val = (field.value || '').trim();
                    if (!val || (field.tagName === 'SELECT' && val === '')) {
                        field.classList.add('validation-error-border', 'border-red-400', 'ring-2', 'ring-red-100');
                        const wrapper = field.closest('div');
                        const label = wrapper ? wrapper.querySelector('label') : null;
                        const labelText = label ? label.textContent.replace('*', '').trim() : field.name;
                        emptyLabels.push(labelText);

                        if (wrapper && !wrapper.querySelector('.validation-error-msg')) {
                            const errMsg = document.createElement('p');
                            errMsg.className = 'validation-error-msg mt-1.5 text-sm text-red-600 flex items-center gap-1.5';
                            errMsg.innerHTML = '<i class="bi bi-exclamation-triangle"></i> <span>' + labelText + ' is required</span>';
                            field.insertAdjacentElement('afterend', errMsg);
                        }
                        if (!firstInvalid) firstInvalid = field;
                    }
                });

                if (firstInvalid) {
                    e.preventDefault();
                    const tabPanel = firstInvalid.closest('[x-show]');
                    if (tabPanel) {
                        const xShow = tabPanel.getAttribute('x-show');
                        const match = xShow.match(/'([\w]+)'/);
                        if (match && window.Alpine) {
                            const targetTab = match[1];
                            const rootEl = document.querySelector('[x-data*="activeTab"]');
                            if (rootEl) Alpine.$data(rootEl).activeTab = targetTab;
                        }
                    }
                    setTimeout(() => {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalid.focus();
                    }, 400);
                    showValidationToast('There are ' + emptyLabels.length + ' required fields left empty.');
                }
            });
        });
    </script>
@endpush
</x-app-layout>
