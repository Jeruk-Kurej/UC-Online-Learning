<?php

// --- Filter bar and admin table components ---

test('search field puts the class on its wrapper and everything else on the input', function () {
    $html = (string) $this->blade('<x-ui.search-field class="flex-1" id="search" value="abc" placeholder="Find..." />');

    expect($html)
        ->toContain('<div class="relative flex-1">')
        ->toMatch('/<input type="search" name="search"[^>]*id="search"[^>]*value="abc"[^>]*placeholder="Find..."/')
        ->toContain('@input="submitDebounced()"');
});

test('search field passes an Alpine-bound placeholder through', function () {
    expect((string) $this->blade('<x-ui.search-field ::placeholder="label" />'))->toContain(':placeholder="label"');
});

test('filter reset watches the loading flag it is given', function () {
    expect((string) $this->blade('<x-ui.filter-reset />'))
        ->toContain('x-show="!isSubmitting"')
        ->toContain('x-show="isSubmitting"');

    expect((string) $this->blade('<x-ui.filter-reset loading="isLoading" class="flex-shrink-0" />'))
        ->toContain('x-show="!isLoading"')
        ->toContain('flex-shrink-0');
});

test('featured toggle is disabled while the record is hidden', function () {
    $html = (string) $this->blade('<x-admin.featured-toggle action="/x" :visible="false" :featured="true" />');

    expect($html)->toContain('disabled')->toContain('Only visible records can be featured')->toContain('aria-pressed="true"')->toContain('>Featured<');
});
