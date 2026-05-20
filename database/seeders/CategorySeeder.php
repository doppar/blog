<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Support\CmsSlugger;
use Phaseolies\Database\Migration\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Editorial Strategy', 'description' => 'Planning, operations, and publishing workflows.', 'accent_color' => '#5867f9'],
            ['name' => 'Product Notes', 'description' => 'Release stories, roadmap snippets, and changelog style updates.', 'accent_color' => '#0ea5a2'],
            ['name' => 'Culture & Team', 'description' => 'People, rituals, hiring, and internal studio notes.', 'accent_color' => '#f97316'],
            ['name' => 'Growth Journal', 'description' => 'Experiments, distribution learnings, and audience research.', 'accent_color' => '#ec4899'],
            ['name' => 'Design Systems', 'description' => 'Interface decisions, tokens, and content patterns.', 'accent_color' => '#8b5cf6'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => CmsSlugger::unique(Category::class, $category['name']),
                'description' => $category['description'],
                'accent_color' => $category['accent_color'],
                'status' => true,
            ]);
        }
    }
}
