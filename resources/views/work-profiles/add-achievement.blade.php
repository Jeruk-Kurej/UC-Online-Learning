<x-app-layout>
    @section('title', 'Add an achievement')

    <div class="mx-auto max-w-3xl space-y-6">
        <x-ui.page-header title="Add an achievement" :subtitle="$company->name" />

        <form method="POST" action="{{ route('intrapreneurs.add_achievement', $company) }}" class="space-y-6">
            @csrf

            <x-forms.section title="Achievement" :grid="false">
                <x-forms.field name="achievement" label="What did you achieve?" required>
                    <textarea name="achievement" id="achievement" rows="4" required class="field-input @error('achievement') !border-red-700 @enderror" placeholder="For example: winner of the National Business Plan Competition 2026">{{ old('achievement') }}</textarea>
                </x-forms.field>
            </x-forms.section>

            <x-forms.actions>
                <a href="{{ route('intrapreneurs.show', $company) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save achievement</button>
            </x-forms.actions>
        </form>
    </div>
</x-app-layout>
