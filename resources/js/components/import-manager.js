// --- Import progress: upload, polling, floating card and detail modal ---
// Used by partials/import-progress on the admin user and business lists.

export default function sharedImportManager() {
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
