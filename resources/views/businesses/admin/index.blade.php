<x-app-layout>
    <div class="w-full max-w-[1600px] mx-auto py-8 px-4" x-data="{ showImportModal: false }">
        <section class="relative overflow-hidden rounded-xl border border-gray-200 bg-white px-6 py-6 shadow-sm md:px-8 mb-8 reveal-on-scroll">
            <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="space-y-1">
                    <span class="inline-flex items-center rounded-md border border-uco-orange-200 bg-uco-orange-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-uco-orange-700">
                        Admin Portal
                    </span>
                    <h1 class="text-3xl font-extrabold text-soft-gray-900 md:text-4xl">Business Management</h1>
                    <p class="text-sm text-soft-gray-600 mt-1">Review and manage business submissions from students.</p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-uco-yellow-50 border border-uco-yellow-200 text-uco-yellow-700 text-xs font-semibold rounded-md">
                        <i class="bi bi-star-fill text-uco-yellow-500"></i>
                        <span class="featured-count">{{ $featuredBusinessesCount }}</span>/8 Featured
                    </span>
                    <button @click="showImportModal = true" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 transition shadow-sm">
                        <i class="bi bi-cloud-upload mr-2"></i>
                        Import CSV
                    </button>

                    <a href="{{ route('businesses.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-black transition shadow-sm">
                        <i class="bi bi-plus-lg mr-2"></i>
                        Create Business
                    </a>
                </div>
            </div>
        </section>

        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 100ms;">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Total Businesses</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalBusinesses }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 150ms;">
                <p class="text-[10px] font-bold text-green-500 uppercase tracking-wider mb-1">Approved</p>
                <p class="text-3xl font-bold text-green-600">{{ $approvedBusinesses }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 200ms;">
                <p class="text-[10px] font-bold text-amber-500 uppercase tracking-wider mb-1">Pending Approval</p>
                <p class="text-3xl font-bold text-amber-600">{{ $pendingBusinesses }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 250ms;">
                <p class="text-[10px] font-bold text-red-500 uppercase tracking-wider mb-1">Rejected / Revision</p>
                <p class="text-3xl font-bold text-red-600">{{ $rejectedBusinesses }}</p>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="mb-8 reveal-on-scroll" style="transition-delay: 300ms;">
            <form action="{{ route('businesses.admin') }}" method="GET">
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    {{-- Search Input --}}
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search business name..."
                            class="w-full border-gray-300 bg-white rounded-md pl-10 pr-4 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm"
                        >
                    </div>

                    {{-- Filters & Reset Button --}}
                    <div class="flex items-center gap-3">
                        <select name="status" onchange="this.form.submit()" 
                                class="min-w-[150px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm">
                            <option value="">Status: All</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="need_revision" {{ $status === 'need_revision' ? 'selected' : '' }}>Need Revision</option>
                        </select>

                        <select name="featured" onchange="this.form.submit()" 
                                class="min-w-[150px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm">
                            <option value="">Featured: All</option>
                            <option value="yes" {{ $featured === 'yes' ? 'selected' : '' }}>Featured</option>
                            <option value="no" {{ $featured === 'no' ? 'selected' : '' }}>Not Featured</option>
                        </select>
                        
                        <a href="{{ route('businesses.admin') }}" title="Reset Filters" class="inline-flex items-center justify-center bg-white border border-gray-300 text-gray-500 hover:text-gray-900 hover:bg-gray-50 h-[38px] w-[38px] rounded-md transition shadow-sm">
                            <i class="bi bi-arrow-clockwise text-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4">Business</th>
                            <th scope="col" class="px-6 py-4">Owner</th>
                            <th scope="col" class="px-6 py-4">Category</th>
                            <th scope="col" class="px-6 py-4">Location</th>
                            <th scope="col" class="px-6 py-4">Status</th>
                            <th scope="col" class="px-6 py-4 text-center">Visible</th>
                            <th scope="col" class="px-6 py-4 text-center">Featured</th>
                            <th scope="col" class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($businesses as $b)
                            <tr class="bg-white border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden border border-gray-200">
                                            @if($b->logo_url)
                                                <img src="{{ $b->logo_url }}" class="w-full h-full object-cover">
                                            @else
                                                <i class="bi bi-shop text-gray-400 text-xl"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $b->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @php
                                            $ownerName = optional($b->user)->name ?? 'Unknown';
                                            $ownerPhoto = optional($b->user)->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($ownerName) . '&color=4B5563&background=F3F4F6';
                                        @endphp
                                        <div class="w-7 h-7 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden">
                                            <img src="{{ $ownerPhoto }}" class="w-full h-full object-cover">
                                        </div>
                                        <span class="font-bold text-gray-700 text-xs">{{ $ownerName }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 italic">
                                    {{ optional($b->category)->name ?? 'Uncategorized' }}
                                </td>
                                <td class="px-6 py-4 text-xs font-medium">
                                    {{ $b->city }}, {{ $b->province }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = match($b->status) {
                                            'approved' => 'bg-green-100 text-green-700 border-green-200',
                                            'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                            'rejected' => 'bg-red-100 text-red-700 border-red-200',
                                            'need_revision' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            default => 'bg-gray-100 text-gray-700 border-gray-200',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase border {{ $statusClasses }}">
                                        {{ $b->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="w-3 h-3 rounded-full inline-block {{ $b->is_visible ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('businesses.toggle-featured', $b) }}" method="POST" class="inline-block toggle-featured-form">
                                         @csrf
                                         <button type="submit"
                                                 {{ !$b->is_visible ? 'disabled' : '' }}
                                                 class="relative group w-7 h-7 rounded-full inline-flex items-center justify-center transition-all border
                                                     {{ !$b->is_visible 
                                                         ? 'bg-gray-50 border-gray-100 text-gray-200 cursor-not-allowed'
                                                         : ($b->is_featured
                                                             ? 'bg-uco-yellow-400 border-uco-yellow-500 text-white hover:bg-uco-yellow-500'
                                                             : 'bg-white border-gray-200 text-gray-300 hover:text-uco-yellow-400 hover:border-uco-yellow-300') }}">
                                             <i class="bi bi-star-fill text-[10px]"></i>
                                             <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-200 z-30 flex flex-col items-center">
                                                 <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap uppercase tracking-wider tooltip-text">
                                                     {{ !$b->is_visible ? 'Requires approved business' : ($b->is_featured ? 'Remove featured' : 'Make featured') }}
                                                 </div>
                                                 <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5"></div>
                                             </div>
                                         </button>
                                     </form>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                         <a href="{{ route('businesses.show', $b) }}" 
                                            class="relative group w-9 h-9 rounded-xl flex items-center justify-center bg-green-100 text-green-600 hover:bg-green-500 hover:text-white transition-all duration-200 shadow-sm">
                                             <i class="bi bi-eye-fill text-sm"></i>
                                             <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-200 z-30 flex flex-col items-center">
                                                 <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap uppercase tracking-wider">
                                                     View Details
                                                 </div>
                                                 <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5"></div>
                                             </div>
                                         </a>
                                         
                                         <button type="button" 
                                                 onclick="openStatusModal({{ json_encode(['id' => $b->id, 'name' => $b->name, 'status' => $b->status, 'reason' => $b->rejection_reason]) }})"
                                                 class="relative group w-9 h-9 rounded-xl flex items-center justify-center bg-gray-100 text-gray-600 hover:bg-gray-900 hover:text-white transition-all duration-200 shadow-sm">
                                             <i class="bi bi-pencil-square text-sm"></i>
                                             <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-200 z-30 flex flex-col items-center">
                                                 <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap uppercase tracking-wider">
                                                     Change Status
                                                 </div>
                                                 <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5"></div>
                                             </div>
                                         </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-20 text-center text-gray-400 italic">
                                    No businesses found matching the criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($businesses->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $businesses->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Status Modal --}}
    <div id="statusModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeStatusModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="statusForm" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="bi bi-gear-fill text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                    Update Business Status
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4" id="businessNameDisplay"></p>
                                    
                                    <div class="mb-4">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-2">New Status</label>
                                        <select name="status" id="statusSelect" onchange="toggleReasonField()"
                                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="pending">Pending Approval</option>
                                            <option value="approved">Approved</option>
                                            <option value="need_revision">Need Revision</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </div>

                                    <div id="reasonField" class="hidden">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-2">Feedback / Reason</label>
                                        <textarea name="rejection_reason" id="rejection_reason" rows="3" 
                                                  class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium outline-none focus:ring-2 focus:ring-blue-500"
                                                  placeholder="Provide specific feedback or reason for rejection..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse sm:items-center sm:gap-2">
                        <button type="submit" class="w-full sm:w-auto btn-uco btn-uco-primary">
                            Save Changes
                        </button>
                        <button type="button" onclick="closeStatusModal()" class="mt-3 sm:mt-0 w-full sm:w-auto btn-uco btn-uco-neutral">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openStatusModal(data) {
            const modal = document.getElementById('statusModal');
            const form = document.getElementById('statusForm');
            const nameDisplay = document.getElementById('businessNameDisplay');
            const statusSelect = document.getElementById('statusSelect');
            const reasonArea = document.getElementById('rejection_reason');
            
            form.action = `/businesses/${data.id}/status`;
            nameDisplay.innerText = `Updating: ${data.name}`;
            statusSelect.value = data.status;
            reasonArea.value = data.reason || '';
            
            modal.classList.remove('hidden');
            toggleReasonField();
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }

        function toggleReasonField() {
            const status = document.getElementById('statusSelect').value;
            const reasonField = document.getElementById('reasonField');
            if (status === 'rejected' || status === 'need_revision') {
                reasonField.classList.remove('hidden');
            } else {
                reasonField.classList.add('hidden');
            }
        }

        // Intercept featured toggle form submissions to handle them via AJAX
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form && form.classList.contains('toggle-featured-form')) {
                e.preventDefault();
                const button = form.querySelector('button');
                const tokenInput = form.querySelector('input[name="_token"]');
                if (!button || !tokenInput) return;

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': tokenInput.value
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const isFeaturedNow = data.message.includes('now featured');
                        const tooltipEl = button.querySelector('.tooltip-text');
                        if (isFeaturedNow) {
                            button.className = "relative group w-7 h-7 rounded-full inline-flex items-center justify-center transition-all border bg-uco-yellow-400 border-uco-yellow-500 text-white hover:bg-uco-yellow-500";
                            if (tooltipEl) tooltipEl.textContent = 'Remove featured';
                        } else {
                            button.className = "relative group w-7 h-7 rounded-full inline-flex items-center justify-center transition-all border bg-white border-gray-200 text-gray-300 hover:text-uco-yellow-400 hover:border-uco-yellow-300";
                            if (tooltipEl) tooltipEl.textContent = 'Make featured';
                        }
                        
                        // Update featured badge count in header
                        const countEl = document.querySelector('.featured-count');
                        if (countEl) {
                            let currentCount = parseInt(countEl.textContent, 10) || 0;
                            countEl.textContent = isFeaturedNow ? currentCount + 1 : Math.max(0, currentCount - 1);
                        }
                        
                        // Dispatch global toast message
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { message: data.message, type: 'success' }
                        }));
                    } else {
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { message: data.message || 'Error occurred', type: 'error' }
                        }));
                    }
                })
                .catch(err => {
                    console.error('Featured toggle failed:', err);
                });
            }
        });
    </script>
    </div>

    {{-- Import Modal --}}
    @if (auth()->user()?->isAdmin())
        <div x-show="showImportModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-lg shadow-2xl max-w-lg w-full p-8" @click.away="showImportModal = false"
                 x-data="{
                    isDragging: false,
                    handleDragOver(e) { e.preventDefault(); this.isDragging = true; },
                    handleDragLeave() { this.isDragging = false; },
                    handleDrop(e) {
                        e.preventDefault();
                        this.isDragging = false;
                        if (e.dataTransfer.files.length > 0) {
                            const file = e.dataTransfer.files[0];
                            document.getElementById('biz_csv_file').files = e.dataTransfer.files;
                            document.getElementById('biz_file_name').textContent = file.name;
                        }
                    }
                 }">
                <h3 class="text-2xl font-black text-gray-900 mb-2">Import Businesses</h3>
                <p class="text-sm text-gray-500 mb-6">Upload the UC Online Form Responses CSV file to sync profiles.</p>
                
                <form action="{{ route('businesses.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="border-2 border-dashed rounded-xl p-10 text-center transition group"
                         :class="isDragging ? 'border-gray-900 bg-orange-50' : 'border-gray-200 hover:border-gray-300'"
                         @dragover="handleDragOver"
                         @dragleave="handleDragLeave"
                         @drop="handleDrop">
                        <input type="file" name="file" required class="hidden" id="biz_csv_file" onchange="document.getElementById('biz_file_name').textContent = this.files[0].name">
                        <label for="biz_csv_file" class="cursor-pointer">
                            <i class="bi bi-file-earmark-spreadsheet text-4xl text-gray-300 group-hover:text-gray-900 transition"></i>
                            <p class="mt-4 text-sm font-bold text-gray-600" id="biz_file_name">Click to select or drag CSV/Excel file here</p>
                        </label>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" @click="showImportModal = false" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Cancel</button>
                        <button type="submit" class="flex-1 px-6 py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-black transition">Start Import</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Import Progress Tracker --}}
    <div x-data="importProgress()" x-init="checkActiveImport().then(() => startPolling())" class="fixed bottom-6 right-6 z-50 w-96">
        <div class="bg-white rounded-lg shadow-2xl border border-gray-200 overflow-hidden" x-show="visible" x-transition>
            <div class="px-5 py-4 flex items-center justify-between" :class="status === 'completed' ? 'bg-emerald-50' : 'bg-gray-50'">
                <div class="flex items-center gap-3">
                    <template x-if="status !== 'completed'">
                        <div class="w-5 h-5 border-2 border-gray-400 border-t-gray-900 rounded-full animate-spin"></div>
                    </template>
                    <template x-if="status === 'completed'">
                        <i class="bi bi-check-circle-fill text-emerald-500 text-xl"></i>
                    </template>
                    <span class="font-bold text-sm text-gray-900" x-text="status === 'completed' ? 'Import Complete!' : 'Importing...'"></span>
                </div>
                <button @click="dismiss()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="px-5 pb-4 pt-2">
                <div class="w-full bg-gray-100 rounded-full h-2.5 mb-3 overflow-hidden">
                    <div class="h-2.5 rounded-full transition-all duration-500 ease-out"
                         :class="status === 'completed' ? 'bg-emerald-500' : 'bg-gray-900'"
                         :style="'width: ' + percent + '%'"></div>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="bg-gray-50 rounded-xl p-2">
                        <p class="text-xs text-gray-500">Processed</p>
                        <p class="text-sm font-black text-gray-900" x-text="current + '/' + total"></p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-2">
                        <p class="text-xs text-emerald-600">Success</p>
                        <p class="text-sm font-black text-emerald-700" x-text="success"></p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-2">
                        <p class="text-xs text-amber-600">Skipped</p>
                        <p class="text-sm font-black text-amber-700" x-text="skipped"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function importProgress() {
        return {
            importId: '',
            status: 'processing',
            total: 0,
            current: 0,
            success: 0,
            skipped: 0,
            percent: 0,
            visible: false,
            polling: null,

            async checkActiveImport() {
                try {
                    const res = await fetch('/import-progress/check', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    });
                    const data = await res.json();
                    
                    if (data.importId) {
                        this.importId = data.importId;
                        this.visible = true;
                    }
                } catch (e) {
                    console.error('Check active import error:', e);
                }
            },

            startPolling() {
                if (!this.importId) return;
                this.poll();
                this.polling = setInterval(() => this.poll(), 2000);
            },

            async poll() {
                if (!this.importId) return;
                try {
                    const res = await fetch(`/import-progress/${this.importId}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();

                    this.status = data.status || 'processing';
                    this.total = data.total || 0;
                    this.current = data.current || 0;
                    this.success = data.success || 0;
                    this.skipped = data.skipped || 0;
                    this.percent = this.total > 0 ? Math.min(100, Math.round((this.current / this.total) * 100)) : 0;

                    if (this.status === 'completed' || this.status === 'failed') {
                        clearInterval(this.polling);
                        fetch('/clear-active-import', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ type: 'business' })
                        }).then(() => {
                            setTimeout(() => window.location.reload(), 3000);
                        });
                    }
                } catch (e) {
                    console.error('Progress poll error:', e);
                }
            },

            dismiss() {
                this.visible = false;
                this.importId = '';
                clearInterval(this.polling);
                fetch('/clear-active-import', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ type: 'business' })
                });
            }
        }
    }
    </script>
</x-app-layout>
