<?php

namespace Tests\Unit;

use App\Http\Transformers\UserTransactionResource;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Http\Request;

class TransactionTest extends TestCase
{
    public function testUserTransactionsForAuthenticatedUser()
    {

        $user = User::factory()->create();
        $this->actingAs($user);


        $transactions = UserTransaction::factory()->count(3)->create(['user_id' => $user->id]);


        $request = Request::create('/api/user_transactions', 'GET');


        $response = $this->json('GET', '/api/user_transactions');


        $response->assertStatus(200);


        $response->assertJson([
            'code' => 200,
            'data' => UserTransactionResource::collection($transactions)->toArray($request),
        ]);
    }


    public function testUserTransactionsForUnauthenticatedUser()
    {

        $request = Request::create('/api/user_transactions', 'GET');


        $response = $this->json('GET', '/api/user_transactions');

        $response->assertStatus(403);


        $response->assertJson([
            'code' => 403,
            'message' => 'Unauthorized',
        ]);
    }
}
