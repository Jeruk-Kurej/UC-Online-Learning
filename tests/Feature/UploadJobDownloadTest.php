<?php

use App\Jobs\UploadImageToCloudinaryJob;
use App\Models\User;

// --- The job downloads images from links in imported files: who gets the portal cookie, and how the request is made ---

function downloadJob(): UploadImageToCloudinaryJob
{
    return new UploadImageToCloudinaryJob(new User, 'profile_photo_url', 'https://example.test/a.jpg', 'users', 'Someone');
}

function callPrivate(object $object, string $method, mixed ...$arguments): mixed
{
    return (new ReflectionMethod($object, $method))->invoke($object, ...$arguments);
}

test('only the university portal hosts count as the portal, whatever else the link contains', function (string $url, bool $portal) {
    expect(callPrivate(downloadJob(), 'isUniversityPortal', $url))->toBe($portal);
})->with([
    'portal' => ['https://employee.uc.ac.id/index.php/file/get/sis/a.jpg', true],
    'portal on the ciputra host' => ['http://employee.ciputra.ac.id/index.php/file/get/sis/a.jpg', true],
    'upper-case host' => ['https://EMPLOYEE.UC.AC.ID/a.jpg', true],
    'portal name in the query' => ['https://evil.example/a.jpg?from=employee.uc.ac.id', false],
    'portal name in the path' => ['https://evil.example/employee.ciputra.ac.id/a.jpg', false],
    'portal name as a prefix of another host' => ['https://employee.uc.ac.id.evil.example/a.jpg', false],
    'portal name as the user part' => ['https://employee.uc.ac.id@evil.example/a.jpg', false],
    'google drive' => ['https://drive.google.com/uc?export=download&id=abc', false],
]);

test('the portal cookie and referer go to the portal only', function () {
    config(['services.uc.cookie_raw' => ' session=abc123 ']);
    $job = downloadJob();

    $portal = callPrivate($job, 'requestHeaders', 'https://employee.ciputra.ac.id/a.jpg');
    $other = callPrivate($job, 'requestHeaders', 'https://evil.example/a.jpg?x=employee.uc.ac.id');

    expect($portal)->toContain('Cookie: session=abc123')
        ->and(collect($portal)->contains(fn ($header) => str_starts_with($header, 'Referer: https://employee.uc.ac.id')))->toBeTrue()
        ->and(collect($other)->contains(fn ($header) => str_starts_with($header, 'Cookie:') || str_starts_with($header, 'Referer:')))->toBeFalse()
        ->and($other)->toContain('Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8');
});

test('the download checks the server certificate and only follows http and https', function () {
    $options = callPrivate(downloadJob(), 'curlOptions', 'https://example.test/a.jpg', ['Accept: image/*']);

    expect($options)->not->toHaveKey(CURLOPT_SSL_VERIFYPEER)
        ->and($options)->not->toHaveKey(CURLOPT_SSL_VERIFYHOST)
        ->and($options[CURLOPT_PROTOCOLS])->toBe(CURLPROTO_HTTP | CURLPROTO_HTTPS)
        ->and($options[CURLOPT_REDIR_PROTOCOLS])->toBe(CURLPROTO_HTTP | CURLPROTO_HTTPS)
        ->and($options[CURLOPT_MAXREDIRS])->toBe(5)
        ->and($options[CURLOPT_FOLLOWLOCATION])->toBeTrue()
        ->and($options[CURLOPT_HTTPHEADER])->toBe(['Accept: image/*']);
});
