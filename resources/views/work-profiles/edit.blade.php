<x-app-layout>
    @section('title', 'Edit work profile')

    @push('styles')
        @vite('resources/css/vendor/tom-select.css')
    @endpush

    <div class="mx-auto max-w-3xl space-y-6">
        <x-ui.page-header title="Edit work profile" :subtitle="$company->name" />

        <form method="POST" action="{{ route('intrapreneurs.update', $company) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <x-forms.section title="Your job">
                @include('work-profiles.partials.fields')

                <x-forms.removable-image class="md:col-span-2" label="Company logo" name="logo_url" delete-name="delete_logo" :url="$company->logo_url" :has-image="$company->has_logo" :initial="$company->initial" noun="Logo" hint="JPG, PNG or WEBP, up to 5 MB.">
                    @error('logo_url')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </x-forms.removable-image>

                <x-forms.field name="job_description" label="What you do" required class="md:col-span-2">
                    <textarea name="job_description" id="job_description" rows="5" required class="field-input @error('job_description') !border-red-700 @enderror" placeholder="Describe your role, your daily work and what you have contributed.">{{ old('job_description', $company->job_description) }}</textarea>
                </x-forms.field>
            </x-forms.section>

            <x-forms.actions>
                <a href="{{ route('intrapreneurs.show', $company) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </x-forms.actions>
        </form>
    </div>
</x-app-layout>
