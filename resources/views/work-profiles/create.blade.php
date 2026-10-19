<x-app-layout>
    @section('title', 'Add a work profile')

    @push('styles')
        @vite('resources/css/vendor/tom-select.css')
    @endpush

    <div class="mx-auto max-w-3xl space-y-6">
        <x-ui.page-header title="Add a work profile" subtitle="Show where you work and what you do there. You do not need to own the company." />

        <form method="POST" action="{{ route('intrapreneurs.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <x-forms.section title="Your job">
                @include('work-profiles.partials.fields')

                <x-forms.field name="logo_url" label="Company logo (optional)" :show-error="false">
                    <input type="file" name="logo_url" id="logo_url" accept="image/*" class="field-input @error('logo_url') !border-red-700 @enderror !h-auto py-2">
                    @error('logo_url')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </x-forms.field>

                <x-forms.field name="job_description" label="What you do" required class="md:col-span-2">
                    <textarea name="job_description" id="job_description" rows="5" required class="field-input @error('job_description') !border-red-700 @enderror" placeholder="Describe your role, your daily work and what you have contributed.">{{ old('job_description') }}</textarea>
                </x-forms.field>
            </x-forms.section>

            @if (auth()->user()->isAdmin())
                <x-forms.section title="Owner">
                    <x-forms.field name="user_id" label="Student" required class="md:col-span-2">
                        <select name="user_id" id="user_id" data-tom-select="Choose the student" required class="block w-full">
                            <option value="">Choose the student</option>
                            @foreach ($owners as $ownerUser)
                                <option value="{{ $ownerUser->id }}" {{ old('user_id') == $ownerUser->id ? 'selected' : '' }}>{{ $ownerUser->name }} ({{ $ownerUser->email }})</option>
                            @endforeach
                        </select>
                    </x-forms.field>
                </x-forms.section>
            @endif

            <x-forms.actions>
                <a href="{{ $cancelUrl }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Add work profile</button>
            </x-forms.actions>
        </form>
    </div>
</x-app-layout>
