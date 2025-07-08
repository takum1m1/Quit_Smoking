<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        // Logic for storing a new comment for a post
    }

    public function destroy($postId, $commentId)
    {
        // Logic for deleting a comment
    }
}
