<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserService
{
    /**
     * Get users based on current user's role with filters
     */
    public function getUsers($request = null)
    {
        $currentUser = Auth::user();
        $query = User::with(['roles:id,name']);

        // If not super admin, exclude super admin users
        if (!$currentUser->isSuperAdmin()) {
            $query->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Super Admin');
            });
        }

        // Apply filters if request is provided
        if ($request) {
            $this->applyFilters($query, $request);
        }

        // Apply sorting
        $sortBy = $request ? $request->get('sort_by', 'created_at') : 'created_at';
        $sortOrder = $request ? $request->get('sort_order', 'desc') : 'desc';
        
        // Validate sort fields
        $allowedSortFields = ['id', 'name', 'email', 'status', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }
        
        $query->orderBy($sortBy, $sortOrder);

        return $query->get();
    }

    /**
     * Apply filters to the query
     */
    private function applyFilters($query, $request)
    {
        // Search filter (name or email)
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Role filter
        if ($request->filled('role_id')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('roles.id', $request->get('role_id'));
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }
    }

    /**
     * Create a new user
     */
    public function createUser(array $data)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status' => $data['status'] ?? 'active',
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(),
            ]);

            // Assign role if provided
            if (isset($data['role_id'])) {
                $role = \App\Models\Role::find($data['role_id']);
                if ($role) {
                    $user->assignRole($role->name); // Use role name instead of ID
                }
            }

            DB::commit();
            return $user->load('roles');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update user
     */
    public function updateUser(User $user, array $data)
    {
        // Check if current user can update this user
        if (!$this->canManageUser($user)) {
            throw new \Exception('You do not have permission to update this user.');
        }

        DB::beginTransaction();
        try {
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'status' => $data['status'],
                'updated_by_id' => Auth::id(),
            ];

            // Only update password if provided
            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            // Update role if provided and user has permission
            if (isset($data['role_id']) && $this->canAssignRole($data['role_id'])) {
                // Get the role by ID first, then assign by name or use syncRoles with ID
                $role = \App\Models\Role::find($data['role_id']);
                if ($role) {
                    // Option 1: Assign by role name
                    $user->syncRoles([$role->name]);
                    
                    // Option 2: Or if you prefer to use ID, make sure your Role model supports it
                    // $user->roles()->sync([$data['role_id']]);
                }
            }

            DB::commit();
            return $user->load('roles');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        // Check if current user can delete this user
        if (!$this->canManageUser($user)) {
            throw new \Exception('You do not have permission to delete this user.');
        }

        // Prevent deletion of super admin by non-super admin
        if ($user->hasRole('Super Admin') && !Auth::user()->isSuperAdmin()) {
            throw new \Exception('You cannot delete a Super Admin user.');
        }

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            throw new \Exception('You cannot delete your own account.');
        }

        return $user->delete();
    }

    /**
     * Get available roles for assignment
     */
    public function getAvailableRoles()
    {
        $currentUser = Auth::user();
        $query = \App\Models\Role::query();

        // If not super admin, exclude super admin role
        if (!$currentUser->isSuperAdmin()) {
            $query->where('name', '!=', 'Super Admin');
        }

        return $query->where('status', 'active')->get();
    }

    /**
     * Check if current user can manage the given user
     */
    public function canManageUser(User $user)
    {
        $currentUser = Auth::user();

        // Super admin can manage anyone except themselves for deletion
        if ($currentUser->isSuperAdmin()) {
            return true;
        }

        // Non-super admin cannot manage super admin users
        if ($user->hasRole('Super Admin')) {
            return false;
        }

        // Manager can manage non-super admin users
        if ($currentUser->isManager()) {
            return true;
        }

        // Regular users cannot manage other users
        return false;
    }

    /**
     * Check if current user can assign the given role
     */
    public function canAssignRole($roleId)
    {
        $currentUser = Auth::user();
        
        // Debug logging
        \Log::info('Role assignment check', [
            'current_user_id' => $currentUser->id,
            'current_user_roles' => $currentUser->roles->pluck('name'),
            'role_id_to_assign' => $roleId,
            'is_super_admin' => $currentUser->isSuperAdmin(),
            'is_manager' => $currentUser->isManager()
        ]);
        
        // If no role is being assigned, allow it
        if (!$roleId) {
            \Log::info('No role ID provided, allowing assignment');
            return true;
        }
        
        $role = \App\Models\Role::find($roleId);

        if (!$role) {
            \Log::warning('Role not found', ['role_id' => $roleId]);
            return false;
        }

        \Log::info('Role found', ['role_name' => $role->name]);

        // Super admin can assign any role
        if ($currentUser->isSuperAdmin()) {
            \Log::info('Super admin can assign any role');
            return true;
        }

        // Non-super admin cannot assign super admin role
        if ($role->name === 'Super Admin') {
            \Log::warning('Non-super admin trying to assign Super Admin role');
            return false;
        }

        // Manager can assign non-super admin roles
        if ($currentUser->isManager()) {
            \Log::info('Manager can assign non-super admin roles');
            return true;
        }

        \Log::warning('User does not have permission to assign roles');
        return false;
    }

    /**
     * Get user statistics with filters
     */
    public function getUserStats($request = null)
    {
        $currentUser = Auth::user();
        $query = User::query();

        // If not super admin, exclude super admin users
        if (!$currentUser->isSuperAdmin()) {
            $query->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Super Admin');
            });
        }

        // Apply the same filters as in getUsers for consistent stats
        if ($request) {
            $this->applyFilters($query, $request);
        }

        // Clone query for different counts
        $baseQuery = clone $query;
        $activeQuery = clone $query;
        $inactiveQuery = clone $query;
        $recentQuery = clone $query;

        return [
            'total_users' => $baseQuery->count(),
            'active_users' => $activeQuery->where('status', 'active')->count(),
            'inactive_users' => $inactiveQuery->where('status', 'inactive')->count(),
            'recent_users' => $recentQuery->where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    /**
     * Get filter options for users
     */
    public function getFilterOptions()
    {
        $currentUser = Auth::user();
        
        // Get available roles for filter
        $rolesQuery = \App\Models\Role::query();
        
        // If not super admin, exclude super admin role
        if (!$currentUser->isSuperAdmin()) {
            $rolesQuery->where('name', '!=', 'Super Admin');
        }
        
        $roles = $rolesQuery->where('status', 'active')
                           ->select('id', 'name')
                           ->get();

        // Get status options
        $statusOptions = [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive']
        ];

        // Get sort options
        $sortOptions = [
            ['value' => 'name', 'label' => 'Name'],
            ['value' => 'email', 'label' => 'Email'],
            ['value' => 'status', 'label' => 'Status'],
            ['value' => 'created_at', 'label' => 'Created Date'],
            ['value' => 'updated_at', 'label' => 'Updated Date']
        ];

        return [
            'roles' => $roles->map(function ($role) {
                return [
                    'value' => $role->id,
                    'label' => $role->name
                ];
            }),
            'statuses' => $statusOptions,
            'sort_options' => $sortOptions
        ];
    }
}