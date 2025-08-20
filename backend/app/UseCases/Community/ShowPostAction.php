<?php

namespace App\UseCases\Community;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class ShowPostAction
{
    public function __invoke(int $id)
    {
        $post = Post::with(['user', 'comments', 'likes'])->findOrFail($id);
        return $post;
    }
}
