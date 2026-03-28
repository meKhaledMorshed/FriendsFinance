<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guests cannot access protected routes
     */
    public function test_guests_cannot_access_protected_routes(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * Test that authenticated users can access protected routes
     */
    public function test_authenticated_users_can_access_protected_routes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test that authenticated users cannot access guest routes
     */
    public function test_authenticated_users_cannot_access_guest_routes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test that guests can access registration
     */
    public function test_guests_can_access_registration(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }
}

