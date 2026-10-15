// --- Import progress: upload, polling, floating card and detail modal ---
// Used by partials/import-progress on the admin user and business lists; the partial passes the endpoint URLs as
// data-check-url, data-progress-url (with __ID__ for the import id) and data-clear-url.

export default function sharedImportManager() {
    return {
        importId: '',
        status: 'idle',
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
        urls: {},

        init() {
            this.urls = { ...this.$el.dataset };

            // --- Restore from localStorage first (survives a page refresh) ---
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
                        return;
                    }
                } catch (e) {}
            }

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
                const res = await fetch(this.urls.checkUrl, {
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
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
                            localStorage.setItem(
                                'uco_active_import',
                                JSON.stringify({
                                    importId: this.importId,
                                    fileName: this.fileName,
                                    format: this.format,
                                }),
                            );
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
            this.poll();
            this.polling = setInterval(() => this.poll(), 2000);
        },

        async poll() {
            if (!this.importId) return;
            try {
                const res = await fetch(this.urls.progressUrl.replace('__ID__', this.importId), {
                    headers: {
                        Accept: 'application/json',
                    },
                });
                const data = await res.json();

                this.status = data.status || 'processing';
                this.total = data.total || 0;
                this.current = data.current || 0;
                this.success = data.success || 0;
                this.skipped = data.skipped || 0;
                this.errors = data.errors || [];
                this.percent = this.total > 0 ? Math.min(100, Math.round((this.current / this.total) * 100)) : 0;

                // --- Live table refresh: every 3rd poll (~6 s) ---
                this.pollCount = (this.pollCount || 0) + 1;
                if (this.pollCount % 3 === 0 || this.current !== this._lastCurrent) {
                    this._lastCurrent = this.current;
                    this._refreshPageList();
                }

                if (this.status === 'completed' || this.status === 'failed') {
                    clearInterval(this.polling);
                    this.polling = null;
                    localStorage.removeItem('uco_active_import');
                    this._refreshPageList();
                    fetch(this.urls.clearUrl, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                        },
                    });
                }
            } catch (e) {
                console.error('Progress poll error:', e);
            }
        },

        _refreshPageList() {
            if (document.getElementById('users-list-container')) {
                window.dispatchEvent(new CustomEvent('ajax-update-list'));
                window.dispatchEvent(new CustomEvent('ajax-update-stats'));
            }
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

            localStorage.removeItem('uco_active_import');

            fetch(this.urls.clearUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            });

            if (wasCompleted) {
                window.location.reload();
            }
        },
    };
}
