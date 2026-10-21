<?php

use App\Models\Company;
use App\Models\User;

// --- Work profile page: the achievements list reaches its script and the owner can delete one ---

function workProfileWithAchievements(User $owner): Company
{
    return Company::create(['user_id' => $owner->id, 'name' => 'Maju Corp', 'is_visible' => true, 'approval_status' => 'approved', 'achievement' => "- Raised sales by 20%\n- Opened two branches"]);
}

test('the achievements list is handed to its script as data, for visitors and for the owner', function () {
    $owner = User::factory()->create();
    $company = workProfileWithAchievements($owner);
    $expected = e(Js::from(['Raised sales by 20%', 'Opened two branches']));

    foreach ([null, $owner] as $viewer) {
        $viewer ? $this->actingAs($viewer) : auth()->logout();
        $html = $this->get('/business/'.$company->slug)->assertOk()->getContent();

        expect($html)->not->toContain('@js(')
            ->and($html)->toContain('x-data="achievementList('.$expected)
            ->and($html)->toContain(e(Js::from(route('intrapreneurs.delete_achievement', $company, false))));
    }
});

test('the owner deletes an achievement through the address the page hands over', function () {
    $owner = User::factory()->create();
    $company = workProfileWithAchievements($owner);

    $this->actingAs($owner)->deleteJson(route('intrapreneurs.delete_achievement', $company, false), ['index' => 0])
        ->assertOk()->assertJson(['success' => true, 'achievements' => ['Opened two branches']]);
});
