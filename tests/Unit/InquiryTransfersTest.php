<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Request;

class InquiryTransfersTest extends TestCase
{
    public function testInquiryTransfersWithValidInputAndSufficientBalance()
    {

        $user = User::factory()->create(['wallet' => 100]);
        $this->actingAs($user);

        $request = new Request(['email' => $user->email, 'value' => 50]);

        $response = $this->postJson('/api/inquiry/transfers', $request->all());

        $response->assertStatus(200);

        $response->assertJson([
            'code' => 200,
            'data' => [
                'email' => $user->email,
                'value' => 50,
                'commission' => 7.5, // commission calculation for $value > 25
                'end_value' => 42.5, // $value - $commission
            ],
        ]);
    }


    public function testInquiryTransfersWithInvalidInput()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request = new Request(['email' => $user->email, 'value' => 'abc']);

        $response = $this->postJson('/api/inquiry/transfers', $request->all());

        $response->assertStatus(500);

        $response->assertJson([
            'code' => 500,
            'message' => 'The value must be a number.',
        ]);
    }
}
