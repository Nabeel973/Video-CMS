<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware('auth:api');
        $this->middleware('permission:user.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:user.create', ['only' => ['store']]);
        $this->middleware('permission:user.edit', ['only' => ['update']]);
        $this->middleware('permission:user.delete', ['only' => ['destroy']]);
        
        $this->userService = $userService;
    }

    /**
     * Display a listing of users with filters
     */
    public function index(Request $request)
    {
        try {
            $users = $this->userService->getUsers($request);
            // $stats = $this->userService->getUserStats($request);

            // Transform users for frontend
            $transformedUsers = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $user->status,
                    'roles' => $user->roles->pluck('name')->join(', '),
                    'role_ids' => $user->roles->pluck('id'),
                    'role_id' => $user->roles->first()?->id,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'can_edit' => $this->userService->canManageUser($user),
                    'can_delete' => $this->userService->canManageUser($user) && $user->id !== Auth::id(),
                ];
            });

            return response()->json([
                'data' => $transformedUsers,
                // 'stats' => $stats,
                'message' => 'Users retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve users',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
            'role_id' => 'required|exists:roles,id'
        ]);

        try {
            // Check if user can assign the requested role
            if (!$this->userService->canAssignRole($request->role_id)) {
                // Get the role name for better error message
                $role = \App\Models\Role::find($request->role_id);
                $roleName = $role ? $role->name : 'this role';
                
                return response()->json([
                    'error' => "You do not have permission to assign the '{$roleName}' role"
                ], 403);
            }

            $user = $this->userService->createUser($request->all());

            return response()->json([
                'data' => $user,
                'message' => 'User created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create user',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        try {
            // Check if current user can view this user
            if (!$this->userService->canManageUser($user)) {
                return response()->json([
                    'error' => 'You do not have permission to view this user'
                ], 403);
            }

            $user->load('roles');

            // Add role_id for form binding
            $userData = $user->toArray();
            $userData['role_id'] = $user->roles->first()?->id;

            return response()->json([
                'data' => $userData,
                'message' => 'User retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve user',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
            'role_id' => 'required|exists:roles,id'
        ]);

        try {
            // Check if user can assign the requested role
            if (!$this->userService->canAssignRole($request->role_id)) {
                // Get the role name for better error message
                $role = \App\Models\Role::find($request->role_id);
                $roleName = $role ? $role->name : 'this role';
                
                return response()->json([
                    'error' => "You do not have permission to assign the '{$roleName}' role"
                ], 403);
            }

            $updatedUser = $this->userService->updateUser($user, $request->all());

            return response()->json([
                'data' => $updatedUser,
                'message' => 'User updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update user',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified user
     */
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
     * Get user statistics with filters
     */
    public function getStats(Request $request)
    {
        try {
            // Get filter parameters for stats
            $filters = [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
                'role_id' => $request->get('role_id'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
            ];

            $stats = $this->userService->getUserStats($filters);

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

    /**
     * Get filter options for users
     */
    public function getFilterOptions()
    {
        try {
            $options = $this->userService->getFilterOptions();

            return response()->json([
                'data' => $options,
                'message' => 'Filter options retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve filter options',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}