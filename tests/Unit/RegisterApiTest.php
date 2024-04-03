<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a successful registration.
     *
     * @return void
     */
    public function testSuccessfulRegistration()
    {
        // Send a POST request to the register endpoint
        $response = $this->json('POST', '/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Assert the response
        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    // Add more fields as needed
                ],
                'access_token',
            ]);
    }

    /**
     * Test registration with missing required fields.
     *
     * @return void
     */
    public function testRegistrationWithMissingFields()
    {
        // Send a POST request to the register endpoint with missing fields
        $response = $this->json('POST', '/api/register', [
            // Missing required fields
        ]);

        // Assert the response
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
