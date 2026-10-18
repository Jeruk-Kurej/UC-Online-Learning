<?php

use App\Imports\FormResponseImport;
use App\Imports\UCOStudentImport;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\TestHandler;

// --- Imports run on a server whose logs are readable by more people than the admin list: no personal data in them ---

const PRIVATE_VALUES = [
    'pii-check@example.test', 'Pii Check Person', '0812-9999-0000', '0813-1111-2222', 'Secret testimony words', 'Pii Business Name',
    'noname-pii@example.test', 'dbfail-pii@example.test', 'Pii Student Name', 'nis-pii-4455',
];

function captureLog(): TestHandler
{
    config(['logging.default' => 'privacy', 'logging.channels.privacy' => ['driver' => 'monolog', 'handler' => TestHandler::class, 'level' => 'debug']]);
    Log::forgetChannel('privacy');

    return Log::channel('privacy')->getLogger()->getHandlers()[0];
}

function loggedText(TestHandler $handler): string
{
    return collect($handler->getRecords())->map(fn ($record) => $record->message.' '.json_encode($record->context))->implode("\n");
}

function resetDebugDump(): void
{
    $property = new ReflectionProperty(FormResponseImport::class, 'debugKeysDumped');
    $property->setValue(null, false);
}

function formRow(array $overrides = []): array
{
    return $overrides + [
        'email_address' => 'pii-check@example.test', 'full_name' => 'Pii Check Person', 'category' => 'Entrepreneur', 'current_status' => 'alumni',
        'graduate_year' => '2021', 'personal_phone_number' => '0812-9999-0000', 'personal_whatsapp' => '0813-1111-2222', 'testimony' => 'Secret testimony words',
        'business_name' => 'Pii Business Name', 'timestamp' => '2026-05-23 22:00:00',
    ];
}

test('the form importer logs no personal data for good rows, skipped rows and failing rows', function () {
    $handler = captureLog();
    resetDebugDump();
    $import = new FormResponseImport('privacy-form');

    $import->model(formRow());
    $import->model(formRow(['email_address' => 'noname-pii@example.test', 'full_name' => '']));
    User::creating(function (User $user) {
        if ($user->email === 'dbfail-pii@example.test') {
            throw new QueryException('mysql', 'insert into `users` (`email`, `name`) values (?, ?)', ['dbfail-pii@example.test', 'Pii Check Person'], new Exception("Data too long for column 'name' at row 1"));
        }
    });
    $import->model(formRow(['email_address' => 'dbfail-pii@example.test']));

    $logged = loggedText($handler);
    expect($logged)->not->toBe('');
    foreach (PRIVATE_VALUES as $value) {
        expect($logged)->not->toContain($value);
    }
});

test('the progress card still tells the admin which email was skipped and why the database refused a row', function () {
    captureLog();
    $import = new FormResponseImport('privacy-card');
    User::creating(function (User $user) {
        if ($user->email === 'dbfail-pii@example.test') {
            throw new QueryException('mysql', 'insert into `users` (`email`) values (?)', ['dbfail-pii@example.test'], new Exception("Data too long for column 'name' at row 1"));
        }
    });

    $import->model(formRow(['email_address' => 'noname-pii@example.test', 'full_name' => '']));
    $import->model(formRow(['email_address' => 'dbfail-pii@example.test']));

    $errors = Cache::get('import_privacy-card')['errors'];
    expect($errors)->toHaveCount(2)
        ->and($errors[0])->toBe('Missing Full Name for noname-pii@example.test')
        ->and($errors[1])->toContain("Data too long for column 'name' at row 1")
        ->and($errors[1])->not->toContain('insert into')
        ->and($errors[1])->not->toContain('dbfail-pii@example.test');
});

test('the UCO student importer logs no personal data either', function () {
    $handler = captureLog();
    $import = new UCOStudentImport('privacy-uco');
    $blank = array_fill(0, 40, null);
    $noEmail = array_replace($blank, [3 => 'Pii Student Name', 5 => '0813-1111-2222']);
    $noName = array_replace($blank, [1 => 'nis-pii-4455', 6 => 'noname-pii@example.test']);
    User::creating(fn () => throw new QueryException('mysql', 'insert into `users` values (?)', ['dbfail-pii@example.test'], new Exception('Duplicate entry')));
    $good = array_replace($blank, [1 => 'nis-pii-4455', 3 => 'Pii Student Name', 6 => 'dbfail-pii@example.test']);

    $import->array([$noEmail, $noName, $good]);

    $logged = loggedText($handler);
    expect($logged)->not->toBe('');
    foreach (PRIVATE_VALUES as $value) {
        expect($logged)->not->toContain($value);
    }
});
