<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentTransfersTest extends TestCase
{
    use RefreshDatabase;

    public function it_successfully_transfers_money_between_users()
    {

        $userFrom  = User::factory()->create(['wallet' => 100]);
        $userTo   = User::factory()->create();
        $requestData = [
            'email' => $userTo->email,
            'value' => 50,
            'commission' => 5,
            'end_value' => 45,
        ];


        $response = $this->postJson('api/payment/transfers', $requestData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Transfer Operation Successfully']);

        $this->assertDatabaseHas('transactions', [
            'user_from_id' => $userFrom->id,
            'user_to_id' => $userTo->id,
            'value' => 50,
            'commission' => 5,
            'end_value' => 45,
        ]);

        $this->assertEquals(50, $userFrom->refresh()->wallet);
        $this->assertEquals(45, $userTo->refresh()->wallet);
    }

    public function it_returns_error_if_user_not_found()
    {

        $userFrom = User::factory()->create(['wallet' => 100]);
        $requestData = [
            'email' => 'nonexisting@example.com',
            'value' => 50,
            'commission' => 5,
            'end_value' => 45,
        ];


        $response = $this->postJson('api/payment/transfers', $requestData);


        $response->assertStatus(400)
            ->assertJson(['message' => 'User Not Found']);
    }


    public function it_returns_error_if_insufficient_balance()
    {

        $userFrom = User::factory()->create(['wallet' => 20]);
        $userTo = User::factory()->create();
        $requestData = [
            'email' => $userTo->email,
            'value' => 50,
            'commission' => 5,
            'end_value' => 45,
        ];


        $response = $this->postJson('api/payment/transfers', $requestData);


        $response->assertStatus(415)
            ->assertJson(['message' => 'There is not enough balance in the wallet']);
    }

}

