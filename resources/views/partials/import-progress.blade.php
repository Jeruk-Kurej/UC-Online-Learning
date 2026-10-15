<div x-data="sharedImportManager()"
     x-init="init()"
     data-check-url="{{ route('import.check', [], false) }}"
     data-progress-url="{{ route('import.progress', ['sessionId' => '__ID__'], false) }}"
     data-clear-url="{{ route('import.clear', [], false) }}"
     @start-import.window="uploadImportFile($event.detail)"
     class="relative">

    {{-- --- SVG Gradients --- --}}
    <svg class="pointer-events-none absolute h-0 w-0">
        <defs>
            <linearGradient id="orangeGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#f7931e" />
                <stop offset="100%" stop-color="#ff5e3a" />
            </linearGradient>
        </defs>
    </svg>

    {{-- --- Floating Progress Card (Bottom-Right) --- --}}
    <div x-show="visible"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         @click="maximize()"
         class="fixed bottom-6 right-6 z-50 w-80 cursor-pointer rounded-lg border border-gray-200 bg-white p-4 shadow-xl transition-shadow duration-200 hover:shadow-2xl"
         x-cloak>

        <div class="flex items-center justify-between gap-3">
            {{-- --- Circular Progress Ring --- --}}
            <div class="relative h-12 w-12 flex-shrink-0">
                <svg class="h-full w-full -rotate-90 transform">
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

            {{-- --- Text Info --- --}}
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500"
                   x-text="status === 'uploading' ? 'Uploading File' : (status === 'completed' ? 'Import Complete' : 'Importing Data')"></p>
                <p class="truncate pr-2 text-sm font-black text-gray-800"
                   x-text="status === 'uploading' ? fileName : (format || 'Processing...')"></p>
            </div>

            {{-- --- Controls / Close Button --- --}}
            <div class="flex items-center gap-1">
                <button x-show="status === 'completed' || status === 'failed'"
                        @click.stop="dismiss()"
                        class="rounded p-1.5 text-gray-400 transition duration-200 hover:bg-gray-100 hover:text-gray-600"
                        x-cloak>
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>
        </div>

        {{-- --- Footer/Mini Banner --- --}}
        <div class="mt-2.5 flex items-center justify-between border-t border-gray-100 pt-2 text-[10px] font-semibold text-gray-400">
            <div class="flex items-center gap-1.5">
                <span class="h-1.5 w-1.5 rounded-full"
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

    {{-- --- Detailed Popup Modal --- --}}
    <div x-show="showDetailModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm"
         x-cloak>

        <div @click.away="status === 'completed' || status === 'failed' ? dismiss() : minimize()"
             x-show="showDetailModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="flex max-h-[90vh] w-full max-w-2xl flex-col rounded-xl border border-gray-200 bg-white p-8 shadow-2xl">

            {{-- --- Modal Header --- --}}
            <div class="mb-6 flex items-start justify-between">
                <div>
                    <div class="mb-1.5 flex items-center gap-2.5">
                        <span class="inline-flex items-center rounded-md border border-uco-orange-200 bg-uco-orange-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-uco-orange-700"
                              x-text="format || 'Importer'"></span>
                        <template x-if="status === 'processing'">
                            <span class="inline-flex animate-pulse items-center gap-1 rounded-md border border-blue-200 bg-blue-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-blue-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                Background Importing
                            </span>
                        </template>
                        <template x-if="status === 'completed'">
                            <span class="inline-flex items-center rounded-md border border-emerald-200 bg-emerald-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-700">
                                Completed
                            </span>
                        </template>
                    </div>
                    <h3 class="text-2xl font-black tracking-tight text-gray-900">Import Progress Details</h3>
                    <p class="mt-1 max-w-md truncate text-xs font-semibold text-gray-400" x-text="'File: ' + (fileName || 'Import File')"></p>
                </div>
                <button @click="minimize()"
                        class="rounded-md p-2 text-gray-400 transition duration-200 hover:bg-gray-100 hover:text-gray-600">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>

            {{-- --- Modal Body --- --}}
            <div class="custom-scrollbar flex-1 space-y-6 overflow-y-auto pr-1">

                {{-- --- Progress Bar Indicator --- --}}
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                    <div class="mb-2 flex justify-between text-sm font-bold text-gray-700">
                        <span x-text="status === 'uploading' ? 'Uploading file to server...' : (status === 'completed' ? 'Processing completed!' : 'Importing records to database...')"></span>
                        <span class="text-uco-orange-600" x-text="(status === 'uploading' ? uploadPercent : percent) + '%'"></span>
                    </div>
                    <div class="h-3 w-full overflow-hidden rounded border border-gray-300 bg-gray-100">
                        <div class="h-full rounded bg-uco-orange-500 transition-all duration-300 ease-out"
                             :style="'width: ' + (status === 'uploading' ? uploadPercent : percent) + '%'"></div>
                    </div>
                </div>

                {{-- --- Statistics Cards --- --}}
                <div class="grid grid-cols-3 gap-4">
                    <div class="rounded-lg border border-gray-200 bg-slate-50 p-4 text-center">
                        <p class="mb-1 text-xs font-bold uppercase tracking-wider text-slate-400">Processed</p>
                        <p class="text-2xl font-black text-slate-800" x-text="status === 'uploading' ? '-' : current + ' / ' + total"></p>
                    </div>
                    <div class="rounded-lg border border-emerald-100/50 bg-emerald-50/50 p-4 text-center">
                        <p class="mb-1 text-xs font-bold uppercase tracking-wider text-emerald-500">Success</p>
                        <p class="text-2xl font-black text-emerald-600" x-text="status === 'uploading' ? '-' : success"></p>
                    </div>
                    <div class="rounded-lg border border-rose-100/50 bg-rose-50/50 p-4 text-center">
                        <p class="mb-1 text-xs font-bold uppercase tracking-wider text-rose-500">Skipped</p>
                        <p class="text-2xl font-black text-rose-600" x-text="status === 'uploading' ? '-' : skipped"></p>
                    </div>
                </div>

                {{-- --- Activity Log / Warnings --- --}}
                <div>
                    <h4 class="mb-3 flex items-center gap-2 text-sm font-bold text-gray-700">
                        <i class="bi bi-journal-text text-lg text-gray-400"></i>
                        Import Log & Warning Messages
                    </h4>

                    {{-- --- Placeholder when empty --- --}}
                    <div x-show="errors.length === 0 && status !== 'uploading'"
                         class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded border border-emerald-200 bg-emerald-50">
                            <i class="bi bi-check-lg text-xl font-bold text-emerald-500"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-700">No warnings encountered</p>
                        <p class="mt-1 text-xs text-gray-400">If files contain skipped rows or data validation mismatches, they will appear here in real-time.</p>
                    </div>

                    {{-- --- List of warnings --- --}}
                    <div x-show="errors.length > 0"
                         class="overflow-hidden rounded-lg border border-gray-200">
                        <div class="custom-scrollbar max-h-56 space-y-2 overflow-y-auto bg-slate-50 p-3">
                            <template x-for="(error, index) in errors" :key="index">
                                <div class="flex items-start gap-2.5 rounded-lg border border-gray-200 bg-white p-3">
                                    <div class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded border border-rose-200 bg-rose-50">
                                        <i class="bi bi-exclamation-triangle text-xs text-rose-500"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="mb-0.5 flex items-center justify-between">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-500" x-text="'Row Skip #' + (index + 1)"></span>
                                        </div>
                                        <p class="break-words text-xs font-semibold leading-relaxed text-gray-700" x-text="error"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>

            {{-- --- Modal Footer --- --}}
            <div class="mt-6 flex items-center justify-between border-t border-gray-100 pt-4">
                <p class="max-w-sm text-[10px] font-semibold text-gray-400">
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
