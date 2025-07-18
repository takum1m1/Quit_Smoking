<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\UseCases\Community\LikeAction;
use App\UseCases\Community\UnlikeAction;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function likePost(LikeAction $action, $postId)
    {
        $action($postId);
        return response()->json(['message' => '投稿にいいねを押しました。'], 200);
    }

    public function unlikePost(UnlikeAction $action, $postId)
    {
        $action($postId);
        return response()->json(['message' => '投稿のいいねを取り消しました。'], 200);
    }
}
