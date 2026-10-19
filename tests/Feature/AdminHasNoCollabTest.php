<?php

use App\Models\Business;
use App\Models\Collab;
use App\Models\User;

// --- Admin accounts are staff: no inbox, no collab requests in either direction ---

test('an admin has no inbox and no bell', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('inbox.index'))->assertForbidden();
    $this->actingAs($admin)->get(route('home'))->assertOk()->assertDontSee(route('inbox.index'), false);
});

test('students still see the bell', function () {
    $this->actingAs(User::factory()->create())->get(route('home'))->assertOk()->assertSee(route('inbox.index'), false);
});

test('an admin cannot send a collab request, and nobody can send one to an admin', function () {
    $admin = User::factory()->admin()->create();
    $student = User::factory()->create();

    $this->actingAs($admin)->post(route('collabs.store', $student))->assertForbidden();
    $this->actingAs($student)->post(route('collabs.store', $admin))->assertForbidden();

    expect(Collab::count())->toBe(0);
});

test('students can still send a collab request to each other', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    $this->actingAs($sender)->post(route('collabs.store', $recipient))->assertSessionHas('success');

    expect(Collab::count())->toBe(1);
});

test('the collab buttons are not shown to an admin, nor on the page of an admin', function () {
    $admin = User::factory()->admin()->create(['is_visible' => true]);
    $student = User::factory()->create(['is_visible' => true]);
    $business = new Business(['user_id' => $student->id, 'name' => 'Some Venture', 'is_visible' => true, 'type' => 'entrepreneur']);
    $business->approval_status = 'approved';
    $business->save();

    $this->actingAs($admin)->get(route('users.show', $student))->assertOk()->assertDontSee('Send a collab request');
    $this->actingAs($admin)->get(route('businesses.show', $business))->assertOk()->assertDontSee('Collab with the owner');

    $other = User::factory()->create(['is_visible' => true]);
    $this->actingAs($other)->get(route('users.show', $student))->assertOk()->assertSee('Send a collab request');
    $this->actingAs($other)->get(route('users.show', $admin))->assertDontSee('Send a collab request');
});
