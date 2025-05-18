<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Make sure roles are created before users
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            UsersSeeder::class,
            // Add other seeders here
        ]);
    }
}
