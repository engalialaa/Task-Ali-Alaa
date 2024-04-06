<?php

use Tests\TestCase;
use App\Models\User;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testSaveUserTransaction()
    {
        $userFrom = User::factory()->create(['wallet' => 1000]);
        $userTo = User::factory()->create(['wallet' => 500]);

        $value = 200;
        $commission = 10;
        $endValue = $value - $commission;

        $transactionService = new TransactionController();

        $transactionService->SaveUserTransaction($userFrom, $userTo, $value, $commission, $endValue);

        $this->assertDatabaseHas('transfer_operations', [
            'from_user_id' => $userFrom->id,
            'from_phone' => $userFrom->phone,
            'to_user_id' => $userTo->id,
            'value' => $endValue,
            'admin_commission' => $commission,
        ]);

        $this->assertEquals($userFrom->wallet - $value, $userFrom->fresh()->wallet);
        $this->assertEquals($userTo->wallet + $endValue, $userTo->fresh()->wallet);

        $this->assertDatabaseHas('user_transactions', [
            'user_id' => $userFrom->id,
            'wallet_balance' => $userFrom->wallet + $value,
            'end_wallet_balance' => $userFrom->wallet,
            'admin_commission' => $commission,
        ]);

        $this->assertDatabaseHas('user_transactions', [
            'user_id' => $userTo->id,
            'wallet_balance' => $userTo->wallet - $endValue,
            'end_wallet_balance' => $userTo->wallet,
            'admin_commission' => $commission,
        ]);
    }
}
