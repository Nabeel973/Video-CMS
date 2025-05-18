<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permission groups
        $permissionGroups = [
            'user-management' => [
                'user.view' => 'View users',
                'user.create' => 'Create users',
                'user.edit' => 'Edit users',
                'user.delete' => 'Delete users',
            ],
            'role-management' => [
                'role.view' => 'View roles',
                'role.create' => 'Create roles',
                'role.edit' => 'Edit roles',
                'role.delete' => 'Delete roles',
            ],
            'permission-management' => [
                'permission.view' => 'View permissions',
                'permission.create' => 'Create permissions',
                'permission.edit' => 'Edit permissions',
                'permission.delete' => 'Delete permissions',
            ],
            'content-management' => [
                'content.view' => 'View content',
                'content.create' => 'Create content',
                'content.edit' => 'Edit content',
                'content.delete' => 'Delete content',
            ],
            'category-management' => [
                'category.view' => 'View categories',
                'category.create' => 'Create categories',
                'category.edit' => 'Edit categories',
                'category.delete' => 'Delete categories',
            ],
            'tag-management' => [
                'tag.view' => 'View tags',
                'tag.create' => 'Create tags',
                'tag.edit' => 'Edit tags',
                'tag.delete' => 'Delete tags',
            ],
            'genre-management' => [
                'genre.view' => 'View genres',
                'genre.create' => 'Create genres',
                'genre.edit' => 'Edit genres',
                'genre.delete' => 'Delete genres',
            ],
            'release-management' => [
                'release.view' => 'View releases',
                'release.create' => 'Create releases',
                'release.edit' => 'Edit releases',
                'release.delete' => 'Delete releases',
            ],
        ];

        // Create permissions and assign to roles
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $managerRole = Role::where('name', 'Manager')->first();
        $userRole = Role::where('name', 'User')->first();

        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $name => $description) {
                $permission = Permission::create([
                    'name' => $name,
                    'guard_name' => 'api',
                    'description' => $description,
                    'group' => $group
                ]);

                // Assign permissions to roles
                $superAdminRole->givePermissionTo($permission); // Super Admin gets all permissions

                // Manager gets most permissions except some sensitive ones
                if (!in_array($name, ['role.delete', 'permission.delete', 'user.delete'])) {
                    $managerRole->givePermissionTo($permission);
                }

                // Regular users only get view permissions
                if (str_contains($name, '.view')) {
                    $userRole->givePermissionTo($permission);
                }
            }
        }
    }
}