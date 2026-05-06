<x-app-layout>
    <div class="w-full max-w-[1200px] mx-auto py-8 px-4" x-data="{ showRejectModal: false, showRevisionModal: false }">
        {{-- Back Button --}}
        <div class="mb-8">
            <a href="{{ route('admin.business-approvals.index') }}" 
               class="group inline-flex items-center justify-center sm:justify-start gap-2.5 px-4 py-2.5 bg-white hover:bg-gray-900 border border-gray-200 hover:border-gray-900 text-gray-700 hover:text-white rounded-xl font-medium text-sm shadow-sm hover:shadow-md transition-all duration-200">
                <i class="bi bi-arrow-left text-base group-hover:-translate-x-0.5 transition-transform duration-200"></i>
                <span>Back</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Details --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Business Main Info --}}
                <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm">
                    <div class="h-64 bg-gray-100 relative">
                        @php 
                            $cover = $business->photos->where('is_primary', true)->first()?->photo_path ?? ($business->photos->first()?->photo_path ?? null);
                            $coverUrl = $cover ? storage_image_url($cover, 'preview') : null;
                        @endphp
                        @if($coverUrl)
                            <img src="{{ $coverUrl }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-200">
                                <i class="bi bi-shop text-7xl text-gray-300"></i>
                            </div>
                        @endif
                        
                        {{-- Logo --}}
                        <div class="absolute -bottom-10 left-8">
                            @php $logo = $business->logo_url ? storage_image_url($business->logo_url, 'logo_thumb') : null; @endphp
                            @if($logo)
                                <img src="{{ $logo }}" class="w-24 h-24 rounded-2xl bg-white p-2 shadow-xl border border-white">
                            @else
                                <div class="w-24 h-24 rounded-2xl bg-white p-2 shadow-xl border border-white flex items-center justify-center text-gray-300">
                                    <i class="bi bi-building text-4xl"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-16 pb-8 px-8">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-soft-gray-100 text-soft-gray-600 text-[10px] font-bold uppercase tracking-wider">
                                        {{ optional($business->category)->name }}
                                    </span>
                                    <span class="px-2.5 py-1 rounded-lg bg-soft-gray-900 text-white text-[10px] font-bold uppercase tracking-wider">
                                        {{ $business->status_label }}
                                    </span>
                                </div>
                                <h1 class="text-3xl font-black text-gray-900 mb-2">{{ $business->name }}</h1>
                                <p class="text-gray-500 italic">{{ $business->description ?: 'No description provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Business Details Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- General Info --}}
                    <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="bi bi-info-circle text-uco-orange-500"></i>
                            General Information
                        </h3>
                        <dl class="space-y-4">
                            <div>
                                <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Business Mode</p>
                                <dd class="text-sm font-semibold text-gray-700 capitalize">{{ $business->offering_type }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Established Date</dt>
                                <dd class="text-sm font-semibold text-gray-700">{{ $business->established_date ? $business->established_date->format('d M Y') : 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Employee Count</dt>
                                <dd class="text-sm font-semibold text-gray-700">{{ $business->employee_count ?: 'N/A' }} employees</dd>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Revenue Range</p>
                                <dd class="text-sm font-semibold text-gray-700">{{ $business->revenue_range ?: 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Location & Contact --}}
                    <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="bi bi-geo-alt text-uco-orange-500"></i>
                            Contact & Location
                        </h3>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Address</dt>
                                <dd class="text-sm font-semibold text-gray-700">{{ $business->address ?: 'N/A' }}</dd>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Location</p>
                                <dd class="text-sm font-semibold text-gray-700">{{ $business->city ?: 'N/A' }}, {{ $business->province ?: 'N/A' }}</dd>
                            </div>
                            @if($business->phone_number)
                            <div>
                                <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Phone</dt>
                                <dd class="text-sm font-semibold text-gray-700">{{ $business->phone_number }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Right Column: Admin Actions --}}
            <div class="space-y-6">
                {{-- Owner Card --}}
                <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-sm">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Underlying User</h3>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-uco-orange-50 text-uco-orange-500 flex items-center justify-center text-xl font-bold">
                            {{ substr($business->user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $business->user->name }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ $business->user->role }}</p>
                        </div>
                    </div>
                </div>

                {{-- Action Card --}}
                <div class="bg-white border border-gray-200 rounded-3xl p-8 shadow-xl">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Moderation Action</h2>
                    
                    <div class="space-y-4">
                        <form action="{{ route('admin.business-approvals.approve', $business) }}" method="POST">
                            @csrf
                            <button type="submit" name="status" value="approved"
                                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-lg shadow-emerald-200 transition-all duration-200">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Approve Business</span>
                            </button>
                        </form>
                        <button @click="showRevisionModal = true"
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white hover:bg-blue-50 border-2 border-blue-500 text-blue-600 rounded-xl font-bold transition-all duration-200">
                            <i class="bi bi-pencil-square"></i>
                            <span>Request Revision</span>
                        </button>
                        <button @click="showRejectModal = true"
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white hover:bg-red-50 border-2 border-red-500 text-red-600 rounded-xl font-bold transition-all duration-200">
                            <i class="bi bi-x-circle-fill"></i>
                            <span>Reject Business</span>
                        </button>
                    </div>
                    
                    <p class="text-[10px] text-gray-400 text-center mt-6 uppercase font-bold tracking-widest leading-loose">
                        By approving, this business will be immediately visible on the public listing.
                    </p>

                    {{-- Revision Modal --}}
                    <div x-show="showRevisionModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm text-left">
                        <div @click.away="showRevisionModal = false" class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden p-8 reveal-on-scroll">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <p class="text-xs uppercase tracking-widest font-semibold text-uco-orange-600">Moderation System</p>
                                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mt-1">Request Revision</h1>
                                    <p class="text-gray-600 mt-2 text-sm sm:text-base">Explain what needs to be changed before approval.</p>
                                </div>
                                <button @click="showRevisionModal = false" class="text-gray-400 hover:text-gray-600">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            
                            <form action="{{ route('admin.business-approvals.need-revision', $business) }}" method="POST">
                                @csrf
                                <div class="mb-8">
                                    <label for="rejection_reason_revision" class="block text-sm font-semibold text-gray-700 mb-2">Revision Notes</label>
                                    <textarea name="rejection_reason" id="rejection_reason_revision" rows="4" required
                                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm"
                                              placeholder="E.g., Please upload a clearer logo, or fix the address..."></textarea>
                                </div>
                                
                                <div class="flex items-center gap-4">
                                    <button type="button" @click="showRevisionModal = false" class="flex-1 py-4 bg-gray-100 text-gray-600 font-bold rounded-2xl hover:bg-gray-200 transition-all">
                                        Cancel
                                    </button>
                                    <button type="submit" class="flex-1 py-4 bg-blue-500 text-white font-black rounded-2xl hover:bg-blue-600 transition-all shadow-lg shadow-blue-100">
                                        Send Request
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Reject Modal --}}
                    <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm text-left">
                        <div @click.away="showRejectModal = false" class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden p-8 reveal-on-scroll">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <p class="text-xs uppercase tracking-widest font-semibold text-uco-orange-600">Moderation System</p>
                                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mt-1">Reject Business</h1>
                                    <p class="text-gray-600 mt-2 text-sm sm:text-base">Provide a reason for rejecting this registration.</p>
                                </div>
                                <button @click="showRejectModal = false" class="text-gray-400 hover:text-gray-600">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            
                            <form action="{{ route('admin.business-approvals.reject', $business) }}" method="POST">
                                @csrf
                                <div class="mb-8">
                                    <label for="rejection_reason_reject" class="block text-sm font-semibold text-gray-700 mb-2">Rejection Reason</label>
                                    <textarea name="rejection_reason" id="rejection_reason_reject" rows="4" required
                                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-red-500 focus:border-red-500 transition-all duration-200 text-sm"
                                              placeholder="Provide a reason for rejecting..."></textarea>
                                </div>
                                
                                <div class="flex items-center gap-4">
                                    <button type="button" @click="showRejectModal = false" class="flex-1 py-4 bg-gray-100 text-gray-600 font-bold rounded-2xl hover:bg-gray-200 transition-all">
                                        Cancel
                                    </button>
                                    <button type="submit" class="flex-1 py-4 bg-red-500 text-white font-black rounded-2xl hover:bg-red-600 transition-all shadow-lg shadow-red-100">
                                        Confirm Rejection
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
