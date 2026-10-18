<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

// --- The stats refresh after an import writes into counters by id and fetches the stats address from the page ---

function managerPage(): string
{
    // --- The page sorts enrollment years with MySQL's REGEXP; SQLite only has it when a function is registered ---
    DB::connection()->getPdo()->sqliteCreateFunction('REGEXP', fn ($pattern, $value) => preg_match('/'.$pattern.'/', (string) $value) ? 1 : 0, 2);

    return test()->actingAs(User::factory()->admin()->create())->get(route('users.index'))->assertOk()->getContent();
}

function statsRefreshIds(): array
{
    preg_match_all('/[\'"](stat-[a-z-]+)[\'"]/', file_get_contents(resource_path('js/pages/user-admin.js')), $matches);

    return array_values(array_unique($matches[1]));
}

test('every counter the stats refresh writes into exists on the Manage Users page', function () {
    $html = managerPage();

    expect(statsRefreshIds())->toContain('stat-total', 'stat-entrepreneurs', 'stat-intrapreneurs', 'stat-alumni');
    foreach (statsRefreshIds() as $id) {
        expect($html)->toContain('id="'.$id.'"');
    }
});

test('the page hands the script the address of the stats endpoint', function () {
    $html = managerPage();

    expect($html)->toContain('data-stats-url="'.route('users.stats', [], false).'"')
        ->and(route('users.stats', [], false))->toBe('/admin/users/stats');
});
