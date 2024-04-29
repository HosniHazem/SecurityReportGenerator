<?php

namespace App\Http\Middleware;

use App\Models\AppController;
use App\Models\Permission;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $controllerName = $request->route()->getAction()['controller'];

            $controllerName = explode('@', $controllerName)[0]; 
            $controllerName = substr($controllerName, strrpos($controllerName, '\\') + 1);
            $controller = AppController::where('name', $controllerName)->first();
           
            if (!JwtMiddleware::isAuthorized($user, $controller)) {
                return response()->json(['status' => 'Unauthorized access'], 403);
            }

        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['status' => 'Token is Invalid']);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['status' => 'Token is Expired']);
            }else{
                return response()->json(['status' => 'Authorization Token not found']);
            }
        }
        return $next($request);
    }


    public static function isAuthorized($user, $controller): bool {
        try {
            $userId = $user->id;
            $controllerId = $controller->id;
    
            // Retrieve the permission
            $permission = Permission::where('userId', $userId)
                ->where('controllerId', $controllerId)
                ->first();
    
            // If permission is found, user is authorized
            // If permission is not found, user is not authorized
            return $permission !== null;
        } catch (\Throwable $th) {
            // Log or handle the exception
            throw $th;
        }
    }
    
}
