<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        // 管理者用のユーザー一覧取得処理
    }

    public function show($id)
    {
        // 管理者用のユーザー詳細取得処理
    }

    public function destroy($id)
    {
        // 管理者用のユーザー削除処理
    }
}
