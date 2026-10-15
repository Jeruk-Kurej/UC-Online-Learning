<?php

use App\Models\Business;
use App\Models\Category;
use App\Models\Collab;
use App\Models\Company;
use App\Models\Message;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// --- World ---

function smokeWorld(): array
{
    $pdo = DB::connection()->getPdo();
    if (DB::connection()->getDriverName() === 'sqlite') {
        $pdo->sqliteCreateFunction('REGEXP', fn ($pattern, $value) => (int) preg_match('/'.$pattern.'/', (string) $value), 2);
    }

    $category = Category::create(['name' => 'Kuliner', 'slug' => 'kuliner']);

    $admin = User::factory()->create(['role' => 'admin', 'name' => 'Smoke Admin']);

    $owner = User::factory()->create([
        'name' => 'Smoke Owner',
        'current_status' => 'Entrepreneur',
        'is_visible' => true,
        'is_featured' => true,
        'is_featured_testimony' => true,
        'testimony' => 'Smoke testimony text.',
    ]);

    $employee = User::factory()->create([
        'name' => 'Smoke Employee',
        'current_status' => 'Intrapreneur',
        'is_visible' => true,
    ]);

    $business = Business::create([
        'user_id' => $owner->id,
        'category_id' => $category->id,
        'name' => 'Smoke Venture',
        'description' => 'A venture used by the smoke test.',
        'offering_type' => 'both',
        'type' => 'entrepreneur',
        'is_visible' => true,
        'approval_status' => 'approved',
    ]);

    $product = Product::create(['business_id' => $business->id, 'type' => 'product', 'name' => 'Smoke Product']);
    $service = Product::create(['business_id' => $business->id, 'type' => 'service', 'name' => 'Smoke Service']);

    $company = Company::create([
        'user_id' => $owner->id,
        'category_id' => $category->id,
        'name' => 'Smoke Employer',
        'position' => 'Manager',
        'job_description' => 'Runs the smoke test department.',
        'is_visible' => true,
        'approval_status' => 'approved',
    ]);

    Page::create(['slug' => 'about', 'title' => 'About', 'content_json' => null]);

    Collab::create(['sender_id' => $employee->id, 'recipient_id' => $owner->id, 'status' => 'pending']);
    $message = Message::create([
        'sender_id' => $employee->id,
        'recipient_id' => $owner->id,
        'subject' => 'Collab Request',
        'body' => 'Smoke collab request.',
        'type' => 'collab_invite',
    ]);

    return compact('admin', 'owner', 'business', 'product', 'service', 'company', 'message');
}

function smokeUris(array $w): array
{
    $b = $w['business']->slug;
    $c = $w['company']->slug;

    return [
        'home' => '/',
        'about' => '/about',
        'featured' => '/featured',
        'business list' => '/business',
        'business list intrapreneur' => '/business?view=intrapreneur',
        'testimonies' => '/uc-testimonies',
        'login' => '/login',
        'forgot password' => '/forgot-password',
        'business show' => "/business/{$b}",
        'company show' => "/business/{$c}",
        'unknown business' => '/business/does-not-exist',
        'user profile' => '/'.$w['owner']->slug,
        'unknown profile' => '/does-not-exist',
        'profile edit' => '/profile',
        'inbox' => '/inbox',
        'inbox message' => '/inbox/'.$w['message']->id,
        'my business' => '/my-business',
        'my testimony' => '/my-testimony',
        'business create' => '/business/create',
        'business edit' => "/business/{$b}/edit",
        'product create' => "/business/{$b}/products/create",
        'product edit' => "/business/{$b}/products/".$w['product']->id.'/edit',
        'service create' => "/business/{$b}/services/create",
        'service edit' => "/business/{$b}/services/".$w['service']->id.'/edit',
        'company create' => '/intrapreneurs/create',
        'company edit' => "/intrapreneurs/{$c}/edit",
        'achievement create' => "/intrapreneurs/{$c}/achievements/create",
        'regencies api' => '/api/regencies',
        'import check' => '/import-progress/check',
        'import progress' => '/import-progress/some-id',
        'admin users' => '/admin/users',
        'admin users stats' => '/admin/users/stats',
        'admin user create' => '/admin/users/create',
        'admin user edit' => '/admin/users/'.$w['owner']->slug.'/edit',
        'admin businesses' => '/admin/business',
        'admin companies' => '/admin/business?type=intrapreneur',
        'admin testimonies' => '/admin/testimonies',
        'admin about editor' => '/admin/pages/about/edit',
    ];
}

// --- Expected status per page: [guest, owner, admin] ---

function smokeExpected(): array
{
    return [
        'home' => [302, 302, 302],
        'about' => [200, 200, 200],
        'featured' => [200, 200, 200],
        'business list' => [200, 200, 200],
        'business list intrapreneur' => [200, 200, 200],
        'testimonies' => [200, 200, 200],
        'login' => [200, 302, 302],
        'forgot password' => [200, 302, 302],
        'business show' => [200, 200, 200],
        'company show' => [200, 200, 200],
        'unknown business' => [404, 404, 404],
        'user profile' => [200, 200, 200],
        'unknown profile' => [404, 404, 404],
        'profile edit' => [302, 200, 302],
        'inbox' => [302, 200, 200],
        'inbox message' => [302, 200, 403],
        'my business' => [302, 200, 200],
        'my testimony' => [302, 200, 200],
        'business create' => [302, 200, 200],
        'business edit' => [302, 200, 200],
        'product create' => [302, 200, 200],
        'product edit' => [302, 200, 200],
        'service create' => [302, 200, 200],
        'service edit' => [302, 200, 200],
        'company create' => [302, 200, 200],
        'company edit' => [302, 200, 200],
        'achievement create' => [302, 200, 200],
        'regencies api' => [302, 200, 200],
        'import check' => [302, 200, 200],
        'import progress' => [302, 200, 200],
        'admin users' => [302, 403, 200],
        'admin users stats' => [302, 403, 200],
        'admin user create' => [302, 403, 200],
        'admin user edit' => [302, 403, 200],
        'admin businesses' => [302, 403, 200],
        'admin companies' => [302, 403, 200],
        'admin testimonies' => [302, 403, 200],
        'admin about editor' => [302, 403, 200],
    ];
}

// --- Test ---

test('every page answers with the recorded status for guest, owner and admin', function () {
    $world = smokeWorld();
    $uris = smokeUris($world);
    $expected = smokeExpected();

    $actual = [];
    foreach ($uris as $label => $uri) {
        $row = [];
        foreach ([null, $world['owner'], $world['admin']] as $actor) {
            $this->app['auth']->forgetGuards();
            $request = $actor ? $this->actingAs($actor) : $this;
            $row[] = $request->get($uri)->getStatusCode();
        }
        $actual[$label] = $row;
    }

    expect($actual)->toBe($expected);
});
