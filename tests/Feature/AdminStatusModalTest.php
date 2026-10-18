<?php

use App\Models\Business;
use App\Models\Category;
use App\Models\Company;
use App\Models\User;

// --- The admin lists hand the status modal the address to post to; routes bind by slug, so an id in the path is a 404 ---

function statusModalData(string $html): array
{
    preg_match_all('/data-status-modal="([^"]+)"/', $html, $matches);

    return array_map(fn (string $raw) => json_decode(html_entity_decode($raw), true), $matches[1]);
}

function adminListWorld(): array
{
    $category = Category::create(['name' => 'Kuliner', 'slug' => 'kuliner']);
    $owner = User::factory()->create();

    return [
        'admin' => User::factory()->admin()->create(),
        'business' => Business::create(['user_id' => $owner->id, 'name' => 'Modal Biz', 'slug' => 'modal-biz', 'category_id' => $category->id, 'type' => 'entrepreneur', 'approval_status' => 'pending', 'is_visible' => false]),
        'company' => Company::create(['user_id' => $owner->id, 'name' => 'Modal Corp', 'slug' => 'modal-corp', 'category_id' => $category->id, 'approval_status' => 'pending', 'is_visible' => false]),
    ];
}

test('the business list gives the modal the status address of each business by slug', function () {
    $w = adminListWorld();

    $html = $this->actingAs($w['admin'])->get(route('businesses.admin'))->assertOk()->getContent();
    $modals = statusModalData($html);

    expect($modals)->toHaveCount(1)
        ->and($modals[0]['url'] ?? null)->toBe(route('businesses.update-status', $w['business'], false));
});

test('the work profile list gives the modal the status address of each work profile by slug', function () {
    $w = adminListWorld();

    $html = $this->actingAs($w['admin'])->get(route('businesses.admin', ['type' => 'intrapreneur']))->assertOk()->getContent();
    $modals = statusModalData($html);

    expect($modals)->toHaveCount(1)
        ->and($modals[0]['url'] ?? null)->toBe(route('intrapreneurs.update-status', $w['company'], false));
});

test('posting to the address the modal received changes the status', function () {
    $w = adminListWorld();

    foreach ([[null, $w['business']], [['type' => 'intrapreneur'], $w['company']]] as [$query, $record]) {
        $modal = statusModalData($this->actingAs($w['admin'])->get(route('businesses.admin', $query ?? []))->getContent())[0];

        $this->actingAs($w['admin'])->post($modal['url'], ['status' => 'rejected', 'rejection_reason' => 'Fix the logo'])->assertRedirect();
        expect($record->fresh()->approval_status)->toBe('rejected');

        $this->actingAs($w['admin'])->post($modal['url'], ['status' => 'approved'])->assertRedirect();
        expect($record->fresh())->approval_status->toBe('approved')->is_visible->toBeTrue();
    }
});

test('an id in the address is not found, which is what the modal used to send', function () {
    $w = adminListWorld();

    $this->actingAs($w['admin'])->post('/admin/business/'.$w['business']->id.'/status', ['status' => 'approved'])->assertNotFound();
    $this->actingAs($w['admin'])->post('/admin/intrapreneurs/'.$w['company']->id.'/status', ['status' => 'approved'])->assertNotFound();
});
