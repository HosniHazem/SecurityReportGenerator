<?php

namespace App\Http\Controllers;

use App\Models\AppController;
use App\Models\Permission;
use App\Models\Route;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function _construct(){
        $this->middleware('auth:api');
    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function DeletePrivilige( $userId, $controllerId)
    {
        try {
            $user = auth()->user();
            
            if ($user->name !== "Ayed") {
                return response()->json(['message' => 'You are not authorized', 'success' => false]);
            }
            
            // Find the permission
            $permission = Permission::where('userId', $userId)
                                     ->where('controllerId', $controllerId);
                                     
            

            $permission->delete();
    
            return response()->json(['message' => 'Permission revoked successfully', 'success' => true]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'success' => false]);
        }
    }


    public function fillPermissionTable(){
        try {
            $controllers=AppController::all();
            $users=User::all();
            foreach ($controllers as $route) {
                foreach ($users as $user) {
                    $permission=new Permission();
                    $permission->userId=$user->id;
                    $permission->controllerId=$route->id;
                    $permission->save();

                }
            }
            return response()->json(['message'=>'done' ,'success'=>true]);
            
        } catch (\Throwable $th) {
            throw $th;
        }



    }

    public function grantPrivilige($userId, $controllerId){
        try {
            $user = auth()->user();
            
            if ($user->name !== "Ayed") {
                return response()->json(['message' => 'You are not authorized', 'success' => false]);
            }

            $permission = Permission::where('userId', $userId)
                                     ->where('controllerId', $controllerId)->exists();
            
            if($permission){
                return response()->json(['message' => 'Permission already exists', 'success' => false]);
            }

            else {
                $permission = new Permission();
                $permission->userId=$userId;
                $permission->controllerId=$controllerId;
                $permission->save();
                return response()->json(['message' => 'Permission granted successfully', 'success' => true]);
            }





        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'success' => false]);
        }
    }

    public function getAllPermissions(){
        try {





            $permissions = Permission::all();
            
            // Initialize an empty array to store the response
            $response = [];
    
            // Loop through each permission
            foreach ($permissions as $permission) {
                // Find the user and controller associated with the permission
                $controller = AppController::find($permission->controllerId);
                $user = User::find($permission->userId);
    
                // If the user and controller exist, add their data to the response
                if ($user && $controller) {
                    // If the user already exists in the response array, add the controller to their permissions
                    if (isset($response[$user->id])) {
                        $response[$user->id]['controllers'][] = $controller;
                    } else {
                        // Otherwise, create a new entry for the user
                        $response[$user->id] = [
                            'user' => $user,
                            'controllers' => [$controller],
                        ];
                    }
                }
            }
    
            // Return the response as JSON
            return response()->json($response);
    
        } catch (\Throwable $th) {
            // Handle exceptions if necessary
            throw $th;
        }
    }
    


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
