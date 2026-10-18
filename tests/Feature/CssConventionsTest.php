<?php

use Illuminate\Support\Facades\File;

// --- CSS comments are section headers only: /* --- Name --- */ ---

test('css comments are section headers only', function () {
    $offenders = collect(File::allFiles(resource_path('css')))
        ->filter(fn ($file) => preg_match('/\/\*(?! --- [^\n]+ --- \*\/)/', $file->getContents()) === 1)
        ->map(fn ($file) => $file->getRelativePathname())
        ->values()
        ->all();

    expect($offenders)->toBe([]);
});

test('css section headers stand on their own line', function () {
    $offenders = collect(File::allFiles(resource_path('css')))
        ->filter(fn ($file) => preg_match('/[;{}][ \t]+\/\* --- /', $file->getContents()) === 1)
        ->map(fn ($file) => $file->getRelativePathname())
        ->values()
        ->all();

    expect($offenders)->toBe([]);
});
