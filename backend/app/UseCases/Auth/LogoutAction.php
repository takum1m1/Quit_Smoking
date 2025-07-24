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
    public function __invoke()
    {
        Auth::user()->currentAccessToken()->delete();
    }
}
