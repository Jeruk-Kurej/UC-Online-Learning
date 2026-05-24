<div x-data="sharedImportManager()" 
     x-init="init()" 
     @start-import.window="uploadImportFile($event.detail)"
     class="relative">
     
    {{-- CSS for custom scrollbar --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(247, 147, 30, 0.2);
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(247, 147, 30, 0.4);
        }
    </style>

    {{-- SVG Gradients --}}
    <svg class="w-0 h-0 absolute pointer-events-none">
        <defs>
            <linearGradient id="orangeGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#f7931e" />
                <stop offset="100%" stop-color="#ff5e3a" />
            </linearGradient>
        </defs>
    </svg>

    {{-- Floating Progress Card (Bottom-Right) --}}
    <div x-show="visible" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         @click="maximize()"
         class="fixed bottom-6 right-6 z-50 w-80 bg-white border border-gray-200 shadow-xl rounded-lg p-4 cursor-pointer hover:shadow-2xl transition-shadow duration-200"
         x-cloak>
         
        <div class="flex items-center justify-between gap-3">
            {{-- Circular Progress Ring --}}
            <div class="relative flex-shrink-0 w-12 h-12">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="24" cy="24" r="20" stroke="rgba(229, 231, 235, 0.5)" stroke-width="3.5" fill="transparent" />
                    <circle cx="24" cy="24" r="20" stroke="url(#orangeGradient)" stroke-width="3.5" fill="transparent" 
                            stroke-dasharray="125.6" 
                            :stroke-dashoffset="125.6 - (125.6 * (status === 'uploading' ? uploadPercent : percent) / 100)"
                            class="transition-all duration-300 ease-out" 
                            stroke-linecap="round" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center text-[10px] font-black text-gray-800" 
                     x-text="(status === 'uploading' ? uploadPercent : percent) + '%'"></div>
            </div>

            {{-- Text Info --}}
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider" 
                   x-text="status === 'uploading' ? 'Uploading File' : (status === 'completed' ? 'Import Complete' : 'Importing Data')"></p>
                <p class="text-sm font-black text-gray-800 truncate pr-2" 
                   x-text="status === 'uploading' ? fileName : (format || 'Processing...')"></p>
            </div>

            {{-- Controls / Close Button --}}
            <div class="flex items-center gap-1">
                <button x-show="status === 'completed' || status === 'failed'"
                        @click.stop="dismiss()" 
                        class="p-1.5 rounded hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition duration-200"
                        x-cloak>
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>
        </div>
        
        {{-- Footer/Mini Banner --}}
        <div class="mt-2.5 pt-2 border-t border-gray-100 flex items-center justify-between text-[10px] text-gray-400 font-semibold">
            <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full" 
                      :class="{
                          'bg-blue-500 animate-pulse': status === 'uploading',
                          'bg-amber-500 animate-pulse': status === 'processing',
                          'bg-emerald-500': status === 'completed',
                          'bg-rose-500': status === 'failed'
                      }"></span>
                <span x-text="status === 'uploading' ? 'Uploading...' : (status === 'processing' ? 'Queue active' : (status === 'completed' ? 'Success' : 'Failed'))"></span>
            </div>
            <span class="text-uco-orange-500 hover:underline">Click to view details</span>
        </div>
    </div>

    {{-- Detailed Popup Modal --}}
    <div x-show="showDetailModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
         x-cloak>
         
        <div @click.away="status === 'completed' || status === 'failed' ? dismiss() : minimize()" 
             x-show="showDetailModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-2xl w-full p-8 flex flex-col max-h-[90vh]">
            
            {{-- Modal Header --}}
            <div class="flex justify-between items-start mb-6">
                <div>
                    <div class="flex items-center gap-2.5 mb-1.5">
                        <span class="inline-flex items-center rounded-md border border-uco-orange-200 bg-uco-orange-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-uco-orange-700" 
                              x-text="format || 'Importer'"></span>
                        <template x-if="status === 'processing'">
                            <span class="inline-flex items-center gap-1 rounded-md border border-blue-200 bg-blue-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-blue-700 animate-pulse">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Background Importing
                            </span>
                        </template>
                        <template x-if="status === 'completed'">
                            <span class="inline-flex items-center rounded-md border border-emerald-200 bg-emerald-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-700">
                                Completed
                            </span>
                        </template>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">Import Progress Details</h3>
                    <p class="text-xs text-gray-400 mt-1 font-semibold truncate max-w-md" x-text="'File: ' + (fileName || 'Import File')"></p>
                </div>
                <button @click="minimize()" 
                        class="p-2 rounded-md hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition duration-200">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="flex-1 overflow-y-auto pr-1 custom-scrollbar space-y-6">
                
                {{-- Progress Bar Indicator --}}
                <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                    <div class="flex justify-between text-sm font-bold text-gray-700 mb-2">
                        <span x-text="status === 'uploading' ? 'Uploading file to server...' : (status === 'completed' ? 'Processing completed!' : 'Importing records to database...')"></span>
                        <span class="text-uco-orange-600" x-text="(status === 'uploading' ? uploadPercent : percent) + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-100 border border-gray-300 rounded h-3 overflow-hidden">
                        <div class="h-full bg-uco-orange-500 rounded transition-all duration-300 ease-out"
                             :style="'width: ' + (status === 'uploading' ? uploadPercent : percent) + '%'"></div>
                    </div>
                </div>

                {{-- Statistics Cards --}}
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-slate-50 border border-gray-200 rounded-lg p-4 text-center">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Processed</p>
                        <p class="text-2xl font-black text-slate-800" x-text="status === 'uploading' ? '-' : current + ' / ' + total"></p>
                    </div>
                    <div class="bg-emerald-50/50 border border-emerald-100/50 rounded-lg p-4 text-center">
                        <p class="text-xs font-bold text-emerald-500 uppercase tracking-wider mb-1">Success</p>
                        <p class="text-2xl font-black text-emerald-600" x-text="status === 'uploading' ? '-' : success"></p>
                    </div>
                    <div class="bg-rose-50/50 border border-rose-100/50 rounded-lg p-4 text-center">
                        <p class="text-xs font-bold text-rose-500 uppercase tracking-wider mb-1">Skipped</p>
                        <p class="text-2xl font-black text-rose-600" x-text="status === 'uploading' ? '-' : skipped"></p>
                    </div>
                </div>

                {{-- Activity Log / Warnings --}}
                <div>
                    <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="bi bi-journal-text text-gray-400 text-lg"></i>
                        Import Log & Warning Messages
                    </h4>

                    {{-- Placeholder when empty --}}
                    <div x-show="errors.length === 0 && status !== 'uploading'" 
                         class="border border-dashed border-gray-300 rounded-lg p-8 text-center bg-gray-50">
                        <div class="w-12 h-12 rounded bg-emerald-50 border border-emerald-200 flex items-center justify-center mx-auto mb-3">
                            <i class="bi bi-check-lg text-emerald-500 text-xl font-bold"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-700">No warnings encountered</p>
                        <p class="text-xs text-gray-400 mt-1">If files contain skipped rows or data validation mismatches, they will appear here in real-time.</p>
                    </div>

                    {{-- List of warnings --}}
                    <div x-show="errors.length > 0" 
                         class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="max-h-56 overflow-y-auto custom-scrollbar p-3 bg-slate-50 space-y-2">
                            <template x-for="(error, index) in errors" :key="index">
                                <div class="flex items-start gap-2.5 bg-white border border-gray-200 rounded-lg p-3">
                                    <div class="mt-0.5 w-5 h-5 flex-shrink-0 rounded bg-rose-50 border border-rose-200 flex items-center justify-center">
                                        <i class="bi bi-exclamation-triangle text-rose-500 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-center mb-0.5">
                                            <span class="text-[10px] font-bold text-rose-500 uppercase tracking-wider" x-text="'Row Skip #' + (index + 1)"></span>
                                        </div>
                                        <p class="text-xs text-gray-700 font-semibold leading-relaxed break-words" x-text="error"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                <p class="text-[10px] text-gray-400 font-semibold max-w-sm">
                    You can safely minimize this popup or continue using the website. The background import process will continue running.
                </p>
                <div class="flex gap-3">
                    <template x-if="status === 'uploading' || status === 'processing'">
                        <button @click="minimize()" 
                                class="btn-uco btn-uco-secondary px-6 py-2.5 text-xs font-bold transition duration-200">
                            Minimize
                        </button>
                    </template>
                    <template x-if="status === 'completed' || status === 'failed'">
                        <button @click="dismiss()" 
                                class="btn-uco btn-uco-primary px-6 py-2.5 text-xs font-bold transition duration-200">
                            <span x-text="status === 'completed' ? 'Done & Refresh' : 'Close'"></span>
                        </button>
                    </template>
                </div>
            </div>
            
        </div>
    </div>

</div>

<script>
if (typeof sharedImportManager !== 'function') {
    function sharedImportManager() {
        return {
            importId: '',
            status: 'idle', // 'idle' | 'uploading' | 'processing' | 'completed' | 'failed'
            uploadPercent: 0,
            total: 0,
            current: 0,
            success: 0,
            skipped: 0,
            percent: 0,
            errors: [],
            format: '',
            visible: false,
            showDetailModal: false,
            polling: null,
            fileName: '',

            init() {
                // Restore from localStorage first (survives page refresh)
                const saved = localStorage.getItem('uco_active_import');
                if (saved) {
                    try {
                        const parsed = JSON.parse(saved);
                        if (parsed.importId) {
                            this.importId = parsed.importId;
                            this.fileName = parsed.fileName || '';
                            this.format = parsed.format || 'Processing...';
                            this.status = 'processing';
                            this.visible = true;
                            this.startPolling();
                            return; // Skip server check since we already have importId
                        }
                    } catch(e) {}
                }

                // Fallback: check server session
                this.checkActiveImport().then(() => {
                    if (this.importId) {
                        this.status = 'processing';
                        this.visible = true;
                        this.startPolling();
                    }
                });
            },

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
                    }
                } catch (e) {
                    console.error('Check active import error:', e);
                }
            },

            uploadImportFile(formOrEvent) {
                const form = formOrEvent.target ? formOrEvent.target : formOrEvent;
                const fileInput = form.querySelector('input[type="file"]');
                if (!fileInput || !fileInput.files.length) {
                    alert('Please select a file to import.');
                    return;
                }

                const file = fileInput.files[0];
                this.fileName = file.name;

                // Close parent view modals by sending window event
                window.dispatchEvent(new CustomEvent('close-import-modal'));

                this.visible = true;
                this.status = 'uploading';
                this.uploadPercent = 0;
                this.errors = [];
                this.total = 0;
                this.current = 0;
                this.success = 0;
                this.skipped = 0;
                this.percent = 0;

                const formData = new FormData(form);
                const xhr = new XMLHttpRequest();

                xhr.open('POST', form.action, true);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);

                // Track upload percentage
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        this.uploadPercent = Math.round((e.loaded / e.total) * 100);
                    }
                });

                xhr.onload = () => {
                    if (xhr.status === 200) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            if (data.success && data.importId) {
                                this.importId = data.importId;
                                this.format = data.format || 'Excel Importer';
                                this.status = 'processing';
                                // Persist to localStorage so refresh doesn't lose widget
                                localStorage.setItem('uco_active_import', JSON.stringify({
                                    importId: this.importId,
                                    fileName: this.fileName,
                                    format: this.format,
                                }));
                                this.startPolling();
                            } else {
                                this.status = 'failed';
                                this.errors.push(data.message || 'Server error occurred during queue registration.');
                            }
                        } catch (e) {
                            this.status = 'failed';
                            this.errors.push('Failed to parse upload response.');
                        }
                    } else {
                        let errMsg = 'File upload failed.';
                        try {
                            const data = JSON.parse(xhr.responseText);
                            errMsg = data.message || errMsg;
                        } catch (e) {}
                        this.status = 'failed';
                        this.errors.push(errMsg);
                    }
                };

                xhr.onerror = () => {
                    this.status = 'failed';
                    this.errors.push('Network error during file upload.');
                };

                xhr.send(formData);
            },

            startPolling() {
                if (!this.importId) return;
                if (this.polling) clearInterval(this.polling);
                this.pollCount = 0;
                this.poll(); // Poll immediately
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
                    this.errors = data.errors || [];
                    this.percent = this.total > 0 ? Math.min(100, Math.round((this.current / this.total) * 100)) : 0;

                    // ── Live table refresh: every 3rd poll (~6s) refresh any list on the page ──
                    this.pollCount = (this.pollCount || 0) + 1;
                    if (this.pollCount % 3 === 0 || this.current !== this._lastCurrent) {
                        this._lastCurrent = this.current;
                        this._refreshPageList();
                    }

                    if (this.status === 'completed' || this.status === 'failed') {
                        clearInterval(this.polling);
                        this.polling = null;
                        // Clear localStorage so widget doesn't reappear after dismiss
                        localStorage.removeItem('uco_active_import');
                        // Refresh the table one final time on completion
                        this._refreshPageList();
                        // Clear active session backend key
                        fetch('/clear-active-import', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                            }
                        });
                    }
                } catch (e) {
                    console.error('Progress poll error:', e);
                }
            },

            // Trigger AJAX list refresh on any page that has a recognised list container
            _refreshPageList() {
                // Users management page — refresh table rows
                if (document.getElementById('users-list-container')) {
                    window.dispatchEvent(new CustomEvent('ajax-update-list'));
                    // Also refresh the stat counters (Total Users / Entrepreneurs / etc.)
                    window.dispatchEvent(new CustomEvent('ajax-update-stats'));
                }
                // Businesses admin page
                if (document.getElementById('businesses-list-container')) {
                    window.dispatchEvent(new CustomEvent('ajax-update-list'));
                }
            },

            minimize() {
                this.showDetailModal = false;
            },

            maximize() {
                if (this.status !== 'idle') {
                    this.showDetailModal = true;
                }
            },

            dismiss() {
                const wasCompleted = this.status === 'completed' || this.percent === 100;
                
                this.visible = false;
                this.showDetailModal = false;
                this.importId = '';
                this.status = 'idle';
                
                if (this.polling) {
                    clearInterval(this.polling);
                    this.polling = null;
                }

                // Always clear localStorage on dismiss
                localStorage.removeItem('uco_active_import');
                
                fetch('/clear-active-import', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    }
                });

                if (wasCompleted) {
                    window.location.reload();
                }
            }
        }
    }
}
</script>
