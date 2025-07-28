<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\UseCases\Admin\ListCommentsAction;
use App\UseCases\Admin\ShowCommentAction;
use App\UseCases\Admin\DestroyCommentAction;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function index(ListCommentsAction $action)
    {
        return response()->json($action(), 200);
    }

    public function show($id, ShowCommentAction $action)
    {
        return response()->json($action($id), 200);
    }

    public function destroy($id, DestroyCommentAction $action)
    {
        $result = $action($id);
        return response()->json($result, 200);
    }
}
