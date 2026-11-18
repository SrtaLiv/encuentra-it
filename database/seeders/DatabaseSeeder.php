<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CatalogSeeder::class,
            ChallengeSeeder::class,
            ChallengeGuideSeeder::class,
            ResourceSeeder::class,
            QuizSeeder::class,
        ]);
    }
}
