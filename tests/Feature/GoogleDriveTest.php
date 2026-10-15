<?php

use App\Support\GoogleDrive;

// --- Google Drive share links ---

test('drive and docs links are recognised', function () {
    expect(GoogleDrive::isDriveUrl('https://drive.google.com/file/d/x/view'))->toBeTrue()
        ->and(GoogleDrive::isDriveUrl('https://docs.google.com/file/d/x/edit'))->toBeTrue()
        ->and(GoogleDrive::isDriveUrl('https://example.com/a.png'))->toBeFalse();
});

test('file id is read from the open and file/d formats', function () {
    $id = '1AbCdEfGhIjKlMnOpQrStUvWxYz012345';

    expect(GoogleDrive::fileId("https://drive.google.com/open?id={$id}"))->toBe($id)
        ->and(GoogleDrive::fileId("https://drive.google.com/file/d/{$id}/view"))->toBe($id)
        ->and(GoogleDrive::fileId('https://drive.google.com/file/d/short/view'))->toBeNull()
        ->and(GoogleDrive::fileId('https://drive.google.com/file/d/short/view', 1))->toBe('short');
});

test('drive addresses', function () {
    expect(GoogleDrive::thumbnailUrl('abc', 'w400'))->toBe('https://drive.google.com/thumbnail?id=abc&sz=w400')
        ->and(GoogleDrive::viewUrl('abc'))->toBe('https://drive.google.com/uc?export=view&id=abc')
        ->and(GoogleDrive::previewUrl('abc'))->toBe('https://drive.google.com/file/d/abc/preview')
        ->and(GoogleDrive::downloadUrl('abc'))->toBe('https://drive.google.com/uc?export=download&confirm=t&id=abc');
});
