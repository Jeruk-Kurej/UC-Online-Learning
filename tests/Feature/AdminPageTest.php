<?php

use App\Models\User;
use App\Models\Page;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can view page edit screen', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $page = Page::create([
        'slug' => 'about',
        'title' => 'About Us',
        'content_json' => ['body' => 'Welcome to UCO'],
    ]);

    $response = $this->actingAs($admin)
        ->get(route('pages.edit', $page));

    $response->assertStatus(200);
});

test('non-admin cannot view page edit screen', function () {
    $user = User::factory()->create(['role' => 'user']);
    $page = Page::create([
        'slug' => 'about',
        'title' => 'About Us',
        'content_json' => ['body' => 'Welcome to UCO'],
    ]);

    $response = $this->actingAs($user)
        ->get(route('pages.edit', $page));

    $response->assertStatus(403);
});

test('admin can update page content', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $page = Page::create([
        'slug' => 'about',
        'title' => 'About Us',
        'content_json' => ['body' => 'Welcome to UCO'],
    ]);

    $response = $this->actingAs($admin)
        ->put(route('pages.update', $page), [
            'title' => 'New About Us Title',
            'content_json' => ['body' => 'Updated Welcome Content'],
        ]);

    $response->assertStatus(200);
    $page->refresh();
    expect($page->title)->toBe('New About Us Title');
    expect($page->content_json['body'])->toBe('Updated Welcome Content');
});

test('non-admin cannot update page content', function () {
    $user = User::factory()->create(['role' => 'user']);
    $page = Page::create([
        'slug' => 'about',
        'title' => 'About Us',
        'content_json' => ['body' => 'Welcome to UCO'],
    ]);

    $response = $this->actingAs($user)
        ->put(route('pages.update', $page), [
            'title' => 'Hack title',
            'content_json' => ['body' => 'Hack body'],
        ]);

    $response->assertStatus(403);
});

test('admin can upload image to page storage', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    $imageFile = UploadedFile::fake()->image('page-image.jpg');

    $response = $this->actingAs($admin)
        ->post(route('pages.upload-image'), [
            'image' => $imageFile,
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'file' => ['url'],
    ]);

    $json = $response->json();
    $url = $json['file']['url'];
    // Parse path relative to storage from asset URL
    $path = 'pages/' . $imageFile->hashName();
    Storage::disk('public')->assertExists($path);
});

test('non-admin cannot upload image to page storage', function () {
    Storage::fake('public');
    $user = User::factory()->create(['role' => 'user']);
    $imageFile = UploadedFile::fake()->image('page-image.jpg');

    $response = $this->actingAs($user)
        ->post(route('pages.upload-image'), [
            'image' => $imageFile,
        ]);

    $response->assertStatus(403);
});
