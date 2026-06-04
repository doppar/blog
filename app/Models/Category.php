<?php

namespace App\Models;

use App\Support\AdminDashboardCache;
use Phaseolies\Database\Entity\Attributes\Hook;
use Phaseolies\Database\Entity\Builder;
use Phaseolies\Database\Entity\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $creatable = [
        'name',
        'slug',
        'description',
        'accent_color',
        'status',
    ];

    #[Hook('before_created')]
    protected function createSlugFromName(): void
    {
        $this->slug = str()->slug($this->name);
    }

    public function posts()
    {
        return $this->linkMany(Post::class, 'category_id', 'id');
    }

    public function __active(Builder $query): Builder
    {
        return $query->where('status', true);
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
