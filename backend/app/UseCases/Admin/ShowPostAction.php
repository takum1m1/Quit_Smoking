<?php

namespace App\UseCases\Admin;

use App\Models\Post;

class ShowPostAction
{
    public function __invoke($id)
    {
        return Post::with('user', 'comments', 'likes')->findOrFail($id);
    }
}
