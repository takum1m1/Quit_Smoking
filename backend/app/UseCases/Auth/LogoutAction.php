<?php

namespace App\UseCases\Auth;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LogoutAction
{
    /**
     * ログアウトのアクション
     *
     * @return void
     */
    public function __invoke()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
    }
}
