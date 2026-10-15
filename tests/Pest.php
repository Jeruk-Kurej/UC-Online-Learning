<?php

// --- Every feature test runs on Laravel's TestCase with a fresh in-memory database ---

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');
