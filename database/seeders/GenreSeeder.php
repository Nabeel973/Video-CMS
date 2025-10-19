<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $userId = $adminUser ? $adminUser->id : 1;

        $genres = [
            'Action',
            'Comedy',
            'Drama',
            'Thriller',
            'Horror',
            'Romance',
            'Sci-Fi',
            'Fantasy',
            'Documentary'
        ];

        foreach ($genres as $genreName) {
            Genre::updateOrCreate(
                ['name' => $genreName],
                [
                    'status' => 'active',
                    'created_by' => $userId,
                    'updated_by' => $userId
                ]
            );
        }

        $this->command->info('Genres seeded successfully!');
    }
}
