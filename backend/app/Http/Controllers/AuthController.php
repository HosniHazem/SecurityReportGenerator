<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function _construct(){
        $this->middleware('auth:api',['except'=>['login','register ']]);
    }



    //register
    public function register (Request $request){
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|string|email|unique:users',
            'password'=>'required|string|confirmed|min:6',
        ]);
        if ($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()->toJson(),
                'success' => false,
            ]);
            
        }
        $user=User::create(array_merge(
            $validator->validated(),
            ['password' =>bcrypt($request->password)]
        ));
        return response()->json([
            'message' =>"registered successfully",
            'success'=>true,
            'user' =>$user
        ],201);
}

public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string|min:6',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors()->toJson(), 400);
    }

    $credentials = [
        'email' => $request->input('email'),
        'password' => $request->input('password'),
    ];

    if (!$token = auth()->attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized', 'success' => false]);
    }

    return $this->createNewToken($token);
}

    public function createNewToken($token){
    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => \Illuminate\Support\Facades\Auth::factory()->getTTL() * 60,
        'user' => auth()->user(),
        'success'=>true,
        'message'=>"redirecting to home page"
    ]);


    
}

public function profile(){
    return response()->json(auth()->user());
}



}
