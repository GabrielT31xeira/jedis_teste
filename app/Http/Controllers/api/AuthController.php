<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request) : JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $data = $request->all();

            if (Auth::attempt($data)) {
                $user = Auth::user();
                $token = $user->createToken('JedisAPI')->accessToken;
                return response()->json(['token' => $token], 200);
            } else {
                return response()->json(['error' => 'Unauthorised'], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('LaravelAuthApp')->accessToken;

            return response()->json([
                'message' => 'User created successfully',
                'token' => $token->token
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        try {
            if(Auth::guard('api')->check()){
                $accessToken = Auth::guard('api')->user()->token();

                    DB::table('oauth_refresh_tokens')
                        ->where('access_token_id', $accessToken->id)
                        ->update(['revoked' => true]);
                $accessToken->revoke();

                return response()->json([
                    'message' => 'User logout successfully.'
                ],200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function profile() {
        try {
            $user = Auth::guard('api')->user();
            return response()->json([
                'user' => $user
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
