<?php

namespace App\UseCases\Community;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;

class CreateCommentAction
{
    public function __invoke(array $data, $postId)
    {
        // コメントを作成
        $comment = new Comment();
        $comment->user_id = Auth::id();
        $comment->post_id = $postId;
        $comment->content = $data['content'];
        $comment->save();

        // 関連するモデルのロード
        $comment->load('user');

        return $comment;
    }
}
