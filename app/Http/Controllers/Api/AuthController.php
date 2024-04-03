<?php

namespace App\Http\Controllers\Api;

use App\Http\Transformers\AuthCollectionResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                 =>'required|string|min:3',
            'email'                 =>'required|email|unique:users',
            'password'              =>'required|min:6',
            'password_confirmation' => 'required_with:password|same:password|min:6'
        ]);

        if ($validator->fails()){return response()->json(['message' => $validator->errors()->first(), "code"=>400,],'400');}


        $data = $request->only('name','email','password');
        $user = User::create($data);

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->token = $token;

        return response()->json(["code"=>200,'message' => 'Successfully Registered','data'=>AuthCollectionResource::make($user)],'200');


    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'             =>'required|email',
            'password'          => 'required|min:6',
        ]);

        if ($validator->fails()){return response()->json(['message' => $validator->errors()->first(),"code"=>400,],'400');}

        $validator = Validator::make($request->all(), [
            'email'  => [ Rule::exists('users', 'email')],
        ]);
        if ($validator->fails()){return response()->json(['message' => $validator->errors()->first(),"code"=>400,],'400');}


        $credentials = request(['email','password']);
        if(!auth()->attempt($credentials)){
            return response()->json(["code"=>400,'message'=> 'User Invalid','data'=>[]],400);
        }

        $user = User::where('email',$request->email)->first();
        $user->update(['online'=>'yes']);
        $authToken = $user->createToken('auth_token')->plainTextToken;
        $user->token = $authToken;

        return response()->json(["code"=>200,'message' => 'Successfully Registered','data'=>AuthCollectionResource::make($user)],'200');

    }
}
