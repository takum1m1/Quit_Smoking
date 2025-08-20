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
        $posts = $action();
        return response()->json($posts, 200);
    }

    public function show($id, ShowPostAction $action)
    {
        $post = $action($id);
        return response()->json($post, 200);
    }

    public function store(CreatePostRequest $req, CreatePostAction $action)
    {
        $post = $action($req->validated());
        return response()->json($post, 201);
    }

    public function update(UpdatePostRequest $req, UpdatePostAction $action, $id)
    {
        $post = $action($id, $req->validated());
        return response()->json($post, 200);
    }

    public function destroy(DeletePostAction $action, $id)
    {
        $action($id);
        return response()->json(['message' => '投稿は正常に削除されました。'], 200);
    }
}
