<x-app-layout>
    @section('title', 'My testimony')

    <div class="mx-auto max-w-3xl space-y-6" x-data="{ testimony: @js($user->testimony ?? '') }">
        <x-ui.page-header title="My testimony" subtitle="Tell others what UC Online Learning has meant for you. It shows on your profile." />

        <form action="{{ route('uc-testimonies.store') }}" method="POST" class="space-y-6">
            @csrf

            <x-forms.section title="Your testimony" :grid="false">
                <x-forms.field name="content" label="What was your experience?" required>
                    <textarea name="content" id="content" rows="6" maxlength="255" x-model="testimony" required class="field-input @error('content') !border-red-700 @enderror"></textarea>
                    <p class="field-hint" aria-live="polite"><span x-text="testimony.length"></span> of 255 characters</p>
                </x-forms.field>
            </x-forms.section>

            <x-forms.actions>
                <button type="submit" class="btn btn-primary">Save testimony</button>
            </x-forms.actions>
        </form>

        <section class="space-y-3" aria-labelledby="testimony-preview">
            <h2 id="testimony-preview" class="section-heading">How it looks</h2>
            <figure class="card">
                <blockquote class="whitespace-pre-line text-lg text-gray-800" x-text="testimony ? '\u201C' + testimony + '\u201D' : 'Your testimony will appear here.'"></blockquote>
                <figcaption class="mt-4 flex items-center gap-3 border-t border-line pt-4">
                    <x-ui.thumb :src="$user->profile_photo_url" :name="$user->name" size="h-12 w-12" round class="text-lg" />
                    <div>
                        <p class="text-base font-semibold text-brand-900">{{ $user->name }}</p>
                        <p class="text-sm text-gray-600">{{ $user->current_status ?? 'Member' }}</p>
                    </div>
                </figcaption>
            </figure>
        </section>
    </div>
</x-app-layout>
