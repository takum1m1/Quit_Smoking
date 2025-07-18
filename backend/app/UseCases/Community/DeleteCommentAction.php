<?php

namespace App\UseCases\Community;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;

class DeleteCommentAction
{
    public function __invoke($postId, $commentId)
    {
        // コメントを取得
        $comment = Comment::where('post_id', $postId)
            ->where('id', $commentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // コメントを削除
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }
}
