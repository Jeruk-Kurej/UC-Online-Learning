<x-app-layout>
    <div class="users-wrapper max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ showImportModal: false }">
        
        {{-- Page Header --}}
        <section class="relative overflow-hidden rounded-[2.5rem] border border-uco-orange-100 bg-white px-6 py-8 shadow-sm md:px-8 md:py-10 mb-8">
            <div class="uco-hero-mesh"></div>
            <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="space-y-2">
                    <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-uco-orange-700">
                        Admin Portal
                    </span>
                    <h1 class="text-3xl font-extrabold text-soft-gray-900 md:text-4xl">User Management</h1>
                    <p class="text-sm text-soft-gray-600 mt-1">Manage student and alumni profiles synced from the central database.</p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-4 py-3 bg-uco-yellow-50 border border-uco-yellow-200 text-uco-yellow-700 text-xs font-black rounded-2xl">
                        <i class="bi bi-star-fill text-uco-yellow-500"></i>
                        {{ $featuredUserCount }}/4 Featured
                    </span>
                    <button @click="showImportModal = true" class="inline-flex items-center px-6 py-4 bg-white border border-gray-300 text-gray-700 text-sm font-bold rounded-2xl hover:bg-gray-50 transition shadow-sm">
                        <i class="bi bi-cloud-upload mr-2"></i>
                        Import CSV
                    </button>

                    @if(auth()->user() && auth()->user()->isAdmin())
                        <a href="{{ route('users.create') }}" class="inline-flex items-center px-6 py-4 bg-gray-900 text-white text-sm font-bold rounded-2xl hover:bg-black transition shadow-sm">
                            <i class="bi bi-person-plus-fill mr-2"></i>
                            Create User
                        </a>
                    @endif
                </div>
            </div>
        </section>

        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white border rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl transition-all duration-500">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Total Users</p>
                <p class="text-4xl font-black text-gray-900">{{ $totalUsers }}</p>
            </div>
            <div class="bg-white border rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl transition-all duration-500">
                <p class="text-[10px] font-black text-blue-400 uppercase tracking-[0.2em] mb-1">Entrepreneurs</p>
                <p class="text-4xl font-black text-blue-600">{{ $totalEntrepreneurs }}</p>
            </div>
            <div class="bg-white border rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl transition-all duration-500">
                <p class="text-[10px] font-black text-green-400 uppercase tracking-[0.2em] mb-1">Intrapreneurs</p>
                <p class="text-4xl font-black text-green-600">{{ $totalIntrapreneurs }}</p>
            </div>
            <div class="bg-white border rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl transition-all duration-500">
                <p class="text-[10px] font-black text-purple-400 uppercase tracking-[0.2em] mb-1">Alumni</p>
                <p class="text-4xl font-black text-purple-600">{{ $totalAlumni }}</p>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white border rounded-[2.5rem] p-6 mb-8 shadow-sm">
            <form action="{{ route('users.index') }}" method="GET" class="flex gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, or NIS..." class="flex-1 border-gray-200 bg-gray-50 rounded-2xl px-6 py-4 focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all">
                <button type="submit" class="bg-gray-900 text-white px-8 py-4 rounded-2xl font-bold hover:bg-black transition">
                    Search
                </button>
            </form>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-bold px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->has('featured'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm font-bold px-4 py-3 rounded-xl">
                {{ $errors->first('featured') }}
            </div>
        @endif

        {{-- Users Table --}}
        <div class="bg-white border rounded-[2.5rem] overflow-hidden shadow-sm">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Name</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Email</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Peminatan</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Visible</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Featured</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Businesses</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase {{ $user->student_status === 'alumni' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $user->student_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $user->major }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="w-3 h-3 rounded-full inline-block {{ $user->is_visible ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('users.toggle-featured', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        title="{{ $user->is_featured ? 'Remove from featured' : 'Add to featured' }}"
                                        class="w-7 h-7 rounded-full inline-flex items-center justify-center transition-all border
                                            {{ $user->is_featured
                                                ? 'bg-uco-yellow-400 border-uco-yellow-500 text-white hover:bg-uco-yellow-500'
                                                : 'bg-white border-gray-200 text-gray-300 hover:text-uco-yellow-400 hover:border-uco-yellow-300' }}">
                                        <i class="bi bi-star-fill text-[10px]"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-gray-900">{{ $user->businesses_count }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('users.show', $user) }}" class="p-2 text-gray-400 hover:text-uco-orange-500 transition">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-20 text-center text-gray-400 italic">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $users->links() }}
        </div>

        {{-- Import Modal --}}
        <div x-show="showImportModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-8" @click.away="showImportModal = false"
                 x-data="{
                    isDragging: false,
                    handleDragOver(e) { e.preventDefault(); this.isDragging = true; },
                    handleDragLeave() { this.isDragging = false; },
                    handleDrop(e) {
                        e.preventDefault();
                        this.isDragging = false;
                        if (e.dataTransfer.files.length > 0) {
                            const file = e.dataTransfer.files[0];
                            document.getElementById('csv_file').files = e.dataTransfer.files;
                            document.getElementById('file_name').textContent = file.name;
                        }
                    }
                 }">
                <h3 class="text-2xl font-black text-gray-900 mb-2">Import Data</h3>
                <p class="text-sm text-gray-500 mb-6">Upload the UC Online Form Responses CSV file to sync profiles.</p>
                
                <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="border-2 border-dashed rounded-2xl p-10 text-center transition group"
                         :class="isDragging ? 'border-uco-orange-500 bg-orange-50' : 'border-gray-200 hover:border-uco-orange-300'"
                         @dragover="handleDragOver"
                         @dragleave="handleDragLeave"
                         @drop="handleDrop">
                        <input type="file" name="file" required class="hidden" id="csv_file" onchange="document.getElementById('file_name').textContent = this.files[0].name">
                        <label for="csv_file" class="cursor-pointer">
                            <i class="bi bi-file-earmark-spreadsheet text-4xl text-gray-300 group-hover:text-uco-orange-500 transition"></i>
                            <p class="mt-4 text-sm font-bold text-gray-600" id="file_name">Click to select or drag CSV/Excel file here</p>
                        </label>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" @click="showImportModal = false" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Cancel</button>
                        <button type="submit" class="flex-1 px-6 py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-black transition">Start Import</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Import Progress Tracker (Always rendered, fetches active import on load) --}}
        <div x-data="importProgress()" x-init="checkActiveImport().then(() => startPolling())" class="fixed bottom-6 right-6 z-50 w-96">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden" x-show="visible" x-transition>
                {{-- Header --}}
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

                {{-- Progress Bar --}}
                <div class="px-5 pb-4 pt-2">
                    <div class="w-full bg-gray-100 rounded-full h-2.5 mb-3 overflow-hidden">
                        <div class="h-2.5 rounded-full transition-all duration-500 ease-out"
                             :class="status === 'completed' ? 'bg-emerald-500' : 'bg-gray-900'"
                             :style="'width: ' + percent + '%'"></div>
                    </div>

                    {{-- Stats --}}
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
                    
                    this.poll(); // immediate first call
                    this.polling = setInterval(() => this.poll(), 2000);
                },

                async poll() {
                    if (!this.importId) return;
                    
                    try {
                        const res = await fetch(`/import-progress/${this.importId}`);
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
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({ type: 'user' })
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
                    // Clear server-side session
                    fetch('/clear-active-import', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ type: 'user' })
                    });
                }
            }
        }
        </script>
    </div>

</x-app-layout>
