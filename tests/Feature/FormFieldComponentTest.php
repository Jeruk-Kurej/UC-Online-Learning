<?php

// --- x-forms.field: label, required star, wrapper classes and validation message  ---

test('field renders the label, the star and the control', function () {
    $html = (string) $this->withViewErrors([])->blade('<x-forms.field name="email" label="Email Address" required><input id="email"></x-forms.field>');

    expect($html)
        ->toContain('<label for="email" class="field-label">Email Address')
        ->toContain('<span class="text-red-700" aria-hidden="true">*</span>')
        ->toContain('<span class="sr-only">(required)</span>')
        ->toContain('<input id="email">');
});

test('field shows its validation message under the control', function () {
    $this->withViewErrors(['name' => 'The name field is required.'])
        ->blade('<x-forms.field name="name" label="Company Name"><input></x-forms.field>')
        ->assertSee('<p class="field-error">The name field is required.</p>', false);
});

test('fields keep only the classes given to them and never an empty class attribute', function () {
    expect((string) $this->withViewErrors([])->blade('<x-forms.field name="name" label="Name" class="md:col-span-2"><input></x-forms.field>'))
        ->toContain('<div class="md:col-span-2">');

    expect((string) $this->withViewErrors([])->blade('<x-forms.field name="phone" label="Phone" :show-error="false"><input></x-forms.field>'))
        ->not->toContain('class=""');
});

test('field points its label at a different id when asked', function () {
    expect((string) $this->withViewErrors([])->blade('<x-forms.field name="owner_ids" for="owners" label="Owners"><select id="owners"></select></x-forms.field>'))
        ->toContain('<label for="owners" class="field-label">Owners');
});
