<x-app-layout>
    @section('title', 'My profile')

    <div class="mx-auto max-w-3xl space-y-6">
        <x-ui.page-header title="My profile" subtitle="Keep your details up to date. Your name, photo and studies show on your public profile." />

        <form data-enter-blurs method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <x-forms.section title="Photo" :grid="false">
                <x-forms.removable-image label="Profile photo" name="profile_photo" delete-name="delete_profile_photo" :url="$user->profile_photo_url" :initial="$user->initials" noun="Photo" hint="Choose an image from your device." />
            </x-forms.section>

            <x-forms.section title="Name">
                <x-forms.input name="prefix_title" label="Title before your name" :value="$user->prefix_title" placeholder="For example Prof. Dr." />
                <x-forms.input name="name" label="Full name" :value="$user->name" required />
                <x-forms.input name="suffix_title" label="Title after your name" :value="$user->suffix_title" placeholder="For example S.Kom, M.T." />
                <x-forms.input name="personal_email" label="Personal email" type="email" :value="$user->personal_email" />
            </x-forms.section>

            <x-forms.section title="Contact details">
                <x-forms.input name="phone_number" label="Phone number" :value="$user->phone_number" />
                <x-forms.input name="mobile_number" label="Mobile number" :value="$user->mobile_number" />
                <x-forms.input name="whatsapp" label="WhatsApp" :value="$user->whatsapp" />
                <x-forms.input name="linkedin" label="LinkedIn username" :value="$user->linkedin" placeholder="For example johndoe" />

                <label class="flex items-start gap-3 text-base text-gray-900 md:col-span-2">
                    <input type="checkbox" name="show_contact_details" value="1" class="mt-1 h-5 w-5 rounded border-gray-400 text-brand-700 focus:ring-brand-300" {{ old('show_contact_details', $user->show_contact_details) ? 'checked' : '' }}>
                    <span>Show my contact details on my public profile</span>
                </label>
            </x-forms.section>

            <x-forms.section title="Studies" hint="Your student number and major are set by the UC Online Learning team.">
                <x-ui.readonly label="Student number" :value="$user->nis" />
                <x-ui.readonly label="Major" :value="$user->major" />

                <x-forms.field name="current_status" label="I am an">
                    <select name="current_status" id="current_status" class="field-input">
                        <option value="Entrepreneur" {{ old('current_status', $user->current_status) == 'Entrepreneur' ? 'selected' : '' }}>Entrepreneur (I run a business)</option>
                        <option value="Intrapreneur" {{ old('current_status', $user->current_status) == 'Intrapreneur' ? 'selected' : '' }}>Intrapreneur (I work for a company)</option>
                    </select>
                </x-forms.field>
                <x-forms.input name="year_of_enrollment" label="Year you joined" :value="$user->year_of_enrollment" />
                <x-forms.input name="graduate_year" label="Year you graduated" :value="$user->graduate_year" />
            </x-forms.section>

            <x-forms.section title="Skills" :grid="false">
                <x-forms.input name="skills" label="Your skills" :value="$user->skill_list" hint="Separate them with commas, for example Marketing, Excel, Public speaking." />
            </x-forms.section>

            <x-forms.section title="Certificates" hint="Photos and PDFs of your certificates, shown on your profile." :grid="false">
                <div class="space-y-4" x-data="activityFiles(@js($user->editable_certification_files))">
                    <x-files.add-files name="certification_files" />
                    <x-files.activity-previews delete-name="delete_certification_files" />
                </div>
            </x-forms.section>

            <x-forms.section title="Activity documents" hint="Photos and PDFs of your activities, shown on your profile." :grid="false">
                <div class="space-y-4" x-data="activityFiles(@js($user->activity_files))">
                    <x-files.add-files name="activities_files" />
                    <x-files.activity-previews />
                </div>
                <x-forms.input name="activities_caption" label="Caption for the activity photos" :value="$user->activities_caption" placeholder="For example Speaker at the 2025 UC Startup Day" />
            </x-forms.section>

            <x-forms.actions>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </x-forms.actions>
        </form>

        @include('profile.partials.password')
    </div>
</x-app-layout>
