<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// --- Saving the profile keeps, removes and adds activity documents; users who already have documents used to get a 500 ---

function profileWithDocuments(array|string|null $documents): User
{
    Storage::fake('public');
    foreach (['a.png', 'b.pdf'] as $file) {
        Storage::disk('public')->put('student-activities/'.$file, 'x');
    }

    $user = User::factory()->create();
    $user->forceFill(['activities_doc_url' => $documents])->save();

    return $user;
}

function saveProfile(User $user, array $extra = [])
{
    return test()->actingAs($user)->patch('/profile', ['name' => 'Saved Name', 'whatsapp' => '0812'] + $extra);
}

test('a user who already has activity documents can save the profile', function () {
    $user = profileWithDocuments(['/storage/student-activities/a.png', '/storage/student-activities/b.pdf']);

    saveProfile($user)->assertSessionHasNoErrors()->assertRedirect('/profile');

    $user->refresh();
    expect($user->name)->toBe('Saved Name')
        ->and($user->activities_doc_url)->toBe(['/storage/student-activities/a.png', '/storage/student-activities/b.pdf']);
});

test('documents saved in the old semicolon format are kept and rewritten as JSON', function () {
    $user = profileWithDocuments('/storage/student-activities/a.png;/storage/student-activities/b.pdf');

    saveProfile($user)->assertRedirect('/profile');

    $user->refresh();
    expect($user->activities_doc_url)->toBe(['/storage/student-activities/a.png', '/storage/student-activities/b.pdf'])
        ->and(json_decode($user->getRawOriginal('activities_doc_url'), true))->toBe($user->activities_doc_url);
});

test('a flagged document is deleted from the list and from the disk, the others stay', function () {
    $user = profileWithDocuments(['/storage/student-activities/a.png', '/storage/student-activities/b.pdf']);

    saveProfile($user, ['delete_activities_files' => ['/storage/student-activities/a.png']])->assertRedirect('/profile');

    expect($user->fresh()->activities_doc_url)->toBe(['/storage/student-activities/b.pdf']);
    Storage::disk('public')->assertMissing('student-activities/a.png');
    Storage::disk('public')->assertExists('student-activities/b.pdf');
});

test('deleting every document leaves none', function () {
    $user = profileWithDocuments(['/storage/student-activities/a.png']);

    saveProfile($user, ['delete_activities_files' => ['/storage/student-activities/a.png']])->assertRedirect('/profile');

    expect($user->fresh()->activities_doc_url)->toBe([])
        ->and($user->fresh()->getRawOriginal('activities_doc_url'))->toBeNull();
});

test('uploaded documents are added after the existing ones and stored as JSON', function () {
    $user = profileWithDocuments(['/storage/student-activities/a.png']);

    saveProfile($user, ['activities_files' => [UploadedFile::fake()->create('report.pdf', 20, 'application/pdf')]])->assertRedirect('/profile');

    $documents = $user->fresh()->activities_doc_url;
    expect($documents)->toHaveCount(2)
        ->and($documents[0])->toBe('/storage/student-activities/a.png')
        ->and($documents[1])->toContain('student-activities/act_'.$user->id.'_')
        ->and(json_decode($user->fresh()->getRawOriginal('activities_doc_url'), true))->toBe($documents);
});

test('a user without documents can upload the first one', function () {
    $user = profileWithDocuments(null);

    saveProfile($user, ['activities_files' => [UploadedFile::fake()->image('first.png')]])->assertRedirect('/profile');

    expect($user->fresh()->activities_doc_url)->toHaveCount(1);
});
