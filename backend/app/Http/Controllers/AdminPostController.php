<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminPostController extends Controller
{
    public function index(Request $request)
    {
        // 管理者用の投稿一覧取得処理
    }

    public function show($id)
    {
        // 管理者用の投稿詳細取得処理
    }

    public function destroy($id)
    {
        // 管理者用の投稿削除処理
    }
}
