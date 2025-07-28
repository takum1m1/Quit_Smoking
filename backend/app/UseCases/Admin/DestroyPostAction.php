<?php

namespace App\UseCases\Admin;

use App\Models\Post;

class DestroyPostAction
{
    public function __invoke($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return ['message' => '投稿は正常に削除されました。'];
    }
}
