<div x-data="guestToasts(@js($initial))" @notify.window="add($event)" class="toast-stack">
    <template x-for="toast in toasts" :key="toast.id">
        <x-flash.toast-item />
    </template>
</div>
