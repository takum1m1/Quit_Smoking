<?php

namespace App\Exceptions;

use Exception;

class ResetPasswordException extends Exception
{
    protected $message = 'パスワードリセットに失敗しました。';
}
