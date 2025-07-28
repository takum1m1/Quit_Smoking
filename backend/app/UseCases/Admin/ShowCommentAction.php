<?php

namespace App\UseCases\Admin;

use App\Models\Comment;

class ShowCommentAction
{
    public function __invoke($id)
    {
        return Comment::with('user', 'post')->findOrFail($id);
    }
}
