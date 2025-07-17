<?php

namespace App\Http\Controllers;

use App\Http\Requests\Community\CreatePostRequest;
use App\Http\Requests\Community\UpdatePostRequest;
use App\UseCases\Community\CreatePostAction;
use App\UseCases\Community\DeletePostAction;
use App\UseCases\Community\ListPostsAction;
use App\UseCases\Community\ShowPostAction;
use App\UseCases\Community\UpdatePostAction;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(ListPostsAction $action)
    {
        // 投稿一覧を取得
        $posts = $action();
        return response()->json($posts, 200);
    }

    public function show($id, ShowPostAction $action)
    {
        // 特定の投稿を取得
        $post = $action($id);
        return response()->json($post, 200);
    }

    public function store(CreatePostRequest $req, CreatePostAction $action)
    {
        $post = $action($req->validated());
        return response()->json($post, 201);
    }

    public function update(UpdatePostRequest $request, UpdatePostAction $action, $id)
    {
        $post = $action($id, $request->validated());
        return response()->json($post, 200);
    }

    public function destroy(DeletePostAction $action, $id)
    {
        return $action($id);
        return response()->json(['message' => '投稿は正常に削除されました。'], 200);
    }
}
