<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $userId = $adminUser ? $adminUser->id : 1;

        $categories = [
            'Featured',
            'Trending',
            'New Release',
            'Classic',
            'Popular'
        ];

        foreach ($categories as $categoryName) {
            Category::updateOrCreate(
                ['name' => $categoryName],
                [
                    'status' => 'active',
                    'created_by' => $userId,
                    'updated_by' => $userId
                ]
            );
        }

        $this->command->info('Categories seeded successfully!');
    }
}
