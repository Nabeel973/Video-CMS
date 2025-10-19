<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $userId = $adminUser ? $adminUser->id : 1;

        $tags = [
            'Action',
            'Comedy',
            'Drama',
            'Thriller',
            'Horror',
            'Romance',
            'Sci-Fi',
            'Adventure'
        ];

        foreach ($tags as $tagName) {
            Tag::updateOrCreate(
                ['name' => $tagName],
                [
                    'status' => 'active',
                    'created_by' => $userId,
                    'updated_by' => $userId
                ]
            );
        }

        $this->command->info('Tags seeded successfully!');
    }
}
