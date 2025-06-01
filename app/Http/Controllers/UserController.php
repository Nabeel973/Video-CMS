<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        
        $users = $this->userService->getPaginated($perPage, $search);
        
        // Transform users data to include roles as string
        $transformedUsers = $users->getCollection()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->join(', '),
                'role_ids' => $user->roles->pluck('id')->toArray(),
                'status' => $user->status ?? 'inactive',
                'created_at' => $user->created_at,
                'can_edit' => true,
                'can_delete' => true,
            ];
        });
        
        return response()->json([
            'data' => $transformedUsers,
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total()
            ]
        ]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'data' => $user->load('roles')
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $this->userService->validateData($request->all());
            $user = $this->userService->create($validatedData);

            return response()->json([
                'message' => 'User created successfully',
                'data' => $user->load('roles')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating user',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function update(Request $request, User $user): JsonResponse
    {
        try {
            $validatedData = $this->userService->validateData($request->all(), $user->id);
            $this->userService->update($user, $validatedData);

            return response()->json([
                'message' => 'User updated successfully',
                'data' => $user->fresh('roles')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating user',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(User $user)
    {
        try {
            $this->userService->deleteUser($user);

            return response()->json([
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete user',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available roles for user assignment
     */
    public function getRoles()
    {
        try {
            $roles = $this->userService->getAvailableRoles();

            // Debug logging
            \Log::info('Available roles for assignment', [
                'roles' => $roles->toArray(),
                'current_user' => Auth::user()->roles->pluck('name')
            ]);

            return response()->json([
                'data' => $roles,
                'message' => 'Roles retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve roles',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics
     */
    public function getStats()
    {
        try {
            $stats = $this->userService->getUserStats();

            return response()->json([
                'data' => $stats,
                'message' => 'User statistics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve user statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}