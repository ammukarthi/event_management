<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_error_if_required_fields_are_missing()
    {
        $response = $this->postJson('/api/login', []); // empty request

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error',
        ]);
    }

    /** @test */
    public function it_returns_error_if_credentials_are_invalid()
    {
        // Create a user
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'phone' => '9876543210',
        ]);

        // Try wrong password
        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Invalid credentials',
        ]);
    }

    /** @test */
    public function it_logs_in_successfully_and_returns_token()
    {
        // Create a user
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'phone' => '9876543210',
        ]);

        // Attempt login with correct credentials
        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'token',
            'user' => [
                'id',
                'name',
                'email',
                'role',
                'phone',
            ],
        ]);
    }
}
