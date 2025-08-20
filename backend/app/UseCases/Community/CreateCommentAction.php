<?php

namespace App\UseCases\Community;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;

class CreateCommentAction
{
    public function __invoke(array $data, $postId)
    {
        $post = Post::findOrFail($postId);

        $comment = new Comment();
        $comment->user_id = Auth::id();
        $comment->post_id = $postId;
        $comment->content = $data['content'];
        $comment->save();

        $comment->load('user');

        return $comment;
    }
}
