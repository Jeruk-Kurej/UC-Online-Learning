<?php

// --- x-forms.field: label, required star, wrapper classes and validation message per form look ---

test('field renders the label, the star and the control', function () {
    $html = (string) $this->withViewErrors([])->blade('<x-forms.field look="user" name="email" label="Email Address" required><input id="email"></x-forms.field>');

    expect($html)
        ->toContain('<label for="email" class="form-label-custom">Email Address')
        ->toContain('<span class="text-red-500">*</span>')
        ->toContain('<input id="email">')
        ->not->toContain('text-red-600');
});

test('field shows its validation message in the look of its form', function () {
    $this->withViewErrors(['name' => 'The name field is required.'])
        ->blade('<x-forms.field look="work-profile" name="name" label="Company Name"><input></x-forms.field>')
        ->assertSee('<p class="mt-1 text-xs font-medium text-red-600">The name field is required.</p>', false);
});

test('business fields wrap the absolute message only when they show one', function () {
    expect((string) $this->withViewErrors([])->blade('<x-forms.field look="business" name="name" label="Name" class="md:col-span-2"><input></x-forms.field>'))
        ->toContain('<div class="relative pb-5 md:col-span-2">');

    expect((string) $this->withViewErrors([])->blade('<x-forms.field look="business" name="phone" label="Phone" :show-error="false"><input></x-forms.field>'))
        ->not->toContain('relative pb-5')
        ->not->toContain('class=""');
});

test('field points its label at a different id when asked', function () {
    expect((string) $this->withViewErrors([])->blade('<x-forms.field look="offering" name="owner_ids" for="owners" label="Owners"><select id="owners"></select></x-forms.field>'))
        ->toContain('<label for="owners" class="mb-2 block text-sm font-medium text-gray-700">Owners');
});
