<?php

namespace App\UseCases\Community;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CreatePostAction
{
    public function __invoke(array $data)
    {
        $post = new Post();
        $post->user_id = Auth::id();
        $post->content = $data['content'];
        $post->save();

        $post->load(['user.profile', 'comments', 'likes']);

        // 投稿一覧のキャッシュをクリア
        Cache::forget('posts.all');

        return $post;
    }
}
