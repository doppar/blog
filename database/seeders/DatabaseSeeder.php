<?php

namespace Database\Seeders;

use Phaseolies\Database\Migration\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
            LikeSeeder::class,
        ]);
    }
}
