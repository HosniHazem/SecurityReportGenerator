<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\RegisteredUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use App\Mail\ConfirmationMail;
use App\Models\AppController;
use App\Models\Permission;

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
        'expires_in' => 60,
        'user' => auth()->user(),
        'success'=>true,
        'message'=>"redirecting to home page"
    ]);


    
}

    public function createUser(Request $request){
        $admin=auth()->user();
        $adminName=$admin->name;
        
        if($adminName !='Ayed') {
            return response()->json(["message"=>"you are not authorized to do this action ",'success'=>false]);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name'=>'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $existingUser = User::where('email', $request->email)->first();
        $user=new User();
        if(!$existingUser){
            $password=self::generatePassword();
            $user->email=$request->email;
            $user->name=$request->name;

            $user->password=bcrypt($password);
            // $message = 'Hello ' . $user->name . ', this is your password: ' . $password;

            Mail::to($user->email)->send(new ConfirmationMail('User Created', $user->name, $password));
            $user->save();

            return response()->json(['message'=>'user created succeffully','success'=>true,$user,'password'=>$password]);

        }
            return response()->json(['message'=>'user already exists','success'=>false]);



        
    }
    public function logout()
{
    auth()->logout();

    return response()->json(['message' => 'Logged out successfully', 'success' => true]);
}



public function profile(){
    try {
        $user = auth()->user();
        $permissions = Permission::where('userId', $user->id)->get();

        if($permissions->isEmpty()){
            return response()->json(['message' => 'Permissions not found', 'success' => false]);
        }

        $controllers = [];
        foreach($permissions as $permission){
            $controller = AppController::where('id',$permission->controllerId)->get()->first();
            if($controller){
                $controllers[] = $controller;
            }
        }
        
        $response = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'controllers' => $controllers,
                'permissions' => $permissions,
            ]
        ];

        return response()->json($response);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage(), 'success' => false]);
    }
}

static function generatePassword(){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString = '';

	for ($i = 0; $i < 12; $i++) {
		$index = rand(0, strlen($characters) - 1);
		$randomString .= $characters[$index];
	}

	return $randomString;

}



}
