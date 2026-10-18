<x-app-layout>
    @section('title', $userToEdit ? 'Edit ' . $userToEdit->name : 'Add a user')

    @push('styles')
        @vite('resources/css/vendor/tom-select.css')
    @endpush

    <div class="mx-auto max-w-3xl space-y-6">
        <x-ui.page-header :title="$userToEdit ? 'Edit user' : 'Add a user'" :subtitle="$userToEdit ? $userToEdit->name : 'Create an account for a student or alumnus.'" />

        <form data-user-form data-enter-blurs method="POST" action="{{ $userToEdit ? route('users.update', $userToEdit) : route('users.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if ($userToEdit)
                @method('PUT')
            @endif

            <x-forms.section title="Account">
                <x-forms.input name="name" label="Full name" :value="$userToEdit?->name" required />
                <x-forms.input name="email" label="Email" type="email" :value="$userToEdit?->email" required />

                <x-forms.field name="role" label="Role" required>
                    <select name="role" id="role" required class="block w-full">
                        <option value="user" {{ old('role', $userToEdit?->role) === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $userToEdit?->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </x-forms.field>

                <x-forms.field name="student_status" label="Student status" required id="student_status_container">
                    <select name="student_status" id="student_status" data-tom-select="Choose a status" required class="block w-full">
                        <option value="student aktif" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'student aktif' ? 'selected' : '' }}>Student, active</option>
                        <option value="student non aktif" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'student non aktif' ? 'selected' : '' }}>Student, inactive</option>
                        <option value="student cuti" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'student cuti' ? 'selected' : '' }}>Student, on leave</option>
                        <option value="alumni aktif" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'alumni aktif' ? 'selected' : '' }}>Alumni, active</option>
                        <option value="alumni non aktif" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'alumni non aktif' ? 'selected' : '' }}>Alumni, inactive</option>
                        <option value="alumni cuti" {{ old('student_status', $userToEdit ? $userToEdit->student_status : 'student aktif') === 'alumni cuti' ? 'selected' : '' }}>Alumni, on leave</option>
                    </select>
                </x-forms.field>

                <x-forms.input name="password" id="password" type="password" :label="$userToEdit ? 'New password (leave empty to keep the current one)' : 'Password'" :required="!$userToEdit" autocomplete="new-password" />
                <x-forms.field name="password_confirmation" label="Repeat password" :required="!$userToEdit" :show-error="false">
                    <input type="password" name="password_confirmation" id="password_confirmation" @unless ($userToEdit) required @endunless autocomplete="new-password" class="field-input">
                </x-forms.field>
            </x-forms.section>

            <x-forms.section title="Name and contact" id="identity_contact_section">
                <x-forms.input name="prefix_title" label="Title before the name" :value="$userToEdit?->prefix_title" />
                <x-forms.input name="suffix_title" label="Title after the name" :value="$userToEdit?->suffix_title" />
                <x-forms.input name="personal_email" label="Personal email" type="email" :value="$userToEdit?->personal_email" />
                <x-forms.input name="phone_number" label="Phone number" type="tel" :value="$userToEdit?->phone_number" />
                <x-forms.input name="mobile_number" label="Mobile number" type="tel" :value="$userToEdit?->mobile_number" />
                <x-forms.input name="whatsapp" label="WhatsApp number" type="tel" :value="$userToEdit?->whatsapp" />
                <x-forms.input name="linkedin" label="LinkedIn address" type="url" :value="$userToEdit?->linkedin" wrapper-class="md:col-span-2" />

                @if ($userToEdit)
                    <x-forms.removable-image class="md:col-span-2" label="Profile photo" name="profile_photo_url" delete-name="delete_profile_photo_url" :url="$userToEdit->profile_photo_url" :initial="$userToEdit->initial" noun="Photo" hint="JPG, PNG or WEBP, up to 5 MB.">
                        @error('profile_photo_url')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </x-forms.removable-image>
                @else
                    <x-forms.file-drop class="md:col-span-2" label="Profile photo" name="profile_photo_url" accept="image/*" prompt="Choose a photo or drop it here" formats="JPG, PNG or WEBP">
                        <x-slot:selection>
                            <x-forms.picked-image label="Selected photo" />
                        </x-slot:selection>
                        @error('profile_photo_url')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </x-forms.file-drop>
                @endif
            </x-forms.section>

            <x-forms.section title="Studies and activities" id="academic_extra_section">
                <x-forms.input name="nis" label="Student number" :value="$userToEdit?->nis" />
                <x-forms.input name="year_of_enrollment" label="Year joined" :value="$userToEdit?->year_of_enrollment" placeholder="For example 2023" />
                <x-forms.input name="graduate_year" label="Year graduated" :value="$userToEdit?->graduate_year" placeholder="For example 2027" />
                <x-forms.input name="major" label="Major" :value="$userToEdit?->major" />

                <x-forms.field name="current_status" label="Entrepreneur or intrapreneur" :show-error="false">
                    <select name="current_status" id="current_status" data-tom-select="Choose one" class="block w-full">
                        <option value="">Choose one</option>
                        <option value="Entrepreneur" {{ old('current_status', $userToEdit?->current_status) == 'Entrepreneur' ? 'selected' : '' }}>Entrepreneur</option>
                        <option value="Intrapreneur" {{ old('current_status', $userToEdit?->current_status) == 'Intrapreneur' ? 'selected' : '' }}>Intrapreneur</option>
                    </select>
                </x-forms.field>

                @if ($userToEdit)
                    <div class="space-y-3 md:col-span-2" x-data="activityFiles(@js($userToEdit->activity_files))">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <p class="field-label !mb-0">Activity files</p>
                            <x-files.add-files name="activities_docs" />
                        </div>
                        <x-files.activity-previews />
                        @error('activities_docs')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <x-forms.file-drop class="md:col-span-2" label="Activity file" name="activities_doc_url" accept=".pdf" prompt="Choose a PDF or drop it here" formats="PDF only" icon="bi-file-earmark-pdf">
                        <x-slot:selection>
                            <div x-show="selected" class="mb-3 flex items-center justify-between gap-3 rounded-md border border-line bg-gray-50 p-3">
                                <p class="min-w-0 truncate text-base text-gray-900" x-text="fileName">Selected file</p>
                                <button type="button" @click="clear()" class="btn btn-secondary btn-sm">Remove</button>
                            </div>
                        </x-slot:selection>
                        @error('activities_doc_url')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </x-forms.file-drop>
                @endif

                <x-forms.field name="testimony" label="Testimony" :show-error="false" class="md:col-span-2">
                    <textarea name="testimony" id="testimony" rows="3" class="field-input">{{ old('testimony', $userToEdit?->testimony) }}</textarea>
                </x-forms.field>

                <x-forms.input name="activities_caption" label="Caption for the activity photos" :value="$userToEdit?->activities_caption" wrapper-class="md:col-span-2" />
            </x-forms.section>

            <x-forms.section title="Businesses" hint="Add businesses to this user. This never takes a business away from anyone else who runs it." id="business_assignment_section" :grid="false">
                <x-forms.field name="owned_businesses" label="Businesses to give to this user" :show-error="false">
                    <select name="owned_businesses[]" id="owned_businesses" data-tom-select="Choose businesses" multiple class="block w-full">
                        @foreach ($availableBusinesses as $b)
                            <option value="{{ $b->id }}" {{ in_array($b->id, old('owned_businesses', $ownedBusinessIds)) ? 'selected' : '' }}>{{ $b->name }} (current owner: {{ $b->user->name ?? 'none' }})</option>
                        @endforeach
                    </select>
                </x-forms.field>
            </x-forms.section>

            <x-forms.actions>
                <a href="{{ $userToEdit ? route('users.show', $userToEdit) : route('users.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ $userToEdit ? 'Save changes' : 'Add user' }}</button>
            </x-forms.actions>
        </form>
    </div>
</x-app-layout>
