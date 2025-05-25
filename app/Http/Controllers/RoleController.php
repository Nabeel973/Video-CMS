<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:role.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:role.create', ['only' => ['store']]);
        $this->middleware('permission:role.edit', ['only' => ['update']]);
        $this->middleware('permission:role.delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Role::with(['permissions']);
        
        // If user is Manager, exclude Super Admin role
        if ($user->isManager()) {
            $query->where('id', '!=', 1);
        }
        
        $roles = $query->get();
        
        // Add user context to each role
        $roles = $roles->map(function ($role) use ($user) {
            $role->can_edit = $this->canEditRole($user, $role);
            $role->can_delete = $this->canDeleteRole($user, $role);
            $role->can_manage_permissions = $this->canManagePermissions($user, $role);
            return $role;
        });
        
        return response()->json([
            'data' => $roles,
            'message' => 'Roles retrieved successfully'
        ]);
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        // Check if user is super admin
        $user = Auth::user();
      

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
                'status' => $request->status,
                'description' => $request->description,
                'guard_name' => 'api',
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id()
            ]);

            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }

            DB::commit();

            return response()->json([
                'data' => $role,
                'message' => 'Role created successfully'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to create role',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $user = Auth::user();
        
        // Manager cannot view Super Admin role
        if ($user->isManager() && !$user->isSuperAdmin() && $role->name === 'Super Admin') {
            return response()->json([
                'error' => 'Access denied'
            ], 403);
        }
        
        $role->load('permissions');
        
        return response()->json([
            'data' => $role,
            'message' => 'Role retrieved successfully'
        ]);
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $user = Auth::user();
        
        // Check if user can edit this role
        if (!$this->canEditRole($user, $role)) {
            return response()->json([
                'error' => 'You cannot edit this role'
            ], 403);
        }
        
        // Check if user is super admin for status changes
        if ($request->has('status') && $request->status != $role->status && !$user->isSuperAdmin()) {
            return response()->json([
                'error' => 'Only Super Admin can change role status'
            ], 403);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($role->id)
            ],
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        DB::beginTransaction();
        try {
            $role->update([
                'name' => $request->name,
                'status' => $request->status,
                'description' => $request->description,
                'updated_by_id' => Auth::id()
            ]);

            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }

            DB::commit();

            return response()->json([
                'data' => $role,
                'message' => 'Role updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to update role',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        $user = Auth::user();
        
        // Check if user can delete this role
        if (!$this->canDeleteRole($user, $role)) {
            return response()->json([
                'error' => 'You cannot delete this role'
            ], 403);
        }

        try {
            $role->delete();
            
            return response()->json([
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete role',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all permissions for role assignment
     */
    public function getAllPermissions()
    {
        $user = Auth::user();
        
        // Get permissions based on user role
        if ($user->isSuperAdmin() || $user->isManager()) {
            // Super Admin can see all permissions
            $permissions = Permission::all();
        }else {
            // Other roles get limited permissions based on their current permissions
            $permissions = $user->getAllPermissions();
        }
        
        $groupedPermissions = $permissions->groupBy('group');
        
        return response()->json([
            'data' => $groupedPermissions,
            'message' => 'Permissions retrieved successfully'
        ]);
    }

    /**
     * Assign permissions to a role
     */
    public function assignPermissions(Request $request, Role $role)
    {
        $user = Auth::user();
        
        // Check if user can manage permissions for this role
        if (!$this->canManagePermissions($user, $role)) {
            return response()->json([
                'error' => 'You cannot manage permissions for this role'
            ], 403);
        }

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $requestedPermissions = Permission::whereIn('id', $request->permissions)->get();
            
            // If user is Manager, filter out delete permissions
            if ($user->isManager()) {
                $deletePermissions = $requestedPermissions->filter(function ($permission) {
                    return str_contains($permission->name, '.delete');
                });
                
                if ($deletePermissions->isNotEmpty()) {
                    return response()->json([
                        'error' => 'VistroVideo Manager cannot assign delete permissions'
                    ], 403);
                }
            }
            
            $role->syncPermissions($requestedPermissions);
            
            return response()->json([
                'message' => 'Permissions assigned successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to assign permissions',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get permissions for a specific role
     */
    public function getRolePermissions(Role $role)
    {
        $user = Auth::user();
        
        // Check if user can view this role's permissions
        if (!$this->canManagePermissions($user, $role)) {
            return response()->json([
                'error' => 'Access denied'
            ], 403);
        }
        
        $permissions = $role->permissions;
        
        return response()->json([
            'data' => $permissions,
            'message' => 'Role permissions retrieved successfully'
        ]);
    }

    /**
     * Check if user can edit a role
     */
    private function canEditRole($user, $role)
    {
        // Super Admin can edit all roles except their own
        if ($user->isSuperAdmin()) {
            return !$user->hasRole($role->name);
        }
        
        // Manager can edit roles except Super Admin and their own role
        if ($user->isManager()) {
            return $role->name !== 'Super Admin' && !$user->hasRole($role->name);
        }
        
        return false;
    }

    /**
     * Check if user can delete a role
     */
    private function canDeleteRole($user, $role)
    {
        // Super Admin can delete all roles except Super Admin role and their own
        if ($user->isSuperAdmin()) {
            return $role->name !== 'Super Admin' && !$user->hasRole($role->name);
        }
        
        // Manager can delete roles except Super Admin and their own role
        if ($user->isManager()) {
            return $role->name !== 'Super Admin' && !$user->hasRole($role->name);
        }
        
        return false;
    }

    /**
     * Check if user can manage permissions for a role
     */
    private function canManagePermissions($user, $role)
    {
        // Super Admin can manage permissions for all roles except their own
        if ($user->isSuperAdmin()) {
            return !$user->hasRole($role->name);
        }
        
        // Manager can manage permissions for roles except Super Admin and their own role
        if ($user->isManager()) {
            return $role->name !== 'Super Admin' && !$user->hasRole($role->name);
        }
        
        return false;
    }
}