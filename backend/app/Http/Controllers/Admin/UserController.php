<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\UseCases\Admin\ListUsersAction;
use App\UseCases\Admin\ShowUserAction;
use App\UseCases\Admin\DestroyUserAction;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(ListUsersAction $action)
    {
        return response()->json($action(), 200);
    }

    public function show($id, ShowUserAction $action)
    {
        return response()->json($action($id), 200);
    }

    public function destroy($id, DestroyUserAction $action)
    {
        $action($id);
        return response()->json(['message' => 'ユーザーは正常に削除されました。'], 200);
    }
}
