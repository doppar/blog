<?php

namespace App\Models;

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

    #[\Override]
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
