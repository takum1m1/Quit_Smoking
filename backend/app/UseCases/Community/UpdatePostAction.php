<?php

namespace App\UseCases\Community;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UpdatePostAction
{
    public function __invoke(int $id, array $data)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $post->content = $data['content'];
        $post->save();

        $post->load(['user.profile', 'comments', 'likes']);

        Cache::forget('posts.all');

        return $post;
    }
}
