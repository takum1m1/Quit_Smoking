<?php

namespace App\UseCases\Auth;

use Illuminate\Support\Facades\Auth;

class LogoutAction
{
    /**
     * ログアウトのアクション
     *
     * @return void
     */
    public function __invoke(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        // セッションを無効化し、CSRFトークンを再生成します。
        // これにより、セッションハイジャックのリスクを軽減します。
    }
}
