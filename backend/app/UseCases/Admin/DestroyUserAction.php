<?php

namespace App\UseCases\Admin;

use App\Models\User;

class DestroyUserAction
{
    public function __invoke($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
}
