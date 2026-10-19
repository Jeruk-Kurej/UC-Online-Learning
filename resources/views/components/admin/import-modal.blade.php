{{-- --- Import dialog for an admin list (importDropzone Alpine component; opened through showImportModal) --- --}}
@props(['title', 'action', 'inputId', 'nameId'])

<div x-show="showImportModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="import-title" @keydown.escape.window="showImportModal = false">
    <div class="modal-backdrop" @click="showImportModal = false"></div>

    <div class="modal-panel" x-data="importDropzone(@js($inputId), @js($nameId))">
        <h2 id="import-title" class="card-title">{{ $title }}</h2>
        <p class="mt-1 text-base text-gray-600">Choose the CSV or Excel file with the form responses. Profiles are added or updated from it.</p>

        <form action="{{ $action }}" method="POST" enctype="multipart/form-data" @submit.prevent="$dispatch('start-import', $el)" class="mt-5 space-y-5">
            @csrf
            <div class="relative rounded-md border border-dashed p-8 text-center focus-within:ring-2 focus-within:ring-brand-300" :class="isDragging ? 'border-brand-600 bg-brand-50' : 'border-gray-400 hover:border-brand-600'" @dragover="handleDragOver" @dragleave="handleDragLeave" @drop="handleDrop">
                <input type="file" name="file" required class="sr-only" id="{{ $inputId }}" @change="showChosenFile">
                <label for="{{ $inputId }}" class="cursor-pointer">
                    <i class="bi bi-file-earmark-spreadsheet text-4xl text-brand-700" aria-hidden="true"></i>
                    <span class="mt-3 block text-base font-medium text-gray-900" id="{{ $nameId }}">Choose a file or drop it here</span>
                </label>
            </div>

            <x-forms.actions>
                <button type="button" @click="$dispatch('close-import-modal')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Start import</button>
            </x-forms.actions>
        </form>
    </div>
</div>
