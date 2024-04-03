<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class LoginApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a successful login.
     *
     * @return void
     */
    public function testSuccessfulLogin()
    {
        // Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Send a POST request to the login endpoint
        $response = $this->json('POST', '/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'token_type' => 'bearer',
            ]);
    }

    /**
     * Test login with invalid credentials.
     *
     * @return void
     */
    public function testLoginWithInvalidCredentials()
    {
        // Send a POST request to the login endpoint with invalid credentials
        $response = $this->json('POST', '/api/login', [
            'email' => 'invalid@example.com',
            'password' => 'invalidpassword',
        ]);

        // Assert the response
        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
            ]);
    }
}
