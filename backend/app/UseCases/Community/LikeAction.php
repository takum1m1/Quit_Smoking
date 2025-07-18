<?php

namespace App\UseCases\Community;

use App\Models\Post;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeAction
{
    public function __invoke($postId)
    {
        $user = Auth::user();

        $post = Post::findOrFail($postId);

        if ($post->likes()->where('user_id', $user->id)->exists()) {
            throw new \Exception('あなたはすでにこの投稿に「いいね！」を押しています。');
        }

        $like = new Like();
        $like->user_id = $user->id;
        $like->post_id = $post->id;
        $like->saveOrFail();
    }
}
