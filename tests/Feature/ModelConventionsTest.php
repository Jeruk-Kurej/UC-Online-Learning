<?php

use App\Models\Business;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;

// --- Slugs ---

test('user, business and company get unique slugs from their name', function () {
    $first = User::factory()->create(['name' => 'Same Name']);
    $second = User::factory()->create(['name' => 'Same Name']);
    $third = User::factory()->create(['name' => 'Same Name']);

    expect([$first->slug, $second->slug, $third->slug])->toBe(['same-name', 'same-name-1', 'same-name-2']);

    $a = Business::create(['user_id' => $first->id, 'name' => 'Toko Maju']);
    $b = Business::create(['user_id' => $first->id, 'name' => 'Toko Maju']);
    expect([$a->slug, $b->slug])->toBe(['toko-maju', 'toko-maju-1']);

    $c = Company::create(['user_id' => $first->id, 'name' => 'PT Karya']);
    $d = Company::create(['user_id' => $first->id, 'name' => 'PT Karya']);
    expect([$c->slug, $d->slug])->toBe(['pt-karya', 'pt-karya-1']);
});

test('a given slug is kept and renaming does not change the slug', function () {
    $user = User::factory()->create(['name' => 'Original Name', 'slug' => 'custom-slug']);
    expect($user->slug)->toBe('custom-slug');

    $user->update(['name' => 'Renamed Person']);
    expect($user->fresh()->slug)->toBe('custom-slug');

    $business = Business::create(['user_id' => $user->id, 'name' => 'First Name']);
    $business->update(['name' => 'Second Name']);
    expect($business->fresh()->slug)->toBe('first-name');

    $company = Company::create(['user_id' => $user->id, 'name' => 'First Employer']);
    $company->update(['position' => 'Lead']);
    expect($company->fresh()->slug)->toBe('first-employer');
});

test('models are resolved in routes by slug', function () {
    expect((new User)->getRouteKeyName())->toBe('slug')
        ->and((new Business)->getRouteKeyName())->toBe('slug')
        ->and((new Company)->getRouteKeyName())->toBe('slug');
});

test('slug uses the cleaned business name', function () {
    $user = User::factory()->create();
    $business = Business::create(['user_id' => $user->id, 'name' => 'PT ABADI<br>JAYA']);

    expect($business->slug)->toBe('pt-abadi-jaya');
});

// --- Text cleaning ---

test('business and company names are cleaned and formatted on read', function (string $stored, string $shown) {
    $user = User::factory()->create();
    $business = Business::create(['user_id' => $user->id, 'name' => $stored]);
    $company = Company::create(['user_id' => $user->id, 'name' => $stored]);

    expect($business->fresh()->name)->toBe($shown)
        ->and($company->fresh()->name)->toBe($shown)
        ->and($business->fresh()->getRawOriginal('name'))->toBe($stored);
})->with([
    'uppercase company' => ['PT ABADI JAYA', 'PT Abadi Jaya'],
    'line break and tags' => ['Toko <b>Maju</b><br>Bersama', 'Toko Maju Bersama'],
    'cv and tbk' => ['CV SUMBER REJEKI TBK', 'CV Sumber Rejeki Tbk'],
    'mixed case is left alone' => ['Kopi Kenangan pt', 'Kopi Kenangan PT'],
    'surrounding spaces' => ['  Warung Sederhana  ', 'Warung Sederhana'],
]);

test('descriptions, job descriptions, product text and testimonies lose html on read', function () {
    $user = User::factory()->create(['testimony' => 'Sangat <i>membantu</i><br/>sekali ']);
    $business = Business::create(['user_id' => $user->id, 'name' => 'Biz', 'description' => '<p>Jual <b>kopi</b></p><br>enak']);
    $company = Company::create(['user_id' => $user->id, 'name' => 'Emp', 'job_description' => 'Mengelola<br>tim <u>sales</u>']);
    $product = Product::create(['business_id' => $business->id, 'name' => '<b>Kopi</b> Susu', 'description' => 'Manis<br>dingin']);

    expect($user->fresh()->testimony)->toBe('Sangat membantu sekali')
        ->and($business->fresh()->description)->toBe('Jual kopi enak')
        ->and($company->fresh()->job_description)->toBe('Mengelola tim sales')
        ->and($product->fresh()->name)->toBe('Kopi Susu')
        ->and($product->fresh()->description)->toBe('Manis dingin');
});

test('empty text reads as an empty string', function () {
    $user = User::factory()->create(['testimony' => null]);
    $business = Business::create(['user_id' => $user->id, 'name' => 'Biz', 'description' => null]);
    $company = Company::create(['user_id' => $user->id, 'name' => 'Emp', 'job_description' => null]);
    $product = Product::create(['business_id' => $business->id, 'name' => 'Item', 'description' => null]);

    expect($user->fresh()->testimony)->toBe('')
        ->and($business->fresh()->description)->toBe('')
        ->and($company->fresh()->job_description)->toBe('')
        ->and($product->fresh()->description)->toBe('');
});

test('cleaned text is what toArray returns', function () {
    $user = User::factory()->create();
    $business = Business::create(['user_id' => $user->id, 'name' => 'PT ABADI JAYA', 'description' => '<p>Halo</p>']);

    expect($business->fresh()->toArray())->toMatchArray(['name' => 'PT Abadi Jaya', 'description' => 'Halo']);
});

// --- Approval status ---

function approvable(string $kind): string
{
    return $kind === 'business' ? Business::class : Company::class;
}

test('status and label follow approval_status, falling back to visibility', function (string $kind, ?string $approval, bool $visible, string $status, string $label) {
    $model = approvable($kind);
    $user = User::factory()->create();
    $record = new $model(['user_id' => $user->id, 'name' => 'Record', 'is_visible' => $visible]);
    $record->approval_status = $approval;

    expect($record->status)->toBe($status)->and($record->status_label)->toBe($label);
})->with(['business', 'company'])->with([
    'approved' => ['approved', true, 'approved', 'Approved'],
    'pending' => ['pending', true, 'pending', 'Pending Approval'],
    'rejected' => ['rejected', false, 'rejected', 'Rejected'],
    'need revision' => ['need_revision', false, 'need_revision', 'Need Revision'],
    'unknown value' => ['archived', true, 'archived', 'Pending Approval'],
    'no status, visible' => [null, true, 'approved', 'Approved'],
    'no status, hidden' => [null, false, 'pending', 'Pending Approval'],
]);

test('visible scope needs approval, visibility and a visible owner', function (string $kind) {
    $model = approvable($kind);
    $shown = User::factory()->create(['is_visible' => true]);
    $hidden = User::factory()->create(['is_visible' => false]);

    $make = fn (string $name, User $owner, bool $visible, string $approval) => tap(
        new $model(['user_id' => $owner->id, 'name' => $name, 'is_visible' => $visible]),
        function ($record) use ($approval) {
            $record->approval_status = $approval;
            $record->save();
        }
    );

    $make('Shown', $shown, true, 'approved');
    $make('Pending', $shown, true, 'pending');
    $make('Hidden', $shown, false, 'approved');
    $make('Hidden Owner', $hidden, true, 'approved');

    expect($model::visible()->pluck('name')->all())->toBe(['Shown']);
})->with(['business', 'company']);
