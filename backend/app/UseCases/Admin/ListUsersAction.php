<?php

namespace App\UseCases\Admin;

use App\Models\User;

class ListUsersAction
{
    public function __invoke()
    {
        return User::with('profile')->get();
    }
}
