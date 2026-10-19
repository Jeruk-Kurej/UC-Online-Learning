<?php

use Illuminate\Support\Facades\Blade;

// --- Thumbnails load lazily and are marked so the page script can show the grey loading state ---

test('a thumbnail with a picture is lazy, marked for the loading state and keeps its letter behind it', function () {
    $html = Blade::render('<x-ui.thumb src="/images/example.png" name="budi" />');

    expect($html)->toContain('data-thumb', 'loading="lazy"', 'decoding="async"')
        ->and($html)->toContain('>B</span>');
});

test('a thumbnail without a picture stays the orange letter and has no image to wait for', function () {
    $html = Blade::render('<x-ui.thumb name="budi" />');

    expect($html)->not->toContain('<img')->and($html)->toContain('>B</span>');
});

test('the page script registers the loading state and the style for it exists', function () {
    expect(file_get_contents(resource_path('js/app.js')))->toContain('registerImageLoading()')
        ->and(file_get_contents(resource_path('css/components/ui.css')))->toContain('img[data-thumb][data-loading]');
});
