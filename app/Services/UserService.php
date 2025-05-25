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
     * Get users based on current user's role
     */
    public function getUsers()
    {
        $currentUser = Auth::user();
        $query = User::with(['roles:id,name']);

        // If not super admin, exclude super admin users
        if (!$currentUser->isSuperAdmin()) {
            $query->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Super Admin');
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
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
                $user->assignRole($data['role_id']);
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
                $user->syncRoles([$data['role_id']]);
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
     * Get user statistics
     */
    public function getUserStats()
    {
        $currentUser = Auth::user();
        $query = User::query();

        // If not super admin, exclude super admin users
        if (!$currentUser->isSuperAdmin()) {
            $query->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Super Admin');
            });
        }

        return [
            'total_users' => $query->count(),
            'active_users' => $query->where('status', 'active')->count(),
            'inactive_users' => $query->where('status', 'inactive')->count(),
            'recent_users' => $query->where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }
}