<?php

use App\Imports\UCOStudentImport;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

// --- Small bugs from the roadmap, each with the case that showed it ---

function achievementCompany(): array
{
    $owner = User::factory()->create();

    return [$owner, Company::create(['user_id' => $owner->id, 'name' => 'List Corp', 'position' => 'Lead', 'job_description' => 'Leads.', 'approval_status' => 'approved', 'is_visible' => true, 'achievement' => "- First\n- Second\n- Third"])];
}

test('an achievement is deleted only when the item at that place still reads the same', function () {
    [$owner, $company] = achievementCompany();
    $url = route('intrapreneurs.delete_achievement', $company);

    $this->actingAs($owner)->deleteJson($url, ['index' => 1, 'text' => 'First'])
        ->assertOk()->assertJson(['success' => false, 'achievements' => ['First', 'Second', 'Third']]);
    $this->actingAs($owner)->deleteJson($url, ['index' => 9, 'text' => 'Third'])->assertJson(['success' => false]);
    expect($company->fresh()->achievements_list)->toBe(['First', 'Second', 'Third']);

    $this->actingAs($owner)->deleteJson($url, ['index' => 1, 'text' => 'Second'])
        ->assertJson(['success' => true, 'achievements' => ['First', 'Third']]);
});

test('deleting a missing achievement through the form says so instead of claiming success', function () {
    [$owner, $company] = achievementCompany();

    $this->actingAs($owner)->from('/business/'.$company->slug)->delete(route('intrapreneurs.delete_achievement', $company), ['index' => 7])
        ->assertSessionHas('error')->assertSessionMissing('success');
});

test('the UCO import summary log reads the shared counters of a queued import', function () {
    Cache::forever('import_abc_success', 12);
    Cache::forever('import_abc_skipped', 3);

    $line = (fn () => $this->completedLogLine())->call(new UCOStudentImport('abc'));

    expect($line)->toBe('[UCOStudentImport] Done: 12 ok, 3 skipped');
});
