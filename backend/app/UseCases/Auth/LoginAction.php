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
    public function __invoke(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return false; // ログイン失敗
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $token;
    }
}
