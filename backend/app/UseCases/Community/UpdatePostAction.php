<?php

namespace App\UseCases\Community;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UpdatePostAction
{
    public function __invoke(int $id, array $data)
    {
        // 投稿を取得
        $post = Post::findOrFail($id);

        // 投稿の所有者であることを確認
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 投稿内容を更新
        $post->content = $data['content'];
        $post->save();

        // 関連するモデルのロード
        $post->load(['user.profile', 'comments', 'likes']);

        // 投稿一覧のキャッシュをクリア
        Cache::forget('posts.all');

        return $post;
    }
}
