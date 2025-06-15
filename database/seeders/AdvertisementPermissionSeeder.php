<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdvertisementPermissionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create advertisement permissions
        $permissions = [
            'advertisement.view',
            'advertisement.create',
            'advertisement.edit',
            'advertisement.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // You can also assign specific permissions to other roles

        $managerRole = Role::where('name', 'Manager')->first();
        if ($managerRole) {
            $managerRole->givePermissionTo([
                'advertisement.view',
                'advertisement.create',
                'advertisement.edit'
            ]);
        }
    }
}