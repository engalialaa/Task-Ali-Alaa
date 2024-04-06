<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


class WalletTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testGetCurrentValueForAuthenticatedUser()
    {
        $this->actingAs($this->user);

        $response = $this->get('/api/current_value');

        $response->assertStatus(200)
            ->assertJson(['code' => 200]);
    }

    public function testChargeWalletForAuthenticatedUser()
    {
        $this->actingAs($this->user);

        $response = $this->post('/api/charge_wallet', ['amount' => 50]);

        $response->assertStatus(200)
            ->assertJson(['code' => 200, 'message' => 'User Wallet Updated Successfully']);

        $this->user->refresh();

        $this->assertEquals(50, $this->user->wallet);
    }

    public function testUnauthorizedAccess()
    {
        $response = $this->get('/api/current_value');

        $response->assertStatus(403);

        $response = $this->post('/api/charge_wallet', ['amount' => 50]);

        $response->assertStatus(403);
    }
}
