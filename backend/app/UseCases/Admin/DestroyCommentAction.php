<?php

namespace App\UseCases\Admin;

use App\Models\Comment;

class DestroyCommentAction
{
    public function __invoke($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
    }
}
