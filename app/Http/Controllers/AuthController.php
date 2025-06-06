<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function login(LoginRequest $request)
    {
        $email = $request->email;
        $ip = $request->ip();
        $key = "login:attempts:" . $ip;

        // Rate limiting: 5 attempts per minute
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Too many login attempts. Try again in {$seconds} seconds.",
            ], 429);
        }

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60);
            return response()->json([
                'message' => 'Invalid login credentials',
                'errors' => [
                    'email' => ['The provided credentials are incorrect.']
                ]
            ], 422);
        }

        RateLimiter::clear($key);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->first_name . ' ' . $user->last_name,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $token = $user?->currentAccessToken();

            // If the user or token is missing, it's likely due to an invalid or expired token
            if (!$user || !$token) {
                return response()->json([
                    'message' => 'Unauthenticated. Invalid or expired token.',
                    'errors' => [
                        'token' => ['No valid access token was provided or it has expired.']
                    ]
                ], 401);
            }

            $token->delete();

            return response()->json([
                'message' => 'Successfully logged out.'
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'message' => 'Logout failed due to a system error.',
                'errors' => [
                    'exception' => [$e->getMessage()]
                ]
            ], 500);
        }
    }

}
