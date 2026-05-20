<?php

namespace App\Models;

use App\Support\AdminDashboardCache;
use Phaseolies\Database\Entity\Attributes\Hook;
use Phaseolies\Database\Entity\Builder;
use Phaseolies\Database\Entity\Model;

class Post extends Model
{
    protected $table = 'posts';

    protected $creatable = [
        'category_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image',
        'author_name',
        'status',
        'is_featured',
        'published_at',
        'view_count',
        'seo_title',
        'seo_description',
    ];

    public function category()
    {
        return $this->bindTo(Category::class, 'id', 'category_id');
    }

    public function tags()
    {
        return $this->bindToMany(Tag::class, 'post_id', 'tag_id', 'post_tag');
    }

    public function __published(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function __draft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function __featured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    #[Hook('before_created')]
    protected function createSlugFromTitle(): void
    {
        $this->slug = str()->slug($this->title);
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
