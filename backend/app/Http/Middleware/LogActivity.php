<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogActivity
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check()) {
            $user = Auth::user();
            $action = $request->method() . ' ' . $request->path();

            // Log the activity in the database
            DB::table('activity_log')->insert([
                'user_id' => $user->id,
                'user_name'=>$user->name,
                'action' => $action,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $response;
    }
}
