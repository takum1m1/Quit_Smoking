<?php

namespace App\Http\Controllers;

use App\Http\Requests\Community\CreateCommentRequest;
use App\UseCases\Community\CreateCommentAction;
use App\UseCases\Community\DeleteCommentAction;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(CreateCommentRequest $req, CreateCommentAction $action, $postId)
    {
        $comment = $action($req->validated(), $postId);
        return response()->json($comment, 201);
    }

    public function destroy(DeleteCommentAction $action, $postId, $commentId)
    {
        $action($postId, $commentId);
        return response()->json(['message' => 'コメント削除成功'], 200);
    }
}
