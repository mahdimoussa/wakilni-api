<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;

class JWTMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            // Try to authenticate the user with JWT
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token invalid'], 401);
        } catch (JWTException $e) {
            // If JWT authentication fails, try to authenticate the user with the remember token
            $rememberToken = $request->cookie('remember_token');
            if ($rememberToken) {
                $user = User::where('remember_token', $rememberToken)->first();
                if ($user) {
                    JWTAuth::fromUser($user);
                }
            }
        }

        if (!isset($user)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
