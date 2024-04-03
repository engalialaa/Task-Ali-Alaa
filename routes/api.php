<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/////////register routes//////
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::group(['middleware' => ['auth:sanctum']], function () {

    /////////////////////wallet Routes/////////////////////////////////
    Route::get('current_value', [WalletController::class, 'current_value']);
    Route::post('charge_wallet', [WalletController::class, 'charge_wallet']);

    /////////////////////transactions Routes/////////////////////////////
    Route::post('inquiry/transfers', [TransactionController::class, 'inquiry_transfers']);
    Route::post('payment/transfers', [TransactionController::class, 'payment_transfers']);
    Route::get('incoming/transfers', [TransactionController::class, 'incoming_transfers']);
    Route::get('outgoing/transfers', [TransactionController::class, 'outgoing_transfers']);
    Route::get('user_transactions', [TransactionController::class, 'user_transactions']);


});
