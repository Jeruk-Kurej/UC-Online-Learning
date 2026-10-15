<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// --- Every feature test runs on Laravel's TestCase with a fresh in-memory database ---

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');
