<?php

namespace App\UseCases\Community;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UnlikeAction
{
    public function __invoke($postId)
    {
        $user = Auth::user();

        $post = Post::findOrFail($postId);

        $like = $post->likes()->where('user_id', $user->id)->first();

        if (!$like) {
            throw new \Exception('あなたはこの投稿に「いいね！」を押していません。');
        }

        $like->delete();
    }
}
