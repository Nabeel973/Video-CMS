<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MovieMetadataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder will populate all movie-related metadata tables.
     */
    public function run(): void
    {
        $this->command->info('Starting to seed movie metadata...');
        
        $this->call([
            GenreSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
        ]);
        
        $this->command->info('Movie metadata seeded successfully!');
    }
}
