<?php

use App\Models\Business;
use App\Models\Message;
use App\Models\Province;
use App\Models\User;

// --- Values the views read from the models instead of building them in Blade ---

test('initial is the upper-cased first letter of the name', function () {
    expect((new User(['name' => 'agus wijaya']))->initial)->toBe('A')
        ->and((new Business(['name' => 'eFishery']))->initial)->toBe('E');
});

test('whatsapp link keeps only the digits of the number', function () {
    expect((new User(['whatsapp' => '+62 812-3456']))->whatsapp_link)->toBe('https://wa.me/628123456')
        ->and((new User)->whatsapp_link)->toBeNull();
});

test('linkedin url accepts a full link or a handle', function () {
    expect((new User(['linkedin' => 'budi']))->linkedin_url)->toBe('https://linkedin.com/in/budi')
        ->and((new User(['linkedin' => 'https://www.linkedin.com/in/budi']))->linkedin_url)->toBe('https://www.linkedin.com/in/budi');
});

test('contacts show when the person allows it or the viewer is an admin', function () {
    $user = new User(['email' => 'a@b.test', 'show_contact_details' => false]);
    $admin = new User(['role' => 'admin']);

    expect($user->hasContactDetails())->toBeTrue()
        ->and($user->contactsVisibleTo(null))->toBeFalse()
        ->and($user->contactsVisibleTo($admin))->toBeTrue()
        ->and((new User(['linkedin' => 'budi']))->hasContactDetails(withLinkedin: false))->toBeFalse();
});

test('the public status reads Student or Alumni', function () {
    expect((new User(['student_status' => 'alumni']))->display_status)->toBe('Alumni')
        ->and((new User(['student_status' => 'student non aktif']))->display_status)->toBe('Student')
        ->and((new User)->display_status)->toBe('Student');
});

test('business website labels', function () {
    $business = new Business(['website' => 'kopi.id', 'type' => 'entrepreneur']);

    expect($business->website_url)->toBe('https://kopi.id')
        ->and((new Business(['website' => 'https://kopi.id']))->website_label)->toBe('kopi.id')
        ->and($business->isEntrepreneur())->toBeTrue();
});

test('review feedback shows only for rejected or revision items with a reason', function () {
    expect((new Business(['approval_status' => 'rejected', 'rejection_reason' => 'Blurry']))->hasReviewFeedback())->toBeTrue()
        ->and((new Business(['approval_status' => 'need_revision', 'rejection_reason' => null]))->hasReviewFeedback())->toBeFalse()
        ->and((new Business(['approval_status' => 'approved', 'rejection_reason' => 'Old']))->hasReviewFeedback())->toBeFalse();
});

test('message excerpt drops tags and shortens the body', function () {
    expect((new Message(['body' => '<b>Hi</b> '.str_repeat('a', 200)]))->excerpt)->toStartWith('Hi aaa')->toEndWith('...');
});

test('region names read in title case', function () {
    $province = new Province;
    $province->name = 'JAWA TIMUR';

    expect($province->display_name)->toBe('Jawa Timur');
});

test('navigation link marks the active section', function () {
    expect((string) $this->blade('<x-nav.link look="account" href="/inbox" :active="true">Inbox</x-nav.link>'))
        ->toContain('class="bg-brand-50 font-medium text-brand-900 block rounded-md px-3 py-3 text-base"');
    expect((string) $this->blade('<x-nav.link look="dropdown" href="/inbox">Inbox</x-nav.link>'))
        ->toContain('class="block px-4 py-2.5 text-base text-gray-800 hover:bg-brand-50 hover:text-brand-900"');
});
