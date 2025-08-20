<?php

namespace App\UseCases\Community;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DeletePostAction
{
    public function __invoke(int $id)
    {
        // 投稿を取得
        $post = Post::findOrFail($id);

        // 投稿の所有者であることを確認
        if ($post->user_id !== Auth::id()) {
            abort(403, '無許可の行為。');
        }

        // 投稿を削除
        $post->delete();

        // 投稿一覧のキャッシュをクリア
        Cache::forget('posts.all');
    }
}
