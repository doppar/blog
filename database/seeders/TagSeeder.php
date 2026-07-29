<?php

namespace Database\Seeders;

use App\Models\Tag;
use Phaseolies\Database\Migration\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Announcements', 'description' => 'Important launches and noteworthy updates.', 'color' => '#7c93ff'],
            ['name' => 'Workflow', 'description' => 'Practical editorial operations and publishing systems.', 'color' => '#2dd4bf'],
            ['name' => 'SEO', 'description' => 'Search strategy, indexing, and content discoverability.', 'color' => '#22c55e'],
            ['name' => 'Analytics', 'description' => 'Numbers, benchmarks, and reporting practices.', 'color' => '#f59e0b'],
            ['name' => 'UI', 'description' => 'Panels, interactions, and visual refinement.', 'color' => '#fb7185'],
            ['name' => 'Writers Room', 'description' => 'Editorial playbooks, voice, and ideation.', 'color' => '#38bdf8'],
            ['name' => 'Roadmap', 'description' => 'Future-facing planning and milestone content.', 'color' => '#a78bfa'],
            ['name' => 'Ops', 'description' => 'Systems, automations, and process decisions.', 'color' => '#94a3b8'],
        ];

        foreach ($tags as $tag) {
            // Tag's before_created hook always regenerates the slug from name,
            // so re-running this seeder would otherwise try to insert the same
            // slug twice. firstOrCreate keeps this idempotent.
            Tag::firstOrCreate(
                ['name' => $tag['name']],
                [
                    'description' => $tag['description'],
                    'color' => $tag['color'],
                ]
            );
        }
    }
}
