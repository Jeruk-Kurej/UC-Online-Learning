<?php

use App\Models\Business;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

// --- What a student manages alone: business details, skills, certificates, caption, product photos, deleting their own ventures ---

function manageBusiness(User $owner): Business
{
    $business = new Business(['user_id' => $owner->id, 'name' => 'Manage Venture', 'description' => 'Sells things.', 'category_id' => Category::firstOrCreate(['slug' => 'jasa'], ['name' => 'Jasa'])->id, 'offering_type' => 'both', 'is_visible' => true, 'type' => 'entrepreneur']);
    $business->approval_status = 'approved';
    $business->save();

    return $business;
}

test('the owner fills in the business details the page shows; the revenue stays private', function () {
    $owner = User::factory()->create();
    $business = manageBusiness($owner);

    $this->actingAs($owner)->put(route('businesses.update', $business), [
        'name' => 'Manage Venture', 'description' => 'Sells things.', 'category_id' => $business->category_id, 'business_mode' => 'both',
        'employee_count' => 7, 'customer_base_size' => 250, 'target_market' => 'Students in Surabaya', 'business_scale' => 'Micro',
        'business_legality' => 'NIB', 'product_legality' => 'PIRT', 'revenue_range' => 'Rp 10 to 50 million a month',
    ])->assertSessionHasNoErrors();

    expect($business->fresh()->only(['employee_count', 'customer_base_size', 'target_market', 'business_scale', 'business_legality', 'product_legality', 'revenue_range']))
        ->toEqual(['employee_count' => 7, 'customer_base_size' => 250, 'target_market' => 'Students in Surabaya', 'business_scale' => 'Micro', 'business_legality' => 'NIB', 'product_legality' => 'PIRT', 'revenue_range' => 'Rp 10 to 50 million a month']);

    $this->get(route('businesses.show', $business))->assertSee('7 people')->assertSee('Students in Surabaya')->assertSee('PIRT')->assertSee('Rp 10 to 50 million a month');
    auth()->logout();
    $this->get(route('businesses.show', $business))->assertSee('NIB')->assertDontSee('Rp 10 to 50 million a month');

    $this->actingAs($owner)->get(route('businesses.edit', $business))->assertSee('name="business_legality"', false)->assertSee('value="NIB"', false);
});

test('skills are saved from one comma-separated field, reusing existing skills by name', function () {
    $user = User::factory()->create();
    $existing = Skill::create(['name' => 'Marketing']);
    $user->skills()->attach($existing);

    $this->actingAs($user)->patch(route('profile.update'), ['name' => $user->name, 'skills' => 'Marketing, Excel,  , excel; Public speaking, '.str_repeat('x', 101)])->assertSessionHasNoErrors();

    expect($user->fresh()->skills->pluck('name')->sort()->values()->all())->toBe(['Excel', 'Marketing', 'Public speaking'])
        ->and(Skill::where('name', 'Marketing')->count())->toBe(1);
    $this->actingAs($user)->get(route('profile.edit'))->assertSee('value="Marketing, Excel, Public speaking"', false);

    $this->actingAs($user)->patch(route('profile.update'), ['name' => $user->name, 'skills' => ''])->assertSessionHasNoErrors();
    expect($user->fresh()->skills)->toHaveCount(0);

    $user->skills()->attach($existing);
    $this->actingAs($user)->patch(route('profile.update'), ['name' => $user->name])->assertSessionHasNoErrors();
    expect($user->fresh()->skills)->toHaveCount(1);
});

test('certificates and the activity caption are managed on the profile', function () {
    Storage::fake('public');
    $user = User::factory()->create(['expertise_certification_url' => 'https://example.test/old.pdf, https://example.test/keep.png']);

    $this->actingAs($user)->patch(route('profile.update'), [
        'name' => $user->name,
        'activities_caption' => 'Speaker at UC Startup Day',
        'certification_files' => [UploadedFile::fake()->image('new.png')],
        'delete_certification_files' => ['https://example.test/old.pdf'],
    ])->assertSessionHasNoErrors();

    $user->refresh();
    expect($user->activities_caption)->toBe('Speaker at UC Startup Day')
        ->and($user->certification_urls)->toHaveCount(2)
        ->and($user->certification_urls[0])->toBe('https://example.test/keep.png')
        ->and($user->certification_urls[1])->toStartWith('/storage/student-certificates/cert_'.$user->id.'_')
        ->and($user->getRawOriginal('expertise_certification_url'))->toContain(';');
    Storage::disk('public')->assertExists(str_replace('/storage/', '', $user->certification_urls[1]));

    $form = $this->actingAs($user)->get(route('profile.edit'))->getContent();
    expect($form)->toContain('name="certification_files[]"', 'name="delete_certification_files[]"', 'name="activities_caption"', 'keep.png');
});

test('a product photo can be removed; a service keeps its photo', function () {
    $owner = User::factory()->create();
    $business = manageBusiness($owner);
    $product = Product::create(['business_id' => $business->id, 'type' => 'product', 'name' => 'Mug', 'description' => 'A mug', 'price_type' => 'fixed', 'price' => 1000, 'photo_url' => 'https://example.test/mug.png']);
    $service = Product::create(['business_id' => $business->id, 'type' => 'service', 'name' => 'Repair', 'description' => 'Fixes', 'price_type' => 'negotiable', 'photo_url' => 'https://example.test/repair.png']);

    $this->actingAs($owner)->put(route('businesses.products.update', [$business, $product]), ['name' => 'Mug', 'description' => 'A mug', 'price_type' => 'fixed', 'price' => 1000, 'delete_photo' => 1])->assertRedirect();
    $this->actingAs($owner)->put(route('businesses.services.update', [$business, $service]), ['name' => 'Repair', 'description' => 'Fixes', 'price_type' => 'negotiable', 'delete_photo' => 1])->assertRedirect();

    expect($product->fresh()->getRawOriginal('photo_url'))->toBeNull()
        ->and($service->fresh()->getRawOriginal('photo_url'))->toBe('https://example.test/repair.png');

    expect($this->actingAs($owner)->get(route('businesses.products.edit', [$business, $product]))->getContent())->toContain('Remove photo')
        ->and($this->actingAs($owner)->get(route('businesses.services.edit', [$business, $service]))->getContent())->not->toContain('Remove photo');
});

test('the owner or an admin deletes a business with its offerings; a co-owner or a stranger cannot', function () {
    $owner = User::factory()->create();
    $coOwner = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $business = manageBusiness($owner);
    $business->members()->attach($coOwner);
    Product::create(['business_id' => $business->id, 'type' => 'product', 'name' => 'Mug', 'description' => 'A mug', 'price_type' => 'fixed', 'price' => 1000]);

    expect($this->actingAs($owner)->get(route('businesses.edit', $business))->getContent())->toContain('Delete business')
        ->and($this->actingAs($coOwner)->get(route('businesses.edit', $business))->getContent())->not->toContain('Delete business');

    $this->actingAs($coOwner)->delete(route('businesses.destroy', $business))->assertForbidden();
    $this->actingAs(User::factory()->create())->delete(route('businesses.destroy', $business))->assertForbidden();

    $this->actingAs($owner)->delete(route('businesses.destroy', $business))
        ->assertRedirect(route('businesses.my'))->assertSessionHas('success', '"Manage Venture" was deleted.');
    expect(Business::find($business->id))->toBeNull()->and(Product::where('business_id', $business->id)->count())->toBe(0);

    $other = manageBusiness($owner);
    $this->actingAs($admin)->delete(route('businesses.destroy', $other))->assertRedirect(route('businesses.admin'));
    expect(Business::find($other->id))->toBeNull();
});

test('the owner or an admin deletes a work profile; a stranger cannot', function () {
    $owner = User::factory()->create();
    $make = fn () => Company::create(['user_id' => $owner->id, 'name' => 'Gone Corp', 'position' => 'Lead', 'job_description' => 'Leads the team.', 'approval_status' => 'approved', 'is_visible' => true]);

    $company = $make();
    expect($this->actingAs($owner)->get(route('intrapreneurs.edit', $company))->getContent())->toContain('Delete work profile');
    $this->actingAs(User::factory()->create())->delete(route('intrapreneurs.destroy', $company))->assertForbidden();
    $this->actingAs($owner)->delete(route('intrapreneurs.destroy', $company))->assertRedirect(route('businesses.my'))->assertSessionHas('success', '"Gone Corp" was deleted.');
    expect(Company::find($company->id))->toBeNull();

    $second = $make();
    $this->actingAs(User::factory()->admin()->create())->delete(route('intrapreneurs.destroy', $second))->assertRedirect(route('businesses.admin'));
    expect(Company::find($second->id))->toBeNull();
});

test('the edit forms pre-select the saved city, whatever its case, so saving does not wipe it', function () {
    foreach (['villages', 'districts', 'regencies', 'provinces'] as $table) {
        Schema::dropIfExists($table);
    }
    Schema::create('provinces', fn ($t) => [$t->char('id', 2)->primary(), $t->string('name')]);
    Schema::create('regencies', fn ($t) => [$t->char('id', 4)->primary(), $t->char('province_id', 2), $t->string('name')]);
    DB::table('provinces')->insert(['id' => '35', 'name' => 'JAWA TIMUR']);
    DB::table('regencies')->insert([['id' => '3578', 'province_id' => '35', 'name' => 'KOTA SURABAYA'], ['id' => '3573', 'province_id' => '35', 'name' => 'KOTA MALANG']]);

    $owner = User::factory()->create();
    $company = Company::create(['user_id' => $owner->id, 'name' => 'City Corp', 'position' => 'Lead', 'job_description' => 'Leads the team.', 'province' => 'JAWA TIMUR', 'city' => 'Kota Surabaya', 'approval_status' => 'approved', 'is_visible' => true]);
    $business = manageBusiness($owner);
    $business->update(['province' => 'JAWA TIMUR', 'city' => 'KOTA SURABAYA']);

    $selectedCity = fn (string $html) => preg_match('/<option value="(\d+)" selected>\s*Kota Surabaya/', $html) === 1;

    expect($selectedCity($this->actingAs($owner)->get(route('intrapreneurs.edit', $company))->getContent()))->toBeTrue()
        ->and($selectedCity($this->actingAs($owner)->get(route('businesses.edit', $business))->getContent()))->toBeTrue();

    $this->actingAs($owner)->put(route('intrapreneurs.update', $company), ['name' => 'City Corp', 'position' => 'Lead', 'job_description' => 'Leads the team.', 'category_id' => Category::firstOrCreate(['slug' => 'jasa'], ['name' => 'Jasa'])->id, 'province' => '35', 'city' => '3578'])->assertSessionHasNoErrors();
    expect($company->fresh()->city)->toBe('Kota Surabaya');

    $afterError = $this->actingAs($owner)->withSession(['_old_input' => ['province' => '35', 'city' => '3573']])->get(route('intrapreneurs.edit', $company))->getContent();
    expect(preg_match('/<option value="3573" selected>/', $afterError))->toBe(1);
});
