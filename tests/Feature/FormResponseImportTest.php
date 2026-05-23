<?php

use App\Imports\FormResponseImport;
use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

test('importer processes selected true and false rows without skipping', function () {
    $import = new FormResponseImport('test-import-id');

    // Row 1: selected = TRUE
    $row1 = [
        'email_address' => 'featured@example.com',
        'full_name' => 'Featured Student',
        'category' => 'Entrepreneur',
        'current_status' => 'alumni',
        'selected' => 'TRUE',
        'business_name' => 'Featured Business',
        'timestamp' => '2026-05-23 22:00:00',
    ];

    // Row 2: selected = FALSE
    $row2 = [
        'email_address' => 'regular@example.com',
        'full_name' => 'Regular Student',
        'category' => 'Entrepreneur',
        'current_status' => 'alumni',
        'selected' => 'FALSE',
        'business_name' => 'Regular Business',
        'timestamp' => '2026-05-23 22:00:00',
    ];

    // Import row 1
    $user1 = $import->model($row1);
    $this->assertNotNull($user1);
    $this->assertEquals('featured@example.com', $user1->email);
    $this->assertTrue($user1->is_featured);

    $business1 = Business::where('name', 'Featured Business')->first();
    $this->assertNotNull($business1);
    $this->assertTrue($business1->is_featured);

    // Import row 2
    $user2 = $import->model($row2);
    $this->assertNotNull($user2);
    $this->assertEquals('regular@example.com', $user2->email);
    $this->assertFalse($user2->is_featured);

    $business2 = Business::where('name', 'Regular Business')->first();
    $this->assertNotNull($business2);
    $this->assertFalse($business2->is_featured);
});
