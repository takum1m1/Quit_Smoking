<?php

namespace App\UseCases\Admin;

use App\Models\Comment;

class ListCommentsAction
{
    public function __invoke()
    {
        return Comment::with('user', 'post')->get();
    }
}
