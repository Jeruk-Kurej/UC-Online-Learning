<div x-data="toastStack" @notify.window="add($event)" class="flex flex-col gap-3">
    <template x-for="toast in toasts" :key="toast.id">
        <x-flash.toast-item />
    </template>
</div>
