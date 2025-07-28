<?php

namespace App\UseCases\Community;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ListPostsAction
{
    public function __invoke()
    {
        // 投稿一覧をキャッシュから取得（15分間キャッシュ）
        $posts = Cache::remember('posts.all', 900, function () {
            return Post::with(['user.profile', 'comments', 'likes'])->get();
        });

        return $posts;
    }
}
