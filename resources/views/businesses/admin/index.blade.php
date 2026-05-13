<x-app-layout>
    <div class="w-full max-w-[1600px] mx-auto py-8 px-4">
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Business Management</h1>
                <p class="text-gray-500 mt-1">Review and manage business submissions from students.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <form action="{{ route('businesses.admin') }}" method="GET" class="flex items-center gap-3">
                    <select name="status" onchange="this.form.submit()" 
                            class="bg-white border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="need_revision" {{ $status === 'need_revision' ? 'selected' : '' }}>Need Revision</option>
                    </select>
                    
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ $search }}"
                               class="bg-white border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" 
                               placeholder="Search business name...">
                    </div>
                </form>
            </div>
        </div>

        @if(session('success'))
            {{-- Handled by global toast --}}
        @endif

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4">Business & Owner</th>
                            <th scope="col" class="px-6 py-4">Category</th>
                            <th scope="col" class="px-6 py-4">Location</th>
                            <th scope="col" class="px-6 py-4">Status</th>
                            <th scope="col" class="px-6 py-4">Created At</th>
                            <th scope="col" class="px-6 py-4 text-right">Actions</th>
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
                                            <div class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Owner: {{ optional($b->user)->name ?? 'Unknown' }}</div>
                                        </div>
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
                                <td class="px-6 py-4 text-xs">
                                    {{ $b->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('businesses.show', $b) }}" target="_blank" 
                                           class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="View Details">
                                            <i class="bi bi-eye-fill text-lg"></i>
                                        </a>
                                        
                                        <button type="button" 
                                                onclick="openStatusModal({{ json_encode(['id' => $b->id, 'name' => $b->name, 'status' => $b->status, 'reason' => $b->rejection_reason]) }})"
                                                class="p-2 text-gray-400 hover:text-green-600 transition-colors" title="Change Status">
                                            <i class="bi bi-pencil-square text-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center text-gray-400 italic">
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
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse sm:items-center sm:gap-3">
                        <button type="submit" class="w-full inline-flex items-center justify-center rounded-xl border border-transparent shadow-sm px-6 py-2.5 bg-[#198754] text-base font-bold text-white hover:bg-[#157347] focus:outline-none sm:w-auto sm:text-sm transition-all">
                            Save Changes
                        </button>
                        <button type="button" onclick="closeStatusModal()" class="mt-3 w-full inline-flex items-center justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-2.5 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-all">
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
    </script>
</x-app-layout>
