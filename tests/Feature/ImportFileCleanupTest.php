<?php

use App\Imports\FormResponseImport;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Reader;

// --- The uploaded sheet holds names, phone numbers and links: it is removed as soon as the import is over ---

function importSheet(): UploadedFile
{
    $csv = "Email Address,Full Name,Category,Business Name\ncleanup@example.test,Cleanup Person,Entrepreneur,Cleanup Biz\n";

    return UploadedFile::fake()->createWithContent('form-entrepreneur.csv', $csv);
}

test('the uploaded sheet is gone after a finished import', function (string $route, string $field) {
    Storage::fake('local');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->postJson(route($route), [$field => importSheet()])->assertOk()->assertJson(['success' => true]);

    expect(User::where('email', 'cleanup@example.test')->exists())->toBeTrue()
        ->and(Storage::disk('local')->allFiles('imports'))->toBe([]);
})->with([
    'user import' => ['users.import', 'file'],
    'business import' => ['businesses.import', 'file'],
]);

test('the uploaded sheet is gone when the import cannot be queued', function () {
    Storage::fake('local');
    Excel::shouldReceive('queueImport')->once()->andThrow(new RuntimeException('queue is down'));

    $this->actingAs(User::factory()->admin()->create())->postJson(route('users.import'), ['file' => importSheet()])
        ->assertStatus(500)->assertJson(['success' => false]);

    expect(Storage::disk('local')->allFiles('imports'))->toBe([]);
});

test('a queued import that finishes or fails removes its sheet', function (object $event) {
    Storage::fake('local');
    Storage::disk('local')->put('imports/sheet.csv', 'secret');
    $import = (new FormResponseImport('cleanup-event'))->deleteFileAfterImport('imports/sheet.csv');

    ($import->registerEvents()[$event::class])($event);

    Storage::disk('local')->assertMissing('imports/sheet.csv');
})->with([
    'after the import' => fn () => new AfterImport(app(Reader::class), new FormResponseImport),
    'when it failed' => fn () => new ImportFailed(new RuntimeException('boom')),
]);

test('an importer without an uploaded sheet leaves the disk alone', function () {
    Storage::fake('local');
    Storage::disk('local')->put('imports/other.csv', 'keep');

    $import = new FormResponseImport('no-path');
    ($import->registerEvents()[ImportFailed::class])(new ImportFailed(new RuntimeException('boom')));

    Storage::disk('local')->assertExists('imports/other.csv');
});
