<?php

namespace App\UseCases\Auth;

use App\Exceptions\ResetPasswordException;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ResetPasswordAction
{
    /**
     * パスワード再設定ユーザーのアクション
     * @return void
     */
    public function __invoke(array $data, string $token): void
    {
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
            'password_confirmation' => $data['password_confirmation'],
            'token' => $token,
        ];

        $status = Password::reset(
            $credentials,
            function (User $userAccount, string $password) {
                $userAccount->password = Hash::make($password);
                $userAccount->saveOrFail();
            }
        );
        if ($status !== Password::PASSWORD_RESET) {
            throw new ResetPasswordException();
        }
    }
}
