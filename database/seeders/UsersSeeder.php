<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'api']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'api']);
        $userRole = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'api']);
        
        
     
        // // Assign permissions to User role
        // $userRole->givePermissionTo([
        //     'view videos',
        // ]);
        
        // No need to assign permissions to Super Admin role
        // The isSuperAdmin() method in User model will handle this
        
        // Create admin user
        $superAdmin = User::create([
            'name' => 'Super Admin User',
            'email' => 'super_admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => 1,
        ]);
        $superAdmin->assignRole($superAdminRole);
        
        // Create manager user
        $manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role_id' => 2,
        ]);
        $manager->assignRole($managerRole);
        
        // Create regular users
        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
            'role_id' => 3,
        ]);
        $user1->assignRole($userRole);
        
        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
            'role_id' => 3,
        ]);
        $user2->assignRole($userRole);
        
        $user3 = User::create([
            'name' => 'User Three',
            'email' => 'user3@example.com',
            'password' => Hash::make('password'),
            'role_id' => 3,
        ]);
        $user3->assignRole($userRole);
        
        $user4 = User::create([
            'name' => 'User Four',
            'email' => 'user4@example.com',
            'password' => Hash::make('password'),
            'role_id' => 3,
        ]);
        $user4->assignRole($userRole);
    }
}
