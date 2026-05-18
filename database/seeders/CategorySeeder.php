<?php

namespace Database\Seeders;

use App\Models\Category;
use Phaseolies\Database\Migration\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        for ($index = 1; $index <= 100; $index++) {
            Category::create([
                'name' => fake()->unique()->words(2, true) . " {$index}",
                'excerpt' => fake()->sentence(),
                'status' => fake()->boolean(85),
            ]);
        }
    }
}
