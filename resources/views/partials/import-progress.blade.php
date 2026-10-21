<div x-data="sharedImportManager()" data-check-url="{{ route('import.check', [], false) }}" data-progress-url="{{ route('import.progress', ['sessionId' => '__ID__'], false) }}" data-clear-url="{{ route('import.clear', [], false) }}" @start-import.window="uploadImportFile($event.detail)" class="relative">

    {{-- --- Small card while an import runs --- --}}
    <div x-show="visible" x-cloak class="card fixed inset-x-4 bottom-4 z-50 space-y-3 !p-4 shadow-lg sm:left-auto sm:right-6 sm:w-80" role="status">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-sm text-gray-600" x-text="status === 'uploading' ? 'Uploading the file' : (status === 'completed' ? 'Import finished' : (status === 'failed' ? 'Import failed' : 'Importing'))"></p>
                <p class="truncate text-base font-semibold text-gray-900" x-text="status === 'uploading' ? fileName : (format || 'Working')"></p>
            </div>
            <button type="button" x-show="status === 'completed' || status === 'failed'" @click="dismiss()" class="btn btn-quiet btn-sm" x-cloak>Close</button>
        </div>

        <div class="h-2 w-full overflow-hidden rounded bg-gray-200" role="progressbar" aria-valuemin="0" aria-valuemax="100" :aria-valuenow="status === 'uploading' ? uploadPercent : percent">
            <div class="h-full rounded bg-brand-700 transition-all duration-300" :style="'width: ' + (status === 'uploading' ? uploadPercent : percent) + '%'"></div>
        </div>

        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-700" x-text="(status === 'uploading' ? uploadPercent : percent) + '%'"></p>
            <button type="button" @click="maximize()" class="text-link text-sm">See details</button>
        </div>
    </div>

    {{-- --- Details dialog --- --}}
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="import-details-title">
        <div class="modal-backdrop" @click="status === 'completed' || status === 'failed' ? dismiss() : minimize()"></div>

        <div class="modal-panel flex max-h-[90vh] max-w-2xl flex-col">
            <div class="mb-4 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h2 id="import-details-title" class="card-title">Import details</h2>
                    <p class="mt-1 truncate text-base text-gray-600" x-text="(format ? format + '. ' : '') + 'File: ' + (fileName || 'import file')"></p>
                </div>
                <button type="button" @click="minimize()" class="btn btn-secondary btn-sm">Minimise</button>
            </div>

            <div class="flex-1 space-y-5 overflow-y-auto">
                <div>
                    <div class="mb-2 flex justify-between text-base text-gray-800">
                        <span x-text="status === 'uploading' ? 'Uploading the file' : (status === 'completed' ? 'Finished' : (status === 'failed' ? 'Failed' : 'Importing records'))"></span>
                        <span x-text="(status === 'uploading' ? uploadPercent : percent) + '%'"></span>
                    </div>
                    <div class="h-3 w-full overflow-hidden rounded bg-gray-200">
                        <div class="h-full rounded bg-brand-700 transition-all duration-300" :style="'width: ' + (status === 'uploading' ? uploadPercent : percent) + '%'"></div>
                    </div>
                </div>

                <dl class="grid grid-cols-3 gap-3 text-center">
                    <div class="rounded-md border border-line p-3">
                        <dt class="text-sm text-gray-600">Processed</dt>
                        <dd class="text-2xl font-semibold text-gray-900" x-text="status === 'uploading' ? '-' : current + ' / ' + total"></dd>
                    </div>
                    <div class="rounded-md border border-line p-3">
                        <dt class="text-sm text-gray-600">Saved</dt>
                        <dd class="text-2xl font-semibold text-green-800" x-text="status === 'uploading' ? '-' : success"></dd>
                    </div>
                    <div class="rounded-md border border-line p-3">
                        <dt class="text-sm text-gray-600">Skipped</dt>
                        <dd class="text-2xl font-semibold text-red-800" x-text="status === 'uploading' ? '-' : skipped"></dd>
                    </div>
                </dl>

                <div>
                    <h3 class="mb-2 text-base font-semibold text-gray-900">Skipped rows and warnings</h3>
                    <p x-show="errors.length === 0 && status !== 'uploading'" class="text-base text-gray-700">Nothing was skipped so far.</p>
                    <ul x-show="errors.length > 0" class="max-h-56 space-y-2 overflow-y-auto">
                        <template x-for="(error, index) in errors" :key="index">
                            <li class="rounded-md border border-red-300 bg-red-50 p-3 text-base text-red-900" x-text="'Row ' + (index + 1) + ': ' + error"></li>
                        </template>
                    </ul>
                </div>
            </div>

            <div class="mt-5 flex flex-col gap-3 border-t border-line pt-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-gray-600">You can close this window. The import keeps running in the background.</p>
                <div class="flex gap-2">
                    <template x-if="status === 'uploading' || status === 'processing'">
                        <button type="button" @click="minimize()" class="btn btn-secondary">Minimise</button>
                    </template>
                    <template x-if="status === 'completed' || status === 'failed'">
                        <button type="button" @click="dismiss()" class="btn btn-primary" x-text="status === 'completed' ? 'Done' : 'Close'"></button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
