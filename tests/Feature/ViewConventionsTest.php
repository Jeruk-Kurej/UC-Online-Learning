<?php

use Illuminate\Support\Facades\File;

// --- Blade is markup only: CSS lives in resources/css, JS in resources/js, logic in PHP classes ---

function bladeSources(): array
{
    return collect(File::allFiles(resource_path('views')))
        ->reject(fn ($file) => str_starts_with($file->getRelativePathname(), 'vendor'.DIRECTORY_SEPARATOR))
        ->mapWithKeys(fn ($file) => [$file->getRelativePathname() => $file->getContents()])
        ->all();
}

function viewsMatching(string $pattern): array
{
    return array_keys(array_filter(bladeSources(), fn (string $source) => preg_match($pattern, $source) === 1));
}

test('views have no inline style attributes', function () {
    expect(viewsMatching('/\sstyle\s*=/i'))->toBe([]);
});

test('views have no style blocks', function () {
    expect(viewsMatching('/<style[\s>]/i'))->toBe([]);
});

test('views only load scripts by src or carry JSON data', function () {
    expect(viewsMatching('/<script\b(?![^>]*(\ssrc=|type="application\/json"))[^>]*>/i'))->toBe([]);
});

test('views have no inline event handlers', function () {
    expect(viewsMatching('/\son[a-z]+\s*=\s*["\']/i'))->toBe([]);
});

test('views have no @php blocks', function () {
    expect(viewsMatching('/@php\b/'))->toBe([]);
});

test('views have no HTML comments', function () {
    expect(viewsMatching('/<!--/'))->toBe([]);
});

test('view comments are section headers only', function () {
    expect(viewsMatching('/\{\{--(?! --- [^\n]+ --- --\}\})/'))->toBe([]);
});
