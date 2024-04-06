<?php

use Tests\TestCase;
use App\Models\User;
use App\Models\TransferOperation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransferOperationTest extends TestCase
{
    use RefreshDatabase;

    public function testIncomingTransfers()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $transferOperations = TransferOperation::factory()->count(3)->create([
            'to_user_id' => $user->id,
        ]);

        $response = $this->json('GET', '/api/incoming/transfers');

        $response->assertStatus(200);

        $response->assertJson([
            'code' => 200,
            'data' => $transferOperations->toArray(),
        ]);
    }

    public function testIncomingTransfersForUnauthenticatedUser()
    {
        $response = $this->json('GET', '/api/incoming/transfers');

        $response->assertStatus(400);

        $response->assertJson([
            'code' => 400,
            'message' => 'User Not Found',
        ]);
    }
}
