{{-- --- CSV/Excel import dialog for an admin list (importDropzone Alpine component; opened through showImportModal) --- --}}
@props(['title', 'action', 'inputId', 'nameId'])

<div x-show="showImportModal"
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.self="showImportModal = false"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-8"
         x-transition:enter="transition ease-out duration-200 transform"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150 transform"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         x-data="importDropzone('{{ $inputId }}', '{{ $nameId }}')"
         @click.stop>
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-2xl font-black text-gray-900">{{ $title }}</h3>
            <button type="button" @click="$dispatch('close-import-modal')" class="p-1.5 rounded-md hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>
        <p class="text-sm text-gray-500 mb-6">Upload the UC Online Form Responses CSV file to sync profiles.</p>

        <form action="{{ $action }}" method="POST" enctype="multipart/form-data" @submit.prevent="$dispatch('start-import', $el)" class="space-y-6">
            @csrf
            <div class="border-2 border-dashed rounded-lg p-10 text-center transition group"
                 :class="isDragging ? 'border-uco-orange-500 bg-orange-50' : 'border-gray-200 hover:border-uco-orange-300'"
                 @dragover="handleDragOver"
                 @dragleave="handleDragLeave"
                 @drop="handleDrop">
                <input type="file" name="file" required class="hidden" id="{{ $inputId }}" @change="showChosenFile">
                <label for="{{ $inputId }}" class="cursor-pointer">
                    <i class="bi bi-file-earmark-spreadsheet text-4xl text-gray-300 group-hover:text-uco-orange-500 transition"></i>
                    <p class="mt-4 text-sm font-bold text-gray-600" id="{{ $nameId }}">Click to select or drag CSV/Excel file here</p>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="button" @click="$dispatch('close-import-modal')" class="btn-uco btn-uco-neutral flex-1 py-3">Cancel</button>
                <button type="submit" class="btn-uco btn-uco-primary flex-1 py-3">Start Import</button>
            </div>
        </form>
    </div>
</div>
