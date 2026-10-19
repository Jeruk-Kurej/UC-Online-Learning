<?php

use App\Models\Business;
use App\Models\Category;
use App\Models\Company;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// --- Rows imported in one go share a created_at; a list that pages by it alone can show a row twice and skip another ---

function orderOfList(string $table, int $limit, callable $request): string
{
    $queries = [];
    DB::listen(function ($query) use (&$queries) {
        $queries[] = $query->sql;
    });
    $request();

    $list = collect($queries)->first(fn (string $sql) => str_contains($sql, "from \"{$table}\"") && str_contains($sql, " limit {$limit}") && str_contains($sql, 'order by'));
    expect($list)->not->toBeNull("no ordered list query on {$table} with limit {$limit}");

    return trim(preg_replace('/ limit .*$/', '', substr($list, strpos($list, 'order by') + 9)));
}

function listWorld(): array
{
    $category = Category::create(['name' => 'Kuliner', 'slug' => 'kuliner']);
    $admin = User::factory()->admin()->create();
    $owner = User::factory()->create(['testimony' => 'Great place.', 'is_featured_testimony' => true, 'is_visible' => true, 'profile_photo_url' => 'https://example.test/me.jpg']);
    Business::create(['user_id' => $owner->id, 'name' => 'Order Biz', 'slug' => 'order-biz', 'category_id' => $category->id, 'type' => 'entrepreneur', 'approval_status' => 'approved', 'is_visible' => true]);
    Company::create(['user_id' => $owner->id, 'name' => 'Order Corp', 'slug' => 'order-corp', 'category_id' => $category->id, 'approval_status' => 'approved', 'is_visible' => true]);
    Message::create(['sender_id' => $admin->id, 'recipient_id' => $owner->id, 'subject' => 'Hello', 'body' => 'Body', 'type' => 'general']);

    return compact('admin', 'owner');
}

test('the public directory pages break ties by id', function () {
    listWorld();

    expect(orderOfList('businesses', 6, fn () => $this->get(route('businesses.index'))))->toEndWith('"created_at" desc, "id" desc')
        ->and(orderOfList('companies', 6, fn () => $this->get(route('businesses.index', ['view' => 'intrapreneur']))))->toEndWith('"created_at" desc, "id" desc');
});

test('the admin business and work profile lists break ties by id', function () {
    $w = listWorld();

    expect(orderOfList('businesses', 10, fn () => $this->actingAs($w['admin'])->get(route('businesses.admin'))))->toEndWith('"created_at" desc, "id" desc')
        ->and(orderOfList('companies', 10, fn () => $this->actingAs($w['admin'])->get(route('businesses.admin', ['type' => 'intrapreneur']))))->toEndWith('"created_at" desc, "id" desc');
});

test('the admin user list breaks ties by id, also when sorted by name', function () {
    $w = listWorld();

    foreach (['' => '"created_at" desc, "id" desc', 'sort_name=asc' => '"name" asc, "created_at" desc, "id" desc'] as $query => $expected) {
        $order = orderOfList('users', 10, fn () => $this->actingAs($w['admin'])->get(route('users.index').($query ? '?'.$query : '')));
        expect($order)->toEndWith($expected);
    }
});

test('sorting users by enrollment year still ends with created_at and id', function () {
    // --- The year sort uses MySQL's REGEXP, which SQLite has no function for, so the page itself cannot run here ---
    expect(User::orderByEnrollmentYear('DESC')->newestFirst()->toSql())->toEndWith('DESC, "created_at" desc, "id" desc');
});

test('the admin testimony list breaks ties by id', function () {
    $w = listWorld();

    expect(orderOfList('users', 12, fn () => $this->actingAs($w['admin'])->get(route('uc-testimonies.admin'))))->toEndWith('"submitted_at" desc, "created_at" desc, "id" desc');
});

test('the inbox breaks ties by id', function () {
    $w = listWorld();

    expect(orderOfList('messages', 15, fn () => $this->actingAs($w['owner'])->get(route('inbox.index'))))->toEndWith('"created_at" desc, "id" desc');
});

test('the home page reads its featured testimonies in a fixed base order', function () {
    listWorld();

    $queries = [];
    DB::listen(function ($query) use (&$queries) {
        $queries[] = $query->sql;
    });
    $this->get(route('home'));

    $list = collect($queries)->first(fn (string $sql) => str_contains($sql, 'is_featured_testimony'));
    expect($list)->not->toBeNull()->and($list)->toEndWith('order by "created_at" desc, "id" desc');
});
