<x-app-layout>
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Page Header --}}
        <section class="relative overflow-hidden rounded-xl border border-uco-orange-100 bg-white px-6 py-8 shadow-sm md:px-8 md:py-10 mb-12 reveal-on-scroll">
            <div class="uco-hero-mesh"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-uco-orange-700">
                        Admin Portal
                    </span>
                    <h1 class="text-3xl font-extrabold text-gray-900 md:text-4xl">Business Approvals</h1>
                    <p class="text-sm text-gray-500 mt-1">Review and moderate business registrations before they go public.</p>
                </div>
                
                <div class="flex flex-wrap gap-2 bg-gray-100 p-1.5 rounded-lg">
                    <a href="{{ route('admin.business-approvals.index', ['status' => 'pending']) }}" 
                       class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ $status === 'pending' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Pending
                    </a>
                    <a href="{{ route('admin.business-approvals.index', ['status' => 'need_revision']) }}" 
                       class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ $status === 'need_revision' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Needs Revision
                    </a>
                    <a href="{{ route('admin.business-approvals.index', ['status' => 'approved']) }}" 
                       class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ $status === 'approved' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Approved
                    </a>
                    <a href="{{ route('admin.business-approvals.index', ['status' => 'rejected']) }}" 
                       class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ $status === 'rejected' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Rejected
                    </a>
                </div>
            </div>
        </section>

        @if($businesses->count() > 0)
            {{-- Businesses Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 mb-12">
                @foreach($businesses as $b)
                    <div class="group bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-2xl hover:border-uco-orange-300 transition-all duration-500 overflow-hidden flex flex-col reveal-on-scroll" style="transition-delay: {{ ($loop->index % 6) * 50 }}ms">
                        {{-- Cover Image / Logo --}}
                        <div class="h-48 bg-gray-50 relative overflow-hidden">
                            @php 
                                $cover = $b->photos->where('is_primary', true)->first()?->photo_path ?? ($b->photos->first()?->photo_path ?? null);
                                $coverUrl = $cover ? storage_image_url($cover, 'preview') : null;
                            @endphp
                            @if($coverUrl)
                                <img src="{{ $coverUrl }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-200">
                                    <i class="bi bi-shop text-5xl text-gray-300"></i>
                                </div>
                            @endif
                            
                            {{-- Logo Overlay --}}
                            <div class="absolute bottom-4 left-4">
                                @php $logo = $b->logo_url ? storage_image_url($b->logo_url, 'logo_thumb') : null; @endphp
                                @if($logo)
                                    <img src="{{ $logo }}" class="w-16 h-16 rounded-xl bg-white p-1.5 shadow-lg border border-white">
                                @endif
                            </div>

                            {{-- Status Badge --}}
                            <div class="absolute top-4 left-4">
                                <span class="px-2.5 py-1 rounded-lg bg-soft-gray-900 text-white text-[10px] font-black uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="bi {{ $b->status === 'rejected' ? 'bi-x-circle-fill' : 'bi-hourglass-split' }}"></i>
                                    {{ $b->status_label }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6 flex-1 flex flex-col">
                            <div class="mb-4 text-left">
                                <span class="inline-block px-2.5 py-1 rounded-lg bg-soft-gray-100 text-soft-gray-600 text-[10px] font-bold uppercase tracking-wider mb-2">
                                    {{ optional($b->category)->name }}
                                </span>
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="text-xl font-bold text-gray-900 line-clamp-1">{{ $b->name }}</h3>
                                    <span class="text-xs text-gray-400 whitespace-nowrap">{{ $b->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-uco-orange-600 font-semibold mt-1">Owner: {{ $b->user->name }}</p>
                            </div>
                            
                            <p class="text-sm text-gray-500 line-clamp-2 mb-6 flex-1 italic text-left">
                                {{ $b->description ?: 'No description provided' }}
                            </p>

                            @if(in_array($b->status, ['rejected', 'need_revision']) && $b->rejection_reason)
                                <div class="mb-6 p-3 {{ $b->status === 'rejected' ? 'bg-red-50 border-red-100' : 'bg-blue-50 border-blue-100' }} border rounded-xl">
                                    <p class="text-[10px] font-bold {{ $b->status === 'rejected' ? 'text-red-600' : 'text-blue-600' }} uppercase tracking-wider mb-1">
                                        {{ $b->status === 'rejected' ? 'Rejection Reason:' : 'Revision Request:' }}
                                    </p>
                                    <p class="text-xs {{ $b->status === 'rejected' ? 'text-red-700' : 'text-blue-700' }} italic line-clamp-2">"{{ $b->rejection_reason }}"</p>
                                </div>
                            @endif

                            <div class="flex items-center gap-3 pt-5 border-t border-gray-100 mt-auto">
                                <a href="{{ route('admin.business-approvals.show', $b) }}" 
                                   class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-gray-800 transition-all shadow-lg shadow-gray-200">
                                    Review Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8">
                {{ $businesses->links() }}
            </div>
        @else
            <div class="bg-white border-2 border-dashed border-gray-200 rounded-xl p-20 text-center">
                <div class="w-24 h-24 bg-soft-gray-50 text-soft-gray-300 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                    <i class="bi bi-journal-check text-5xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">No Pending Approvals</h2>
                <p class="text-gray-500 max-w-md mx-auto leading-relaxed italic">Everything is up to date! There are no businesses waiting for approval at the moment.</p>
            </div>
        @endif
    </div>
</x-app-layout>
