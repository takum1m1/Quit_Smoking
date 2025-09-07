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
     * @return array|null
     * @throws \Exception
     */
    public function __invoke(array $data): ?array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return null; // ログイン失敗
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user->load('profile')
        ];
    }
}
