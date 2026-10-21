<?php

// --- Image addresses: complete ones are used as they are, stored paths go through the disk ---

test('a complete address is returned unchanged whatever the disk', function () {
    foreach (['public', 'cloudinary'] as $disk) {
        config(['filesystems.default' => $disk]);

        expect(storage_image_url('https://ui-avatars.com/api/?name=A', 'logo_thumb'))->toBe('https://ui-avatars.com/api/?name=A')
            ->and(storage_image_url('http://localhost/images/logo.png', ['width' => 150]))->toBe('http://localhost/images/logo.png')
            ->and(storage_image_url('data:image/png;base64,AAAA'))->toBe('data:image/png;base64,AAAA');
    }
});

test('a stored path on the public disk becomes a /storage address, an empty one stays empty', function () {
    config(['filesystems.default' => 'public']);

    expect(storage_image_url('logos/a.png'))->toEndWith('/storage/logos/a.png')
        ->and(storage_image_url(null))->toBeNull()
        ->and(storage_image_url(''))->toBeNull();
});
