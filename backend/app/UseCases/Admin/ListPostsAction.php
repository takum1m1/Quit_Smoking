<?php

namespace App\UseCases\Admin;

use App\Models\Post;

class ListPostsAction
{
    public function __invoke()
    {
        return Post::with('user', 'comments', 'likes')->get();
    }
}
