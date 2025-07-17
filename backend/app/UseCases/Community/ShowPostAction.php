<?php

namespace App\UseCases\Community;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class ShowPostAction
{
    public function __invoke()
    {
        // 投稿を取得
        $post = Post::with('user', 'comments', 'likes')
            ->where('id', request()->route('id'))
            ->firstOrFail();

        return $post;
    }
}
