<?php

namespace App\Http\Controllers\Api;

use App\Http\Transformers\UserTransactionResource;
use App\Models\TransferOperation;
use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class WalletController extends Controller
{

    public function current_value(Request $request)
    {
        if (auth()->user()){
            $data = User::select('wallet')->where('id',auth()->user()->id)->first();
            return response()->json(["code" => 200,'data'=>['wallet'=>$data->wallet]],'200');
        }else{
            return response()->json(['code' => 403, 'message' => 'Unauthorized'],'403');
        }//end of else

    }//end of fun

    public function charge_wallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount'   => ['required','numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json(["code" => 400, 'message' => $validator->errors()->first(),], '400');
        }
        if (auth()->user()) {
            $user = User::where('id',auth()->user()->id)->first();
            $user->update([
                'wallet' => $user->wallet+$request->amount,
            ]);

            return response()->json(['code' => 200, 'message' =>'User Wallet Updated Successfully'], '200');
        }
        else{
            return response()->json(['code' => 403, 'message' => 'Unauthorized'],'403');
        }//end of else
    }
}
