<?php

use App\Models\Collab;
use App\Models\User;

// --- Collab: who answers a request, and what the pair of users looks like from either side ---

test('collab status is the same seen from both users, and a third user sees nothing', function () {
    [$sender, $recipient, $other] = User::factory()->count(3)->create();
    Collab::create(['sender_id' => $sender->id, 'recipient_id' => $recipient->id, 'status' => 'accepted']);

    expect($sender->collabStatusWith($recipient))->toBe('accepted')
        ->and($recipient->collabStatusWith($sender))->toBe('accepted')
        ->and($other->collabStatusWith($sender))->toBeNull()
        ->and($sender->collabStatusWith(null))->toBeNull();
});

test('Collab::between finds the pair in either direction only', function () {
    [$a, $b, $c] = User::factory()->count(3)->create();
    $ab = Collab::create(['sender_id' => $a->id, 'recipient_id' => $b->id, 'status' => 'pending']);
    Collab::create(['sender_id' => $a->id, 'recipient_id' => $c->id, 'status' => 'pending']);

    expect(Collab::between($a->id, $b->id)->pluck('id')->all())->toBe([$ab->id])
        ->and(Collab::between($b->id, $a->id)->pluck('id')->all())->toBe([$ab->id])
        ->and(Collab::between($b->id, $c->id)->exists())->toBeFalse();
});

test('a request that is no longer pending cannot be accepted or rejected again', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();
    $collab = Collab::create(['sender_id' => $sender->id, 'recipient_id' => $recipient->id, 'status' => 'accepted']);

    $this->actingAs($recipient)->post(route('collabs.accept', $collab))->assertSessionHas('error', 'This request is no longer pending.');
    $this->actingAs($recipient)->post(route('collabs.reject', $collab))->assertSessionHas('error', 'This request is no longer pending.');
    $this->assertDatabaseCount('messages', 0);
    expect($collab->fresh()->status)->toBe('accepted');
});

test('only the recipient can reject a request', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();
    $collab = Collab::create(['sender_id' => $sender->id, 'recipient_id' => $recipient->id, 'status' => 'pending']);

    $this->actingAs($sender)->post(route('collabs.reject', $collab))->assertForbidden();
    expect($collab->fresh()->status)->toBe('pending');
});

// --- Testimonies: a testimony counts only when it has text ---

test('User::withTestimony skips users with no or empty testimony', function () {
    $with = User::factory()->create(['testimony' => 'Kuliah di sini seru.']);
    User::factory()->create(['testimony' => null]);
    User::factory()->create(['testimony' => '']);

    expect(User::withTestimony()->pluck('id')->all())->toBe([$with->id]);
});

test('the testimony admin page counts testimonies and featured ones', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['testimony' => 'Satu.', 'is_featured_testimony' => true]);
    User::factory()->create(['testimony' => 'Dua.', 'is_featured_testimony' => false]);
    User::factory()->create(['testimony' => null, 'is_featured_testimony' => true]);

    $this->actingAs($admin)->get(route('uc-testimonies.admin'))
        ->assertOk()
        ->assertViewHas('totalTestimonies', 2)
        ->assertViewHas('featuredTestimonies', 1);
    $this->actingAs($admin)->get(route('uc-testimonies.admin', ['featured' => '1', 'search' => 'Satu']))
        ->assertViewHas('users', fn ($users) => $users->total() === 1);
});
