{{-- --- Previews for the activityFiles Alpine component: existing files (flag/undo delete) and newly picked files --- --}}
@props(['blurOverlay' => false])

{{-- --- Existing files flagged for deletion --- --}}
<template x-for="url in deletedFiles" :key="url">
    <input type="hidden" name="delete_activities_files[]" :value="url">
</template>

{{-- --- Existing files --- --}}
<div x-show="existingFiles.length > 0" class="w-full">
    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-2">Existing Uploaded Documents</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        <template x-for="item in existingFiles" :key="item.url">
            <div class="relative group rounded-sm overflow-hidden aspect-video flex items-center justify-center transition-all duration-200">

                <template x-if="item.previewUrl">
                    <div class="w-full h-full relative overflow-hidden flex items-center justify-center">
                        <img :src="item.previewUrl" class="relative z-10 max-w-full max-h-full object-contain rounded-sm" referrerpolicy="no-referrer">
                    </div>
                </template>

                <template x-if="!item.previewUrl">
                    <div class="flex flex-col items-center justify-center p-3 text-center">
                        <div class="w-10 h-10 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center text-red-500 mb-1">
                            <i class="bi bi-file-earmark-pdf-fill text-xl"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 truncate w-full max-w-[120px]" x-text="item.name"></span>
                        <span class="text-[8px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">PDF</span>
                    </div>
                </template>

                <div x-show="!isDeleted(item.url)" class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center {{ $blurOverlay ? 'backdrop-blur-[1px]' : '' }}">
                    <button type="button" @click="deleteFile(item.url)" class="w-8 h-8 rounded-lg bg-red-500 text-white hover:bg-red-600 flex items-center justify-center shadow-md transition-colors" title="Delete file">
                        <i class="bi bi-trash-fill text-sm"></i>
                    </button>
                </div>

                <div x-show="isDeleted(item.url)" class="absolute inset-0 bg-red-50/90 flex flex-col items-center justify-center p-2 text-center transition-all duration-200">
                    <i class="bi bi-trash3 text-red-500 text-lg mb-1"></i>
                    <span class="text-[9px] font-bold text-red-600 uppercase tracking-wider mb-1.5">Flagged to Delete</span>
                    <button type="button" @click="undoDeleteFile(item.url)" class="px-2 py-1 bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 font-bold text-[9px] rounded border border-slate-200 shadow-sm flex items-center gap-1 transition-colors">
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
    <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-200">
        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider flex items-center gap-1.5">
            <i class="bi bi-check2-circle text-green-500 text-sm"></i>
            <span x-text="newFiles.length + ' New Files Selected'"></span>
        </span>
        <button type="button" @click="clearNewFiles()" class="text-[10px] text-amber-600 hover:text-amber-700 font-extrabold flex items-center gap-1 transition-colors">
            <i class="bi bi-trash3-fill"></i>
            <span>Clear Selection</span>
        </button>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        <template x-for="(file, idx) in newFiles" :key="idx">
            <div class="relative rounded-sm overflow-hidden aspect-video flex items-center justify-center">

                 <template x-if="file.preview">
                     <div class="w-full h-full relative overflow-hidden flex items-center justify-center">
                         <img :src="file.preview" class="relative z-10 max-w-full max-h-full object-contain rounded-sm" referrerpolicy="no-referrer">
                     </div>
                 </template>

                <template x-if="!file.preview">
                    <div class="flex flex-col items-center justify-center p-3 text-center">
                        <div class="w-8 h-8 rounded-lg bg-red-50 border border-red-100 flex items-center justify-center text-red-500 mb-1">
                            <i class="bi bi-file-earmark-pdf-fill text-lg"></i>
                        </div>
                        <span class="text-[9px] font-bold text-slate-700 truncate w-full max-w-[120px]" x-text="file.name"></span>
                        <span class="text-[7px] text-slate-400 font-extrabold uppercase mt-0.5" x-text="file.size"></span>
                    </div>
                </template>
            </div>
        </template>
    </div>
</div>
