<?php

namespace App\Models;

use Phaseolies\Database\Entity\Model;

class SavedPost extends Model
{
    protected $table = 'saved_posts';

    protected $creatable = [
        'post_id',
        'user_id',
    ];

    protected $unexposable = [];

    /**
     * Get the post that was saved.
     */
    public function post()
    {
        return $this->bindTo(Post::class, 'id', 'post_id');
    }

    /**
     * Get the user that saved the post.
     */
    public function user()
    {
        return $this->bindTo(User::class, 'id', 'user_id');
    }
}
