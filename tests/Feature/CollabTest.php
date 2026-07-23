<?php

use App\Models\User;
use App\Models\Collab;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can send collab request', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    $response = $this->actingAs($sender)
        ->post(route('collabs.store', $recipient));

    $response->assertRedirect();
    $this->assertDatabaseHas('collabs', [
        'sender_id' => $sender->id,
        'recipient_id' => $recipient->id,
        'status' => 'pending',
    ]);

    $this->assertDatabaseHas('messages', [
        'sender_id' => $sender->id,
        'recipient_id' => $recipient->id,
        'type' => 'collab_invite',
    ]);
});

test('user cannot collab with themselves', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('collabs.store', $user));

    $response->assertRedirect();
    $this->assertDatabaseEmpty('collabs');
});

test('duplicate collab request is blocked', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    Collab::create([
        'sender_id' => $sender->id,
        'recipient_id' => $recipient->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($sender)
        ->post(route('collabs.store', $recipient));

    $response->assertRedirect();
    expect(Collab::count())->toBe(1);
});

test('rejected collab request can be resent', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    $collab = Collab::create([
        'sender_id' => $sender->id,
        'recipient_id' => $recipient->id,
        'status' => 'rejected',
    ]);

    $response = $this->actingAs($sender)
        ->post(route('collabs.store', $recipient));

    $response->assertRedirect();
    $collab->refresh();
    expect($collab->status)->toBe('pending');
});

test('recipient can accept collab request', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    $collab = Collab::create([
        'sender_id' => $sender->id,
        'recipient_id' => $recipient->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($recipient)
        ->post(route('collabs.accept', $collab));

    $response->assertRedirect();
    $collab->refresh();
    expect($collab->status)->toBe('accepted');

    $this->assertDatabaseHas('messages', [
        'sender_id' => $recipient->id,
        'recipient_id' => $sender->id,
        'type' => 'collab_accepted',
    ]);
});

test('non-recipient cannot accept collab request', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();
    $other = User::factory()->create();

    $collab = Collab::create([
        'sender_id' => $sender->id,
        'recipient_id' => $recipient->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($other)
        ->post(route('collabs.accept', $collab));

    $response->assertStatus(403);
    $collab->refresh();
    expect($collab->status)->toBe('pending');
});

test('recipient can reject collab request', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    $collab = Collab::create([
        'sender_id' => $sender->id,
        'recipient_id' => $recipient->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($recipient)
        ->post(route('collabs.reject', $collab));

    $response->assertRedirect();
    $collab->refresh();
    expect($collab->status)->toBe('rejected');
});
