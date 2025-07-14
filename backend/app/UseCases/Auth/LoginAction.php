<?php

namespace App\UseCases\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginAction
{
    /**
     * Execute the login action.
     *
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function __invoke(array $data): bool
    {
        if (Auth::guard('web')->attempt($data)) {
            session()->regenerate();
            return true;
        }
        return false;
    }
}
