<?php

namespace App\UseCases\Admin;

use App\Models\User;

class ShowUserAction
{
    public function __invoke($id)
    {
        return User::with('profile')->findOrFail($id);
    }
}
