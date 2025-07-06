<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($postId)
    {
        // Logic for displaying all comments for a post
    }

    public function store(Request $request, $postId)
    {
        // Logic for storing a new comment for a post
    }

    public function update(Request $request, $postId, $commentId)
    {
        // Logic for updating a comment
    }

    public function destroy($postId, $commentId)
    {
        // Logic for deleting a comment
    }
}
