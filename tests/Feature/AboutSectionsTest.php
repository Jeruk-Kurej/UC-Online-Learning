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
        ->assertSee('Building the Future of Student &amp; Alumni Entrepreneurship', false)
        ->assertSee('Rapid Launch')->assertSee('1200+')->assertSee('Ready to build your legacy?');
});

test('the about page and the editor use saved sections', function () {
    $page = Page::create(['slug' => 'about', 'title' => 'About', 'content_json' => ['sections' => [
        ['type' => 'hero', 'badge' => 'Custom Badge', 'title' => 'Custom Hero Title', 'subtitle' => 'Custom subtitle'],
    ]]]);

    $this->get('/about')->assertSee('Custom Hero Title')->assertDontSee('Rapid Launch');

    $admin = User::factory()->admin()->create();
    expect(editorSections($this->actingAs($admin)->get(route('pages.edit', $page))))
        ->toBe([['type' => 'hero', 'badge' => 'Custom Badge', 'title' => 'Custom Hero Title', 'subtitle' => 'Custom subtitle']]);
});

test('older pages saved with hero/pillars/stats/cta keys fill the default sections', function () {
    $page = Page::create(['slug' => 'about', 'title' => 'About', 'content_json' => [
        'hero' => ['title' => 'Legacy Hero'],
        'cta' => ['heading' => 'Legacy CTA'],
    ]]);

    $this->get('/about')->assertSee('Legacy Hero')->assertSee('Legacy CTA')->assertSee('Rapid Launch');

    $admin = User::factory()->admin()->create();
    $sections = editorSections($this->actingAs($admin)->get(route('pages.edit', $page)));
    expect(array_column($sections, 'type'))->toBe(['hero', 'feature_cards', 'stats_grid', 'cta_banner'])
        ->and($sections[0]['title'])->toBe('Legacy Hero')
        ->and($sections[0]['badge'])->toBe('About UC Online Learning')
        ->and($sections[3]['heading'])->toBe('Legacy CTA')
        ->and($sections[2]['items'][1])->toBe(['number' => '1200+', 'label' => 'Graduated Founders']);
});

test('an empty page uses the defaults in the editor', function () {
    $page = Page::create(['slug' => 'about', 'title' => 'About', 'content_json' => null]);
    $admin = User::factory()->admin()->create();

    $sections = editorSections($this->actingAs($admin)->get(route('pages.edit', $page)));
    expect(count($sections))->toBe(4)->and($sections[1]['cards'][0]['icon'])->toBe('bi-rocket-takeoff');
});
