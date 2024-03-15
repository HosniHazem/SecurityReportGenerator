<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\RegisteredUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use App\Mail\RegistrationEmail; // Replace with your actual Mail class

class AuthController extends Controller
{
    public function _construct(){
        $this->middleware('auth:api',['except'=>['login','register ']]);
    }


    

    //register
    public function register()
{
    
    $users=[];
    $users=RegisteredUser::all()->toArray();

    foreach ($users as $userData) {
        // Check if the user with the same email exists
        $existingUser = User::where('email', $userData['email'])->first();

        if (!$existingUser) {
            $user = User::create(array_merge(
                $userData,
                ['password' => bcrypt($userData['password'])]
            ));

            // $this->sendRegistrationEmail($userData['email'], $userData['password']);
        }
    }

    return response()->json([
        'message' => "Registered successfully",
        'success' => true,
    ], 201);
}



private function sendRegistrationEmail($email, $password)
{
    Mail::raw("Welcome to Your App! Your registration was successful. Your password is: $password", function ( $message) use ($email) {
        $message->to($email)->subject('Welcome to Your App');
    });
    
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
