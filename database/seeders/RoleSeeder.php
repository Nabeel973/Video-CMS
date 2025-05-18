<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $superAdmin = Role::create(['name' => 'Super Admin', 'guard_name' => 'api']);
        $manager = Role::create(['name' => 'Manager', 'guard_name' => 'api']);
        $user = Role::create(['name' => 'User', 'guard_name' => 'api']);
        
        // You can assign roles to users here if needed
        // For example, if you want to assign the Super Admin role to a specific user:
        // $adminUser = User::find(1); // Find user with ID 1
        // if ($adminUser) {
        //     $adminUser->assignRole('Super Admin'); // This will use the default guard
        //     // or explicitly specify the guard
        //     $adminUser->assignRole(['name' => 'Super Admin', 'guard_name' => 'api']);
        // }
    }
}