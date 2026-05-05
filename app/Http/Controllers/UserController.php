<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * @group User Management
 * @authenticated
 * 
 * APIs for viewing and managing user data.
 */
class UserController extends Controller
{
    /**
     * Get User List
     * 
     * Returns a paginated list of users.
     * 
     * @queryParam page int The page number. Example: 1
     */
    public function index()
    {
        $users = User::latest()->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'User list fetched successfully',
            'data' => $users
        ]);
    }

    /**
     * Create User
     * 
     * Add a new user to the database.
     * 
     * @bodyParam name string required The name of the user. Example: John Doe
     * @bodyParam email string required A unique email address. Example: john@example.com
     * @bodyParam password string required Minimum 6 characters. Example: secret123
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Get Individual User
     * 
     * Fetch details of a specific user.
     * 
     * @urlParam user int required The ID of the user. Example: 1
     */
    public function show(User $user)
    {
        // Route Model Binding automatically returns 404 if user not found
        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }

    /**
     * Update User
     * 
     * Update existing user information.
     * 
     * @urlParam user int required The ID of the user. Example: 1
     * @bodyParam name string The name of the user.
     * @bodyParam email string A unique email address.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|min:6',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Delete User
     * 
     * Remove a user from the system.
     * 
     * @urlParam user int required The ID of the user. Example: 1
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
