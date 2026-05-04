<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Get(path: "/api/users", summary: "List Users", tags: ["Users"])]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(
            allOf: [
                new OA\Schema(ref: "#/components/schemas/PaginatedResponse"),
                new OA\Schema(properties: [
                    new OA\Property(property: "data", properties: [
                        new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/User"))
                    ])
                ])
            ]
        )
    )]
    public function index()
    {
        $users = User::latest()->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'User list fetched successfully',
            'data' => $users
        ]);
    }

    
    #[OA\Post(path: "/api/users", summary: "Create User", tags: ["Users"])]
    #[OA\RequestBody(content: new OA\JsonContent(ref: "#/components/schemas/User"))] // reused!
    #[OA\Response(response: 201, description: "Created")]
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

    #[OA\Get(
        path: "/api/users/{id}",
        summary: "Get single user",
        tags: ["Users"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "User found"),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }

    #[OA\Put(
        path: "/api/users/{id}",
        summary: "Update user",
        tags: ["Users"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "email", type: "string"),
                    new OA\Property(property: "password", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Updated"),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => "sometimes|required|email|unique:users,email,$id",
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

    #[OA\Delete(
        path: "/api/users/{id}",
        summary: "Delete user",
        tags: ["Users"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Deleted"),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
