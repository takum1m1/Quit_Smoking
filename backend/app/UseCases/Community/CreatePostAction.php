<?php

namespace App\UseCases\Community;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class CreatePostAction
{
    public function __invoke(array $data)
    {
        // 投稿を作成
        $post = new Post();
        $post->user_id = Auth::id();
        $post->content = $data['content'];
        $post->save();

        // 関連するモデルのロード
        $post->load('user', 'comments', 'likes');

        return $post;
    }
}
