<?php

use App\Models\Page;

// --- Every response says how long PHP and the database took, and how many queries ran ---

test('a page answers with boot, database and total timings and its number of queries', function () {
    $header = $this->get('/login')->assertOk()->headers->get('Server-Timing');

    expect($header)->toMatch('/^boot;dur=\d+\.\d;desc="Laravel boot", db;dur=\d+\.\d;desc="0 queries", total;dur=\d+\.\d;desc="Total in PHP"$/');
});

test('the query count follows the queries the page runs', function () {
    Page::firstOrCreate(['slug' => 'about'], ['title' => 'About', 'content_json' => []]);

    $header = $this->get('/about')->assertOk()->headers->get('Server-Timing');
    preg_match('/desc="(\d+) quer/', $header, $count);

    expect((int) $count[1])->toBeGreaterThan(0);
});
