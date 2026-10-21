{{-- --- Previews for the activityFiles Alpine component: existing files (mark or keep) and newly picked files; $deleteName carries the removed addresses --- --}}
@props(['deleteName' => 'delete_activities_files'])

<template x-for="url in deletedFiles" :key="url">
    <input type="hidden" name="{{ $deleteName }}[]" :value="url">
</template>

{{-- --- Existing files --- --}}
<div x-show="existingFiles.length > 0" class="w-full">
    <p class="mb-2 text-sm font-semibold text-gray-800">Files you uploaded before</p>
    <ul class="grid grid-cols-2 gap-4 sm:grid-cols-3">
        <template x-for="item in existingFiles" :key="item.url">
            <li class="space-y-2">
                <div class="photo-frame aspect-video w-full" :class="{ 'opacity-40': isDeleted(item.url) }">
                    <template x-if="item.previewUrl">
                        <img :src="item.previewUrl" alt="" class="absolute inset-0 h-full w-full bg-white object-contain" referrerpolicy="no-referrer">
                    </template>
                    <template x-if="!item.previewUrl">
                        <span class="flex flex-col items-center p-2 text-center text-sm text-gray-800">
                            <i class="bi bi-file-earmark-pdf text-3xl text-red-700" aria-hidden="true"></i>
                            <span class="w-full truncate" x-text="item.name"></span>
                        </span>
                    </template>
                </div>
                <button type="button" x-show="!isDeleted(item.url)" @click="deleteFile(item.url)" class="btn btn-secondary btn-sm w-full">Remove</button>
                <button type="button" x-show="isDeleted(item.url)" @click="undoDeleteFile(item.url)" class="btn btn-secondary btn-sm w-full">Keep this file</button>
            </li>
        </template>
    </ul>
</div>

{{-- --- Newly selected files --- --}}
<div x-show="newFiles.length > 0" class="w-full">
    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
        <p class="text-sm font-semibold text-gray-800" x-text="newFiles.length + (newFiles.length === 1 ? ' new file selected' : ' new files selected')"></p>
        <button type="button" @click="clearNewFiles()" class="btn btn-secondary btn-sm">Clear selection</button>
    </div>
    <ul class="grid grid-cols-2 gap-4 sm:grid-cols-3">
        <template x-for="(file, idx) in newFiles" :key="idx">
            <li class="photo-frame aspect-video w-full">
                <template x-if="file.preview">
                    <img :src="file.preview" alt="" class="absolute inset-0 h-full w-full bg-white object-contain">
                </template>
                <template x-if="!file.preview">
                    <span class="flex flex-col items-center p-2 text-center text-sm text-gray-800">
                        <i class="bi bi-file-earmark-pdf text-3xl text-red-700" aria-hidden="true"></i>
                        <span class="w-full truncate" x-text="file.name"></span>
                        <span class="text-gray-600" x-text="file.size"></span>
                    </span>
                </template>
            </li>
        </template>
    </ul>
</div>
