<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'data' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah',
                'data' => [],
            ], 401);
        }

        // Create a new token for the user
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        $user->load('village.district');

        return response()->json([
            'message' => 'Success',
            'data' => [
                'user' => $user,
                'access_token' => $token,
            ],
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user = $user->load('village.district');

        return response()->json([
            'message' => 'Success',
            'data' => $user,
        ]);
    }

    /**
     * Logout the user (revoke the token).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Get the current authenticated user
        $user = Auth::user();

        // Get the token ID from the current request
        $tokenId = $request->user()->currentAccessToken()->id;

        // Revoke the token
        PersonalAccessToken::find($tokenId)?->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}
