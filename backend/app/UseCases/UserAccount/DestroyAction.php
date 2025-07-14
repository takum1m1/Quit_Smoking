<?php

namespace App\UseCases\UserAccount;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DestroyAction
{
    public function __invoke(User $user)
    {
        // ユーザーを削除
        $user->delete();
    }
}
