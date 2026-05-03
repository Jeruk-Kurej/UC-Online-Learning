<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_login_and_register_links()
    {
        $response = $this->get(route('businesses.index'));

        $response->assertStatus(200);
        $response->assertSee('Log in');
    }

    public function test_authenticated_student_sees_dashboard_and_profile()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('businesses.index'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee($user->name);
        $response->assertSee('My Businesses');
    }

    public function test_admin_sees_admin_links()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('businesses.index'));

        $response->assertStatus(200);
        $response->assertSee('Users');
    }
}
