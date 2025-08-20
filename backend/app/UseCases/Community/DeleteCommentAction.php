<?php

namespace App\UseCases\Community;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;

class DeleteCommentAction
{
    public function __invoke($postId, $commentId)
    {
        $comment = Comment::where('post_id', $postId)
            ->where('id', $commentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $comment->delete();
    }
}
