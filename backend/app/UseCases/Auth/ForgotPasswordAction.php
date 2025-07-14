<?php

namespace App\UseCases\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ForgotPasswordAction
{
    /**
     * Action for forgetting password users
     * @return void
     */
    public function __invoke(array $data): void
    {
        $email = $data['email'];

        if (User::Where('email', $email)->exists()) {
            Password::sendResetLink(['email' => $email]);
            return;
        }
    }
}
