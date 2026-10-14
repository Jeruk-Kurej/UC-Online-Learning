<?php

use App\Models\Business;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// --- World ---

function offeringWorld(string $mode = 'both'): array
{
    Storage::fake('public');

    $owner = User::factory()->create();
    $business = Business::create([
        'user_id' => $owner->id,
        'name' => 'Offering Venture',
        'offering_type' => $mode,
        'approval_status' => 'approved',
        'is_visible' => true,
    ]);
    $product = Product::create(['business_id' => $business->id, 'type' => 'product', 'name' => 'Kopi', 'description' => 'Kopi susu']);
    $service = Product::create(['business_id' => $business->id, 'type' => 'service', 'name' => 'Desain', 'description' => 'Desain logo']);

    return compact('owner', 'business', 'product', 'service');
}

function offeringPayload(array $overrides = []): array
{
    return $overrides + ['name' => 'Baru', 'description' => 'Deskripsi baru', 'price_type' => 'fixed', 'price' => 15000];
}

// --- Mode checks ---

test('create form redirects with an error when the business does not offer that type', function (string $mode, string $route, string $error) {
    ['owner' => $owner, 'business' => $business] = offeringWorld($mode);

    $this->actingAs($owner)->get(route($route, $business))
        ->assertRedirect(route('businesses.show', $business))
        ->assertSessionHasErrors(['business_mode' => $error]);
})->with([
    'product form on a service business' => ['service', 'businesses.products.create', 'This business is not in Product mode.'],
    'service form on a product business' => ['product', 'businesses.services.create', 'This business is not in Service mode.'],
]);

test('create forms open when the business offers that type', function () {
    ['owner' => $owner, 'business' => $business] = offeringWorld('both');

    $this->actingAs($owner)->get(route('businesses.products.create', $business))->assertOk()->assertViewIs('products.create');
    $this->actingAs($owner)->get(route('businesses.services.create', $business))->assertOk()->assertViewIs('services.create');
});

// --- Edit views ---

test('edit forms receive the offering under its own variable name', function () {
    ['owner' => $owner, 'business' => $business, 'product' => $product, 'service' => $service] = offeringWorld();

    $this->actingAs($owner)->get(route('businesses.products.edit', [$business, $product]))
        ->assertOk()->assertViewIs('products.edit')->assertViewHas('product', fn ($p) => $p->is($product));
    $this->actingAs($owner)->get(route('businesses.services.edit', [$business, $service]))
        ->assertOk()->assertViewIs('services.edit')->assertViewHas('service', fn ($s) => $s->is($service));
});

// --- Type and ownership guards ---

test('a product cannot be reached through the service routes and the other way round', function () {
    ['owner' => $owner, 'business' => $business, 'product' => $product, 'service' => $service] = offeringWorld();

    $this->actingAs($owner)->get(route('businesses.services.edit', [$business, $product]))->assertNotFound();
    $this->actingAs($owner)->put(route('businesses.services.update', [$business, $product]), offeringPayload())->assertNotFound();
    $this->actingAs($owner)->delete(route('businesses.services.destroy', [$business, $product]))->assertNotFound();
    $this->actingAs($owner)->get(route('businesses.products.edit', [$business, $service]))->assertNotFound();
    $this->actingAs($owner)->put(route('businesses.products.update', [$business, $service]), offeringPayload())->assertNotFound();
    $this->actingAs($owner)->delete(route('businesses.products.destroy', [$business, $service]))->assertNotFound();
});

test('an offering of another business answers 404 for its owner', function () {
    ['owner' => $owner, 'business' => $business] = offeringWorld();
    $other = offeringWorld();

    $this->actingAs($owner)->get(route('businesses.products.edit', [$business, $other['product']]))->assertNotFound();
});

test('a non-owner gets 403 before validation or type checks', function () {
    ['business' => $business, 'product' => $product, 'service' => $service] = offeringWorld();
    $stranger = User::factory()->create();

    $this->actingAs($stranger)->post(route('businesses.products.store', $business), [])->assertForbidden();
    $this->actingAs($stranger)->put(route('businesses.services.update', [$business, $product]), [])->assertForbidden();
    $this->actingAs($stranger)->get(route('businesses.products.edit', [$business, $service]))->assertForbidden();
});

test('a team member and an admin can manage offerings', function () {
    ['business' => $business, 'product' => $product] = offeringWorld();
    $member = User::factory()->create();
    $business->members()->attach($member->id);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($member)->get(route('businesses.products.edit', [$business, $product]))->assertOk();
    $this->actingAs($admin)->get(route('businesses.products.edit', [$business, $product]))->assertOk();
});

// --- Validation ---

test('a service needs a photo when created, a product does not', function () {
    ['owner' => $owner, 'business' => $business] = offeringWorld();

    $this->actingAs($owner)->post(route('businesses.services.store', $business), offeringPayload())
        ->assertSessionHasErrors('photo');
    $this->actingAs($owner)->post(route('businesses.products.store', $business), offeringPayload())
        ->assertSessionHasNoErrors();
});

test('updating a service does not require a new photo', function () {
    ['owner' => $owner, 'business' => $business, 'service' => $service] = offeringWorld();

    $this->actingAs($owner)->put(route('businesses.services.update', [$business, $service]), offeringPayload(['name' => 'Desain Ulang']))
        ->assertSessionHasNoErrors();
    expect($service->fresh()->name)->toBe('Desain Ulang');
});

test('validation rules are the same for both types', function (string $store) {
    ['owner' => $owner, 'business' => $business] = offeringWorld();
    $photo = UploadedFile::fake()->image('a.jpg');

    $this->actingAs($owner)->post(route($store, $business), ['photo' => $photo])
        ->assertSessionHasErrors(['name', 'description', 'price_type']);
    $this->actingAs($owner)->post(route($store, $business), offeringPayload(['price' => null, 'photo' => $photo]))
        ->assertSessionHasErrors('price');
    $this->actingAs($owner)->post(route($store, $business), offeringPayload(['price_type' => 'negotiable', 'price' => -1, 'photo' => $photo]))
        ->assertSessionHasErrors('price');
    $this->actingAs($owner)->post(route($store, $business), offeringPayload(['price_type' => 'customize', 'price' => null, 'photo' => $photo]))
        ->assertSessionHasNoErrors();
    $this->actingAs($owner)->post(route($store, $business), offeringPayload(['price_type' => 'free', 'photo' => $photo]))
        ->assertSessionHasErrors('price_type');
})->with(['businesses.products.store', 'businesses.services.store']);

// --- Results ---

test('store saves the type, the photo folder and shows the right message', function (string $route, string $type, string $folder, string $word) {
    ['owner' => $owner, 'business' => $business] = offeringWorld();

    $this->actingAs($owner)->post(route($route, $business), offeringPayload([
        'name' => 'Item Baru',
        'photo' => UploadedFile::fake()->image('item.jpg'),
        'photo_caption' => 'Caption',
    ]))
        ->assertRedirect(route('businesses.show', $business))
        ->assertSessionHas('success', "Success! The {$word} 'Item Baru' has been added.");

    $saved = Product::where('name', 'Item Baru')->firstOrFail();
    expect($saved->type)->toBe($type)
        ->and($saved->business_id)->toBe($business->id)
        ->and($saved->photo_caption)->toBe('Caption')
        ->and($saved->getRawOriginal('photo_url'))->toStartWith($folder.'/');
    Storage::disk('public')->assertExists($saved->getRawOriginal('photo_url'));
})->with([
    'product' => ['businesses.products.store', 'product', 'products', 'product'],
    'service' => ['businesses.services.store', 'service', 'services', 'service'],
]);

test('update and destroy show the right messages', function (string $kind) {
    $world = offeringWorld();
    $item = $world[$kind];
    $base = $kind === 'product' ? 'businesses.products' : 'businesses.services';

    $this->actingAs($world['owner'])->put(route("{$base}.update", [$world['business'], $item]), offeringPayload(['name' => 'Ganti']))
        ->assertSessionHas('success', "Success! The {$kind} 'Ganti' has been updated.");
    $this->actingAs($world['owner'])->delete(route("{$base}.destroy", [$world['business'], $item]))
        ->assertRedirect(route('businesses.show', $world['business']))
        ->assertSessionHas('success', "Success! The {$kind} 'Ganti' has been removed.");
    expect(Product::find($item->id))->toBeNull();
})->with(['product', 'service']);

test('a new photo replaces the stored path on update', function (string $kind, string $folder) {
    $world = offeringWorld();
    $base = $kind === 'product' ? 'businesses.products' : 'businesses.services';

    $this->actingAs($world['owner'])->put(route("{$base}.update", [$world['business'], $world[$kind]]), offeringPayload([
        'photo' => UploadedFile::fake()->image('new.jpg'),
    ]))->assertSessionHasNoErrors();

    expect($world[$kind]->fresh()->getRawOriginal('photo_url'))->toStartWith($folder.'/');
})->with([['product', 'products'], ['service', 'services']]);
