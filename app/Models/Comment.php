<?php

namespace App\Models;

use Phaseolies\Database\Entity\Casts\Attributes\ToDateTime;
use Phaseolies\Database\Entity\Model;

class Comment extends Model
{
    protected $table = 'comments';

    protected $creatable = [
        'post_id',
        'user_id',
        'parent_id',
        'body',
        'status',
    ];

    protected $unexposable = [];

    #[ToDateTime]
    protected $created_at;

    #[ToDateTime]
    protected $updated_at;

    /**
     * Get the post that owns the comment.
     */
    public function post()
    {
        return $this->bindTo(Post::class, 'id', 'post_id');
    }

    /**
     * Get the user that owns the comment.
     */
    public function user()
    {
        return $this->bindTo(User::class, 'id', 'user_id');
    }

    /**
     * Get the parent comment.
     */
    public function parent()
    {
        return $this->bindTo(Comment::class, 'id', 'parent_id');
    }

    /**
     * Get the replies for the comment.
     */
    public function replies()
    {
        return $this->linkMany(Comment::class, 'parent_id', 'id');
    }
}
