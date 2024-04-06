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
use Mockery\Exception;

class TransactionController extends Controller
{
    public function user_transactions(Request $request)
    {
        if (auth()->user()){
                $data =  UserTransaction::where('user_id',auth()->user()->id)->latest()->get();
            return response()->json(["code" => 200, 'data' => UserTransactionResource::collection($data)],'200');
        }     else{
            return response()->json(['code' => 403, 'message' => 'Unauthorized'],'403');
        }//end of else


    }//end of fun

    public function inquiry_transfers(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email'=>  ['required', Rule::exists('users', 'email')],
            'value' => ['required','numeric'],
        ]);
        if ($validator->fails()){
            return response()->json(["code" => 500, 'message' => $validator->errors()->first(),], '500');
        }

        if (auth()->user()){
            $user_from = User::where('id',auth()->user()->id)->first();
        }else{
            return response()->json(['code' => 403, 'message' => 'Unauthorized'],'403');
        }//end of else


        if ($request->value){
            if ($user_from->wallet > $request->value){

                ///////// commission /////////////////////////
                $value_commission = (($request->value * 10) / 100) + 2.5;

                if ($request->value > 25 ){
                    $commission=   $value_commission;
                }else{
                    $commission = 0;
                }//end if

                return response()->json(["code"=>200,
                    'data'=>[
                        'email'=>$request->email,
                        'value'=>$request->value,
                        'commission'=>$commission,
                        'end_value'=>$request->value-$commission
                    ]
                ], 200);
            }else{
                return response()->json(['code' => 415, 'message' => 'There is not enough balance in the wallet'],'415');
            }
        }//end if
    }//end of fun

    public function payment_transfers(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(),[
                'email'=>  ['required', Rule::exists('users', 'email')],
                'value'      => ['required','numeric'],
                'commission' => ['required','numeric'],
                'end_value'  => ['required','numeric'],
            ]);
            if ($validator->fails()){
                return response()->json(["code" => 500, 'message' => $validator->errors()->first(),], '500');
            }

            if (auth()->user()){
                $user_from = User::where('id',auth()->user()->id)->first();
            }else{
                return response()->json(['code' => 403, 'message' => 'Unauthorized'],'403');
            }//end of else

            if ($request->email){
                $user_to = User::where('email',$request->email)->first();
            }//end if

            if ($request->value){
                if ($user_from->wallet > $request->value){
                    if ($user_from && $user_to){

                        $commission = $request->commission;
                        $value = $request->value;
                        $end_value = $request->end_value;

                        $this->SaveUserTransaction($user_from,$user_to,$value,$commission,$end_value);

                        return response()->json(["code"=>200,'message' => 'Transfer Operation Successfully'], 200);
                    }else{
                        return response()->json(["code"=>400,'message' => 'User Not Found',], 400);
                    }//end if

                }else{
                    return response()->json(['code' => 415, 'message' =>'There is not enough balance in the wallet'],'415');
                }//end if
            }//end if
            DB::commit();
        }catch (\Exception $ex){
            DB::rollBack();
            return response()->json(["code"=>500,'message' => $ex->getMessage()],'500');
        }


    }//end of fun

    public function incoming_transfers(Request $request)
    {
        if (auth()->user()){
            $transfer_operation =  TransferOperation::with('from_user:id,name')->where('to_user_id',auth()->user()->id)
                ->latest()->get(['id','from_user_id','value','admin_commission']);

            return response()->json(["code"=>200,'data' => $transfer_operation],'200');
        }else{
            return response()->json(["code"=>400,'message' => 'User Not Found',], 400);
        }//end if
    }//end of fun

    public function outgoing_transfers(Request $request)
    {
        if (auth()->user()){
            $transfer_operation =  TransferOperation::with('to_user:id,name')->where('from_user_id',auth()->user()->id)
                ->latest()->get(['id','to_user_id','value','admin_commission']);

            return response()->json(["code"=>200,'data' => $transfer_operation],'200');
        }else{
            return response()->json(["code"=>400,'message' => '.User Not Found',], 400);
        }//end if
    }//end of fun

    public function SaveUserTransaction($user_from,$user_to,$value,$commission,$end_value)
    {
        TransferOperation::create([
            'from_user_id'=>$user_from->id,
            'from_phone'=>$user_from->phone,
            'to_user_id'=>$user_to->id,
            'value'=>$end_value,
            'admin_commission'=>$commission,
        ]);

        $user_from->update(['wallet'=>$user_from->wallet-$value]);

        $user_to->update(['wallet'=>$user_to->wallet+$end_value]);

        //user from transaction
        UserTransaction::create([
            'user_id' => $user_from->id,
            'wallet_balance' => $user_from->wallet + $value,
            'end_wallet_balance' => $user_from->wallet,
            'operation' => " A balance has been shipped " .$end_value ." ج.م " ." to user name "." $user_to->name " . " discount commission  ". $commission ." Wallet balance after transfer ".$user_from->wallet,
            'admin_commission' => $commission,
        ]);

        //user to transaction
        UserTransaction::create([
            'user_id' => $user_to->id,
            'wallet_balance' => $user_to->wallet - $end_value,
            'end_wallet_balance' => $user_to->wallet,
            'operation' => " A balance has been shipped " .$end_value ." ج.م " ." from user name "." $user_from->name " ." Wallet balance after transfer ".$user_to->wallet,
            'admin_commission' => $commission,
        ]);

    }//end of fun

}
