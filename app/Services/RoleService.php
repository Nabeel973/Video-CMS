<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleService
{
    public function getAll(): Collection
    {
        return Role::with('permissions')->get();
    }

    public function getPaginated(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        $query = Role::with('permissions');

        // Add search functionality
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('guard_name', 'LIKE', "%{$search}%")
                  ->orWhereHas('permissions', function ($subQuery) use ($search) {
                      $subQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): Role
    {
        return Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);
    }

    public function update(Role $role, array $data): bool
    {
        return $role->update([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);
    }

    public function delete(Role $role): bool
    {
        return $role->delete();
    }

    public function findById(int $id): ?Role
    {
        return Role::with('permissions')->find($id);
    }

    public function validateData(array $data, ?int $roleId = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'guard_name' => 'nullable|string|max:255',
        ];

        // If we have an ID, it's an update, so exclude current record from unique check
        if ($roleId) {
            $rules['name'] .= '|unique:roles,name,' . $roleId;
        } else {
            $rules['name'] .= '|unique:roles,name';
        }

        return validator($data, $rules)->validate();
    }

    public function getAllPermissions(): Collection
    {
        return Permission::all();
    }

    public function assignPermissions(Role $role, array $permissionIds): bool
    {
        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $role->syncPermissions($permissions);
        return true;
    }

    public function getRolePermissions(Role $role): Collection
    {
        return $role->permissions;
    }
}