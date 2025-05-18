<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view permissions', ['only' => ['index', 'show']]);
        $this->middleware('permission:create permissions', ['only' => ['store']]);
        $this->middleware('permission:edit permissions', ['only' => ['update']]);
        $this->middleware('permission:delete permissions', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the permissions.
     */
    public function index()
    {
        $permissions = Permission::all();
        
        return response()->json([
            'data' => $permissions,
            'message' => 'Permissions retrieved successfully'
        ]);
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        // Check if user is super admin
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json([
                'error' => 'Only Super Admin can create permissions'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string',
            'group' => 'required|string|max:255'
        ]);

        try {
            $permission = Permission::create([
                'name' => $request->name,
                'description' => $request->description,
                'group' => $request->group,
                'guard_name' => 'api',
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id()
            ]);
            
            return response()->json([
                'data' => $permission,
                'message' => 'Permission created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create permission',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission)
    {
        return response()->json([
            'data' => $permission,
            'message' => 'Permission retrieved successfully'
        ]);
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        // Check if user is super admin
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json([
                'error' => 'Only Super Admin can update permissions'
            ], 403);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions')->ignore($permission->id)
            ],
            'description' => 'nullable|string',
            'group' => 'required|string|max:255'
        ]);

        try {
            $permission->update([
                'name' => $request