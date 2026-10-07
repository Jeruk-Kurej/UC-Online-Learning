<?php

use App\Models\User;

test('public testimony page lists only admin-featured testimonies', function () {
    User::factory()->create([
        'name' => 'Featured Person',
        'testimony' => 'Kuliah di sini sangat membantu usaha saya.',
        'is_visible' => true,
        'is_featured_testimony' => true,
    ]);
    User::factory()->create([
        'name' => 'Unfeatured Person',
        'testimony' => 'Testimoni yang belum dipilih admin.',
        'is_visible' => true,
        'is_featured_testimony' => false,
    ]);

    $response = $this->get('/uc-testimonies');

    $response->assertOk();
    $response->assertSee('Kuliah di sini sangat membantu usaha saya.');
    $response->assertDontSee('Testimoni yang belum dipilih admin.');
});

test('public testimony page hides a featured testimony of a hidden user', function () {
    User::factory()->create([
        'testimony' => 'Testimoni dari profil yang disembunyikan.',
        'is_visible' => false,
        'is_featured_testimony' => true,
    ]);

    $response = $this->get('/uc-testimonies');

    $response->assertOk();
    $response->assertDontSee('Testimoni dari profil yang disembunyikan.');
});
