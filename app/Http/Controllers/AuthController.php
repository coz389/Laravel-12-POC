<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @group Authentication
 *
 * APIs for managing user registration and login access.
 */
class AuthController extends Controller
{
    /**
     * User Sign Up
     * 
     * This endpoint allows new users to create an account and receive an access token.
     * 
     * @bodyParam name string required The name of the user. Example: John Doe
     * @bodyParam email string required A unique email address. Example: john@example.com
     * @bodyParam password string required Minimum 6 characters. Example: password123
     * 
     * @response 201 {
     *  "status": true,
     *  "message": "User registered successfully",
     *  "token": "1|abc123token...",
     *  "user": {"id": 1, "name": "John Doe", "email": "john@example.com"}
     * }
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => $user
        ], 201);
    }

    /**
     * User Login
     * 
     * Authenticate user credentials and return a Bearer Token.
     * 
     * @bodyParam email string required The user's email. Example: john@example.com
     * @bodyParam password string required The user's password. Example: password123
     * 
     * @response 200 {
     *  "status": true,
     *  "message": "Login successful",
     *  "token": "2|xyz456token..."
     * }
     * @response 401 {
     *  "status": false,
     *  "message": "Invalid login credentials"
     * }
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token
        ], 200);
    }

    /**
     * User Logout
     * 
     * Revoke the current access token to log out the user.
     * 
     * @authenticated
     * @response 200 {
     *  "status": true,
     *  "message": "Logged out successfully"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }
}
