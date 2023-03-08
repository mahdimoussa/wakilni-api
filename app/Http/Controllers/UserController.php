<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function authenticateUser(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_token' => 'nullable'
        ]);
        try {
            $token = JWTAuth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']]);
            if ($token) {
                if ($request->has('remember_token')) {
                    $user = JWTAuth::user();
                    $rememberToken = Str::random(60);
                    $user->remember_token = $rememberToken;
                    $user->save();
                    $payload = JWTAuth::getPayload($token);
                    $payload->put('remember_token', $rememberToken);
                    $token = JWTAuth::encode($payload);
                }

                return response(['success' => true, 'token' => $token]);
            }
        } catch (JWTException $error) {
            Log::error('auth error', $error);
            return response()->json(['success' => false, 'error' => 'Could not create token']);
        }
        return response()->json(['success' => false, 'error' => 'Invalid credentials']);
    }

    public function register()
    {
        $user = User::create([
            'email' => request('email'),
            'password' => Hash::make(request('password')),
            'name' => request('name'),
        ]);
        Log::info('user registered', ['userId' => $user->id]);
        $token = JWTAuth::attempt(['email' => request('email'), 'password' => request('password')]);
        Log::info('successful register attempt', ['token' => $token]);
        return response(['success' => true, 'token' => $token, 'message' => 'user created']);
    }
}
