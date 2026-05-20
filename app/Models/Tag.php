<?php

namespace App\Models;

use App\Support\AdminDashboardCache;
use Phaseolies\Database\Entity\Attributes\Hook;
use Phaseolies\Database\Entity\Model;

class Tag extends Model
{
    protected $table = 'tags';

    protected $creatable = [
        'name',
        'slug',
        'description',
        'color',
    ];

    public function posts()
    {
        return $this->bindToMany(Post::class, 'tag_id', 'post_id', 'post_tag');
    }

    #[Hook('after_created')]
    #[Hook('after_deleted')]
    protected function clearDashboardCountCache(): void
    {
        AdminDashboardCache::forgetCounts();
    }

    #[\Override]
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
