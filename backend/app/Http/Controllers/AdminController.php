<?php

namespace App\Http\Controllers;

use App\Models\AppController;
use App\Models\Permission;
use App\Models\Route;
use App\Models\User;
use Illuminate\Http\Request;

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
                                     ->where('controllerId', $controllerId)
                                     ->firstOrFail();
            
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
