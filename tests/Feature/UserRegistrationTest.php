<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_error_if_required_fields_are_missing()
    {
        $response = $this->postJson('/api/register', []); // empty request

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error',
        ]);
    }

    /** @test */
    public function it_returns_error_if_email_is_invalid()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'not-an-email',
            'password' => 'secret123',
            'role' => 'admin',
            'phone' => '9876543210',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error',
        ]);
    }

    /** @test */
    public function it_registers_user_successfully()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
            'role' => 'admin',
            'phone' => '9876543210',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
            'message' => 'User registered successfully',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }
}
