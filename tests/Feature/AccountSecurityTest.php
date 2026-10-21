<?php

use App\Models\User;
use App\Support\StartingPassword;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

// --- No password lives in the code; the shared starting password of imported accounts never opens an account ---

test('the code holds no password: every hash of a fixed text is gone from app and the seeders', function () {
    $sources = collect(File::allFiles(app_path()))->merge(File::allFiles(database_path('seeders')))
        ->filter(fn ($file) => preg_match('/Hash::make\(\s*[\'"]/', $file->getContents()) === 1)
        ->map(fn ($file) => $file->getRelativePathname())
        ->values()
        ->all();

    expect($sources)->toBe([]);
});

test('the starting password is refused at the login form, even when it is the right one', function () {
    config(['accounts.starting_password' => 'shared-start-123']);
    $imported = User::factory()->create(['password' => StartingPassword::hash()]);
    expect(Hash::check('shared-start-123', $imported->password))->toBeTrue();

    $this->post('/login', ['email' => $imported->email, 'password' => 'shared-start-123'])
        ->assertSessionHasErrors(['email' => 'This is the starting password, which cannot be used to log in. Log in with Google using this email address, then choose your own password in My profile.']);
    $this->assertGuest();

    $own = User::factory()->create();
    $this->post('/login', ['email' => $own->email, 'password' => 'password']);
    $this->assertAuthenticated();
});

test('without a starting password set, imported accounts get a random one nobody knows', function () {
    config(['accounts.starting_password' => null]);

    $first = StartingPassword::hash();
    expect(StartingPassword::matches('anything'))->toBeFalse()
        ->and(Hash::check('', $first))->toBeFalse()
        ->and($first)->not->toBe(StartingPassword::hash());
});

test('right after a Google sign-in a person sets a password without the current one, and never the starting one', function () {
    config(['accounts.starting_password' => 'shared-start-123']);
    $user = User::factory()->create(['password' => StartingPassword::hash()]);

    $form = $this->actingAs($user)->withSession(['signed_in_with_google' => true])->get(route('profile.edit'))->getContent();
    expect($form)->not->toContain('name="current_password"')->and($form)->toContain('You logged in with Google');

    $this->actingAs($user)->withSession(['signed_in_with_google' => true])
        ->put(route('password.update'), ['password' => 'shared-start-123', 'password_confirmation' => 'shared-start-123'])
        ->assertSessionHasErrorsIn('updatePassword', ['password' => 'Choose a password of your own, not the starting password.']);

    $this->actingAs($user)->withSession(['signed_in_with_google' => true])
        ->put(route('password.update'), ['password' => 'my-own-secret-9', 'password_confirmation' => 'my-own-secret-9'])
        ->assertSessionHasNoErrors()->assertSessionMissing('signed_in_with_google');
    expect(Hash::check('my-own-secret-9', $user->fresh()->password))->toBeTrue();

    $this->actingAs($user)->put(route('password.update'), ['password' => 'another-secret-9', 'password_confirmation' => 'another-secret-9'])
        ->assertSessionHasErrorsIn('updatePassword', 'current_password');
    $this->actingAs($user)->get(route('profile.edit'))->assertSee('name="current_password"', false);
});

test('the sample accounts take their password from SEED_PASSWORD, or a random one', function () {
    config(['accounts.seed_password' => 'local-seed-pass']);
    $this->seed(UserSeeder::class);
    expect(Hash::check('local-seed-pass', User::where('email', 'admin@uco.com')->value('password')))->toBeTrue();

    User::query()->delete();
    config(['accounts.seed_password' => null]);
    $this->seed(UserSeeder::class);
    expect(Hash::check('local-seed-pass', User::where('email', 'admin@uco.com')->value('password')))->toBeFalse();
});

test('a Google sign-in for a known email logs in and allows setting a password without the current one', function () {
    $user = User::factory()->create(['email' => 'known@student.ciputra.ac.id']);
    $googleUser = Mockery::mock(Laravel\Socialite\Two\User::class);
    $googleUser->shouldReceive('getEmail')->andReturn('known@student.ciputra.ac.id');
    $googleUser->shouldReceive('getId')->andReturn('google-123');
    Socialite::shouldReceive('driver->user')->andReturn($googleUser);

    $this->get(route('auth.google.callback'))->assertRedirect(route('home'))->assertSessionHas('signed_in_with_google', true);
    $this->assertAuthenticatedAs($user);
});
