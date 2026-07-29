<?php

namespace App\Models;

use Phaseolies\Database\Entity\Model;

class CommentLike extends Model
{
    protected $table = 'comment_likes';

    protected $creatable = [
        'comment_id',
        'user_id',
    ];

    protected $unexposable = [];

    /**
     * Get the comment that owns the like.
     */
    public function comment()
    {
        return $this->bindTo(Comment::class, 'id', 'comment_id');
    }

    /**
     * Get the user that owns the like.
     */
    public function user()
    {
        return $this->bindTo(User::class, 'id', 'user_id');
    }
}
