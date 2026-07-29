<?php

namespace App\Models;

use Phaseolies\Database\Entity\Model;

class Like extends Model
{
    protected $table = 'likes';

    protected $creatable = [
        'post_id',
        'user_id',
    ];

    protected $unexposable = [];

    /**
     * Get the post that owns the like.
     */
    public function post()
    {
        return $this->bindTo(Post::class, 'id', 'post_id');
    }

    /**
     * Get the user that owns the like.
     */
    public function user()
    {
        return $this->bindTo(User::class, 'id', 'user_id');
    }
}
