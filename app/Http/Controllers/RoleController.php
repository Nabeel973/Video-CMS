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
        $this->middleware('auth');
        $this->middleware('permission:view roles', ['only' => ['index', 'show']]);
        $this->middleware('permission:create roles', ['only' => ['store']]);
        $this->middleware('permission:edit roles', ['only' => ['update']]);
        $this->middleware('permission:delete roles', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $roles = Role::with(['permissions'])->get();
        
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
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json([
                'error' => 'Only Super Admin can create roles'
            ], 403);
        }

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
        // Check if user is super admin for status changes
        if ($request->has('status') && $request->status != $role->status && !Auth::user()->isSuperAdmin()) {
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
        // Check if user is super admin
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json([
                'error' => 'Only Super Admin can delete roles'
            ], 403);
        }

        // Prevent deletion of Super Admin role
        if ($role->name === 'Super Admin') {
            return response()->json([
                'error' => 'Super Admin role cannot be deleted'
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
        $permissions = Permission::all()->groupBy('group');
        
        return response()->json([
            'data' => $permissions,
            'message' => 'Permissions retrieved successfully'
        ]);
    }

    /**
     * Assign permissions to a role
     */
    public function assignPermissions(Request $request, Role $role)
    {
        // Check if user is super admin
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json([
                'error' => 'Only Super Admin can assign permissions'
            ], 403);
        }

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
            
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
}