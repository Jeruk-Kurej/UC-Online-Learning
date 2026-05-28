<?php

use App\Models\Company;
use App\Models\User;

test('admin can approve, reject, or request revision on a company work profile', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $owner = User::factory()->create();
    $company = Company::create([
        'user_id'   => $owner->id,
        'name'      => 'Test Corp Approval',
        'slug'      => 'test-corp-approval',
        'is_visible' => true,
        'approval_status' => 'pending',
    ]);

    // Test Approval
    $response = $this->actingAs($admin)
        ->post(route('intrapreneurs.update-status', $company), [
            'status' => 'approved',
        ]);

    $response->assertRedirect();
    $company->refresh();
    $this->assertEquals('approved', $company->approval_status);
    $this->assertTrue($company->is_visible);

    // Test Rejection
    $response = $this->actingAs($admin)
        ->post(route('intrapreneurs.update-status', $company), [
            'status' => 'rejected',
            'rejection_reason' => 'Incorrect logo',
        ]);

    $response->assertRedirect();
    $company->refresh();
    $this->assertEquals('rejected', $company->approval_status);
    $this->assertEquals('Incorrect logo', $company->rejection_reason);
    $this->assertFalse($company->is_visible);
});

test('non-admin cannot update company approval status', function () {
    $nonAdmin = User::factory()->create(['role' => 'user']);
    $owner = User::factory()->create();
    $company = Company::create([
        'user_id'   => $owner->id,
        'name'      => 'Test Corp Approval Limit',
        'slug'      => 'test-corp-approval-limit',
        'is_visible' => true,
        'approval_status' => 'pending',
    ]);

    $response = $this->actingAs($nonAdmin)
        ->post(route('intrapreneurs.update-status', $company), [
            'status' => 'approved',
        ]);

    $response->assertStatus(403);
    $company->refresh();
    $this->assertEquals('pending', $company->approval_status);
});

test('unapproved companies are hidden from public visible scope', function () {
    $owner = User::factory()->create(['is_visible' => true]);
    
    // Approved & visible
    $company1 = Company::create([
        'user_id'   => $owner->id,
        'name'      => 'Approved Company',
        'slug'      => 'approved-company',
        'is_visible' => true,
        'approval_status' => 'approved',
    ]);

    // Pending & visible
    $company2 = Company::create([
        'user_id'   => $owner->id,
        'name'      => 'Pending Company',
        'slug'      => 'pending-company',
        'is_visible' => true,
        'approval_status' => 'pending',
    ]);

    $visibleCompanies = Company::visible()->get();

    $this->assertTrue($visibleCompanies->contains($company1));
    $this->assertFalse($visibleCompanies->contains($company2));
});
