{{-- --- Previews for the activityFiles Alpine component: existing files (flag/undo delete) and newly picked files --- --}}
@props(['blurOverlay' => false])

{{-- --- Existing files flagged for deletion --- --}}
<template x-for="url in deletedFiles" :key="url">
    <input type="hidden" name="delete_activities_files[]" :value="url">
</template>

{{-- --- Existing files --- --}}
<div x-show="existingFiles.length > 0" class="w-full">
    <p class="mb-2 text-xs font-bold text-gray-400">Existing Uploaded Documents</p>
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
        <template x-for="item in existingFiles" :key="item.url">
            <div class="group relative flex aspect-video items-center justify-center overflow-hidden rounded-sm transition-all duration-200">

                <template x-if="item.previewUrl">
                    <div class="relative flex h-full w-full items-center justify-center overflow-hidden">
                        <img :src="item.previewUrl" class="relative z-10 max-h-full max-w-full rounded-sm object-contain" referrerpolicy="no-referrer">
                    </div>
                </template>

                <template x-if="!item.previewUrl">
                    <div class="flex flex-col items-center justify-center p-3 text-center">
                        <div class="mb-1 flex h-10 w-10 items-center justify-center rounded-xl border border-red-100 bg-red-50 text-red-500">
                            <i class="bi bi-file-earmark-pdf-fill text-xl"></i>
                        </div>
                        <span class="w-full max-w-[120px] truncate text-xs font-bold text-slate-600" x-text="item.name"></span>
                        <span class="mt-0.5 text-xs font-bold text-slate-400">PDF</span>
                    </div>
                </template>

                <div x-show="!isDeleted(item.url)" class="{{ $blurOverlay ? 'backdrop-blur-[1px]' : '' }} absolute inset-0 flex items-center justify-center bg-slate-900/60 opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                    <button type="button" @click="deleteFile(item.url)" class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 text-white shadow-md transition-colors hover:bg-red-600" title="Delete file">
                        <i class="bi bi-trash-fill text-sm"></i>
                    </button>
                </div>

                <div x-show="isDeleted(item.url)" class="absolute inset-0 flex flex-col items-center justify-center bg-red-50/90 p-2 text-center transition-all duration-200">
                    <i class="bi bi-trash3 mb-1 text-lg text-red-500"></i>
                    <span class="mb-1.5 text-xs font-bold text-red-600">Flagged to Delete</span>
                    <button type="button" @click="undoDeleteFile(item.url)" class="flex items-center gap-1 rounded border border-slate-200 bg-white px-2 py-1 text-xs font-bold text-slate-700 shadow-sm transition-colors hover:bg-slate-100 hover:text-slate-900">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Undo</span>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

{{-- --- Newly selected files --- --}}
<div x-show="newFiles.length > 0" class="w-full rounded-sm p-4">
    <div class="mb-3 flex items-center justify-between border-b border-slate-200 pb-2">
        <span class="flex items-center gap-1.5 text-xs font-bold text-slate-500">
            <i class="bi bi-check2-circle text-sm text-green-500"></i>
            <span x-text="newFiles.length + ' New Files Selected'"></span>
        </span>
        <button type="button" @click="clearNewFiles()" class="flex items-center gap-1 text-xs font-bold text-amber-600 transition-colors hover:text-amber-700">
            <i class="bi bi-trash3-fill"></i>
            <span>Clear Selection</span>
        </button>
    </div>
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
        <template x-for="(file, idx) in newFiles" :key="idx">
            <div class="relative flex aspect-video items-center justify-center overflow-hidden rounded-sm">

                <template x-if="file.preview">
                    <div class="relative flex h-full w-full items-center justify-center overflow-hidden">
                        <img :src="file.preview" class="relative z-10 max-h-full max-w-full rounded-sm object-contain" referrerpolicy="no-referrer">
                    </div>
                </template>

                <template x-if="!file.preview">
                    <div class="flex flex-col items-center justify-center p-3 text-center">
                        <div class="mb-1 flex h-8 w-8 items-center justify-center rounded-lg border border-red-100 bg-red-50 text-red-500">
                            <i class="bi bi-file-earmark-pdf-fill text-lg"></i>
                        </div>
                        <span class="w-full max-w-[120px] truncate text-xs font-bold text-slate-700" x-text="file.name"></span>
                        <span class="mt-0.5 text-xs font-bold text-slate-400" x-text="file.size"></span>
                    </div>
                </template>
            </div>
        </template>
    </div>
</div>
