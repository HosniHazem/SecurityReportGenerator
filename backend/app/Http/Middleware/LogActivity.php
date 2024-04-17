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
            $body = null; // Default value for body

            // Check if the request method is POST and the URI is api/auth/login
            if ($request->isMethod('post') && $request->is('api/auth/login')) {
                $body = null; // Exclude logging request body for login route
            } else {
                $body = $this->ProcessData($request->all()); // Get the entire request body as an array
            }

            // Log the activity in the database including the request body
            DB::table('activity_log')->insert([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => $action,
                'body' => $body !==  null ? json_encode($body) :  null, // Convert array to JSON string for storage
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $response;
    }
    private function ProcessData($requestData)
    {
        // Array to store image names
        $body = [];

        // Loop through the request data to find uploaded files
        foreach ($requestData as $key => $value) {
            // Check if the value is an uploaded file
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                $body[$key] = 'http://webapp.ssk.lc/AppGenerator/backend/api/show-image/' . $value->getClientOriginalName();

            }
            else {
                $body[$key]=$value;
            }
        }

        return $body;
    }
}
