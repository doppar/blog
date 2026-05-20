<?php

namespace App\Models;

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

    public function posts()
    {
        return $this->linkMany(Post::class, 'category_id', 'id');
    }

    public function __active(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    #[\Override]
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
