<?php

use App\Models\Page;
use App\Models\User;

function editorSections($response): array
{
    preg_match('#<script id="initial-sections-data" type="application/json">(.*?)</script>#s', $response->getContent(), $m);

    return json_decode($m[1], true);
}

test('the about page shows the default sections when no page exists', function () {
    $this->get('/about')->assertOk()
        ->assertSee('About UC Online Learning')
        ->assertSee('Find businesses and people')->assertDontSee('1200+')->assertDontSee('Get Started Now');
});

test('the about page and the editor use saved sections', function () {
    $page = Page::create(['slug' => 'about', 'title' => 'About', 'content_json' => ['sections' => [
        ['type' => 'hero', 'badge' => 'Custom Badge', 'title' => 'Custom Hero Title', 'subtitle' => 'Custom subtitle'],
    ]]]);

    $this->get('/about')->assertSee('Custom Hero Title')->assertDontSee('Find businesses and people');

    $admin = User::factory()->admin()->create();
    expect(editorSections($this->actingAs($admin)->get(route('pages.edit', $page))))
        ->toBe([['type' => 'hero', 'badge' => 'Custom Badge', 'title' => 'Custom Hero Title', 'subtitle' => 'Custom subtitle']]);
});

test('older pages saved with hero/pillars/stats/cta keys fill the default sections', function () {
    $page = Page::create(['slug' => 'about', 'title' => 'About', 'content_json' => [
        'hero' => ['title' => 'Legacy Hero'],
        'stats' => ['title' => 'Legacy numbers', 'items' => [['number' => '1200+', 'label' => 'Graduated Founders']]],
        'cta' => ['heading' => 'Legacy CTA'],
    ]]);

    $this->get('/about')->assertSee('Legacy Hero')->assertSee('Legacy CTA')->assertSee('Find businesses and people');

    $admin = User::factory()->admin()->create();
    $sections = editorSections($this->actingAs($admin)->get(route('pages.edit', $page)));
    expect(array_column($sections, 'type'))->toBe(['hero', 'feature_cards', 'stats_grid', 'cta_banner'])
        ->and($sections[0]['title'])->toBe('Legacy Hero')
        ->and($sections[0]['badge'])->toBe('')
        ->and($sections[3]['heading'])->toBe('Legacy CTA')
        ->and($sections[2]['items'][0])->toBe(['number' => '1200+', 'label' => 'Graduated Founders']);
});

test('an empty page uses the defaults in the editor', function () {
    $page = Page::create(['slug' => 'about', 'title' => 'About', 'content_json' => null]);
    $admin = User::factory()->admin()->create();

    $sections = editorSections($this->actingAs($admin)->get(route('pages.edit', $page)));
    expect(count($sections))->toBe(2)->and($sections[1]['cards'][1]['icon'])->toBe('bi-rocket-takeoff');
});
