<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class LoginApiTest extends TestCase
{
    use RefreshDatabase;

    public function testSuccessfulLogin()
    {

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);


        $response = $this->json('POST', '/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);


        $response->assertStatus(200)
            ->assertJson([
                'token_type' => 'bearer',
            ]);
    }


    public function testLoginWithInvalidCredentials()
    {

        $response = $this->json('POST', '/api/login', [
            'email' => 'invalid@example.com',
            'password' => 'invalidpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
            ]);
    }
}
