<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\UseCases\Admin\ListPostsAction;
use App\UseCases\Admin\ShowPostAction;
use App\UseCases\Admin\DestroyPostAction;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function index(ListPostsAction $action)
    {
        return response()->json($action(), 200);
    }

    public function show($id, ShowPostAction $action)
    {
        return response()->json($action($id), 200);
    }

    public function destroy($id, DestroyPostAction $action)
    {
        $result = $action($id);
        return response()->json($result, 200);
    }
}
