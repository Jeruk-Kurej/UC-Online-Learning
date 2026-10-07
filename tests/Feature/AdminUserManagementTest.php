<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can view user edit screen', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $targetUser = User::factory()->create([
        'role' => 'user',
        'name' => 'Naqiya Salsabila Yasmien',
        'slug' => 'naqiya-salsabila-yasmien',
    ]);

    $response = $this->actingAs($admin)
        ->get(route('users.edit', $targetUser));

    $response->assertStatus(200);
});

test('admin can update user successfully without 500 server error', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $targetUser = User::factory()->create([
        'role' => 'user',
        'name' => 'Naqiya Salsabila Yasmien',
        'slug' => 'naqiya-salsabila-yasmien',
    ]);

    $updateData = [
        'name' => 'Naqiya Salsabila Yasmien Updated',
        'email' => $targetUser->email,
        'role' => 'user',
        'student_status' => 'student aktif',
        'prefix_title' => 'S.Tr.Kom',
        'suffix_title' => 'M.Kom',
        'personal_email' => 'naqiya@example.com',
        'phone_number' => '081234567890',
        'mobile_number' => '081234567890',
        'whatsapp' => '081234567890',
        'linkedin' => 'https://linkedin.com/in/naqiya',
        'nis' => '12345678',
        'year_of_enrollment' => '2023',
        'graduate_year' => '2027',
        'major' => 'Corporate Entrepreneurship',
        'current_status' => 'Entrepreneur',
        'testimony' => 'Great program!',
        'activities_caption' => 'Updated activities caption',
        'is_visible' => '1',
    ];

    $response = $this->actingAs($admin)
        ->put(route('users.update', $targetUser), $updateData);

    $response->assertStatus(302);
    $response->assertRedirect(route('users.show', $targetUser));

    $this->assertDatabaseHas('users', [
        'id' => $targetUser->id,
        'name' => 'Naqiya Salsabila Yasmien Updated',
        'activities_caption' => 'Updated activities caption',
    ]);
});
