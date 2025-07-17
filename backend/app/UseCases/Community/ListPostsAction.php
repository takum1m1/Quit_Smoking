<?php

namespace App\UseCases\Community;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class ListPostsAction
{
    public function __invoke()
    {
        // 投稿一覧を取得
        $posts = Post::with('user', 'likes')->get();

        return $posts;
    }
}
