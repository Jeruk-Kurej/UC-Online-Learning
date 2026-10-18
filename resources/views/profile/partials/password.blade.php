{{-- --- Change password: works without e-mail, asks for the current password first --- --}}
<form method="POST" action="{{ route('password.update') }}" id="password" class="card space-y-5">
    @csrf
    @method('PUT')

    <div>
        <h2 class="card-title">Change password</h2>
        <p class="mt-1 text-base text-gray-600">Use at least 8 characters. You stay logged in after the change.</p>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <x-forms.field name="current_password" label="Current password" class="md:col-span-2" :show-error="false">
            <input type="password" name="current_password" id="current_password" autocomplete="current-password" required class="field-input @error('current_password', 'updatePassword') !border-red-700 @enderror">
            @error('current_password', 'updatePassword')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </x-forms.field>

        <x-forms.field name="password" for="password-new" label="New password" :show-error="false">
            <input type="password" name="password" id="password-new" autocomplete="new-password" required class="field-input @error('password', 'updatePassword') !border-red-700 @enderror">
            @error('password', 'updatePassword')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </x-forms.field>

        <x-forms.field name="password_confirmation" label="Repeat new password" :show-error="false">
            <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" required class="field-input">
        </x-forms.field>
    </div>

    <x-forms.actions>
        <button type="submit" class="btn btn-primary">Change password</button>
    </x-forms.actions>
</form>
