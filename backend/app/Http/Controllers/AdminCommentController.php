<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminCommentController extends Controller
{
    public function index(Request $request)
    {
        // 管理者用のコメント一覧取得処理
    }

    public function show($id)
    {
        // 管理者用のコメント詳細取得処理
    }

    public function destroy($id)
    {
        // 管理者用のコメント削除処理
    }
}
