<?php

use App\Models\Business;
use App\Models\User;

// --- World ---

function sharedBusiness(bool $ownerVisible, array $members): array
{
    $owner = User::factory()->create(['name' => 'Owner', 'is_visible' => $ownerVisible]);
    $business = new Business(['user_id' => $owner->id, 'name' => 'Shared Venture', 'is_visible' => true, 'type' => 'entrepreneur']);
    $business->approval_status = 'approved';
    $business->save();

    $people = [];
    foreach ($members as $i => [$name, $visible]) {
        $people[$name] = User::factory()->create(['name' => $name, 'is_visible' => $visible]);
        $business->members()->attach($people[$name]->id, ['created_at' => now()->addMinutes($i), 'updated_at' => now()]);
    }

    return [$business->fresh(), $owner, $people];
}

// --- Rules ---

test('a business with a hidden owner and no visible co-owner is hidden', function () {
    [$business] = sharedBusiness(false, [['Gone Member', false]]);

    expect(Business::visible()->whereKey($business->id)->exists())->toBeFalse();
    $this->get('/business/'.$business->slug)->assertNotFound();
});

test('a business with a hidden owner stays public through a visible co-owner, shown as owner', function () {
    [$business, $owner, $people] = sharedBusiness(false, [['First Partner', true], ['Second Partner', true]]);

    expect(Business::visible()->whereKey($business->id)->exists())->toBeTrue()
        ->and($business->public_owner->is($people['First Partner']))->toBeTrue()
        ->and($business->public_co_owners->pluck('name')->all())->toBe(['Second Partner']);

    $this->get('/business/'.$business->slug)->assertOk()
        ->assertSee('First Partner')->assertSee('Second Partner')->assertDontSee('>Owner<', false);
});

test('a hidden co-owner disappears while the business and the owner stay', function () {
    [$business, $owner, $people] = sharedBusiness(true, [['Hidden Partner', false], ['Shown Partner', true]]);

    expect($business->public_owner->is($owner))->toBeTrue()
        ->and($business->public_co_owners->pluck('name')->all())->toBe(['Shown Partner']);

    $this->get('/business/'.$business->slug)->assertOk()->assertSee('Shown Partner')->assertDontSee('Hidden Partner');
});

test('directory and cards show the public owner', function () {
    [$business, $owner, $people] = sharedBusiness(false, [['Card Partner', true]]);

    $this->get('/business')->assertOk()->assertSee('Card Partner')->assertDontSee('>Owner<', false);
});

test('work profile cards in the directory keep showing their person', function () {
    $person = User::factory()->create(['name' => 'Employee Person', 'is_visible' => true]);
    $company = \App\Models\Company::create(['user_id' => $person->id, 'name' => 'Some Employer', 'is_visible' => true, 'approval_status' => 'approved']);

    expect($company->public_owner->is($person))->toBeTrue();
    $this->get('/business?view=intrapreneur')->assertOk()->assertSee('Employee Person');
});
