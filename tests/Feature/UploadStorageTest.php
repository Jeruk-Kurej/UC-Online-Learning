<?php

use App\Models\Business;
use App\Models\Category;
use App\Models\Company;
use App\Models\User;
use App\Services\UploadStorage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// --- Form uploads follow UPLOADS_DISK: the public disk by default, Cloudinary where the host's disk does not last ---

function fakeCloudinary(): object
{
    $fake = new class
    {
        public array $uploads = [];

        public function uploadApi(): static
        {
            return $this;
        }

        public function upload($file, array $options): array
        {
            $this->uploads[] = ['exists' => is_file($file), 'options' => $options];

            return ['secure_url' => 'https://res.cloudinary.com/fake/image/upload/v1/'.$options['public_id'].'.png'];
        }
    };
    Cloudinary::swap($fake);
    config(['filesystems.uploads_disk' => 'cloudinary']);
    Storage::fake('public');

    return $fake;
}

test('the public disk is the default and the stored value is a /storage path', function () {
    Storage::fake('public');

    $stored = app(UploadStorage::class)->store(UploadedFile::fake()->image('a.png'), 'logos', 'mine.png');

    expect(config('filesystems.uploads_disk'))->toBe('public')
        ->and($stored)->toBe('/storage/logos/mine.png');
    Storage::disk('public')->assertExists('logos/mine.png');
});

test('without a name the stored file gets the hashed name Laravel gives it', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('a.png');

    $stored = app(UploadStorage::class)->store($file, 'products');

    expect($stored)->toBe('/storage/products/'.$file->hashName());
});

test('with cloudinary the file goes to the upload api, the Cloudinary address is stored and nothing stays on the disk', function () {
    $fake = fakeCloudinary();

    $stored = app(UploadStorage::class)->store(UploadedFile::fake()->image('a.png'), 'company-logos', 'pt-maju_1.png');

    expect($stored)->toBe('https://res.cloudinary.com/fake/image/upload/v1/uco/company-logos/pt-maju_1.png')
        ->and($fake->uploads)->toHaveCount(1)
        ->and($fake->uploads[0]['exists'])->toBeTrue()
        ->and($fake->uploads[0]['options'])->toMatchArray(['public_id' => 'uco/company-logos/pt-maju_1', 'resource_type' => 'auto']);
    expect(Storage::disk('public')->allFiles())->toBe([]);
});

test('the profile photo and activity documents follow the setting', function (bool $cloud) {
    $fake = $cloud ? fakeCloudinary() : null;
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)->patch('/profile', [
        'name' => 'Upload Person',
        'profile_photo' => UploadedFile::fake()->image('me.png'),
        'activities_files' => [UploadedFile::fake()->create('report.pdf', 20, 'application/pdf')],
    ])->assertSessionHasNoErrors()->assertRedirect('/profile');

    $user->refresh();
    $photo = $user->getRawOriginal('profile_photo_url');
    $document = $user->activities_doc_url[0];

    if ($cloud) {
        expect($photo)->toStartWith('https://res.cloudinary.com/')->and($document)->toStartWith('https://res.cloudinary.com/')
            ->and($fake->uploads)->toHaveCount(2)
            ->and(Storage::disk('public')->allFiles())->toBe([]);
    } else {
        expect($photo)->toStartWith('/storage/profile-photos/')->and($document)->toStartWith('/storage/student-activities/');
        Storage::disk('public')->assertExists(str($photo)->after('/storage/')->toString());
        Storage::disk('public')->assertExists(str($document)->after('/storage/')->toString());
    }
})->with(['public disk' => false, 'cloudinary' => true]);

test('a work profile logo and a product photo follow the setting', function (bool $cloud) {
    $fake = $cloud ? fakeCloudinary() : null;
    Storage::fake('public');
    $category = Category::create(['name' => 'Kuliner', 'slug' => 'kuliner']);
    $owner = User::factory()->create();
    $business = Business::create(['user_id' => $owner->id, 'name' => 'Upload Cafe', 'slug' => 'upload-cafe', 'offering_type' => 'both', 'is_visible' => true, 'type' => 'entrepreneur']);

    $this->actingAs($owner)->post(route('intrapreneurs.store'), [
        'name' => 'PT Upload', 'category_id' => $category->id, 'position' => 'Staff', 'job_description' => 'Mengelola data penjualan.',
        'logo_url' => UploadedFile::fake()->image('logo.png'),
    ])->assertSessionHasNoErrors();
    $this->actingAs($owner)->post(route('businesses.products.store', $business), [
        'name' => 'Cake', 'description' => 'A cake', 'price' => 10000, 'price_type' => 'fixed', 'photo' => UploadedFile::fake()->image('cake.png'),
    ])->assertSessionHasNoErrors();

    $logo = Company::where('name', 'PT UPLOAD')->orWhere('name', 'PT Upload')->firstOrFail()->getRawOriginal('logo_url');
    $photo = $business->products()->firstOrFail()->getRawOriginal('photo_url');

    if ($cloud) {
        expect($logo)->toStartWith('https://res.cloudinary.com/')->and($photo)->toStartWith('https://res.cloudinary.com/')
            ->and($fake->uploads)->toHaveCount(2)
            ->and(Storage::disk('public')->allFiles())->toBe([]);
    } else {
        expect($logo)->toStartWith('/storage/company-logos/')->and($photo)->toStartWith('/storage/products/');
        Storage::disk('public')->assertExists(str($photo)->after('/storage/')->toString());
    }
})->with(['public disk' => false, 'cloudinary' => true]);
