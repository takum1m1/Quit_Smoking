<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\UseCases\Auth\ForgotPasswordAction;
use App\UseCases\Auth\LoginAction;
use App\UseCases\Auth\LogoutAction;
use App\UseCases\Auth\RegisterAction;
use App\UseCases\Auth\ResetPasswordAction;

class AuthController extends Controller
{
    public function register(RegisterRequest $req, RegisterAction $action)
    {
        //　リクエストのバリデーションはRegisterRequestで行われるため、ここではバリデーション済みのデータを使用します。
        $token = $action($req->validated());
        // ユーザー登録が成功した場合、トークンを返します。
        return response()->json(['message' => 'ユーザー登録が成功しました。', 'token' => $token], 201);
    }

    public function login(LoginRequest $req, LoginAction $action)
    {
        if ($action($req->validated())) {
            // ログインが成功した場合、トークンを返します。
            return response()->json(['message' => 'ログインが成功しました。'], 200);
        }
        // ログインが失敗した場合、エラーメッセージを返します。
        return response()->json(['message' => 'ログインに失敗しました。メールアドレスまたはパスワードが正しくありません。'], 401);
    }

    public function logout(LogoutAction $action)
    {
        $action();
        return response()->json(['message' => 'ログアウトが成功しました'], 200);
    }

    public function forgotPassword(ForgotPasswordRequest $req, ForgotPasswordAction $action)
    {
        $action($req->validated());
        return response()->json(['message' => 'パスワードリセットリンクが送信されました。'], 200);
    }

    public function resetPassword(ResetPasswordRequest $req, ResetPasswordAction $action, string $token)
    {
        // リクエストのバリデーションはResetPasswordRequestで行われるため、ここではバリデーション済みのデータを使用します。
        $action($req->validated(), $token);

        return response()->json(['message' => 'パスワードがリセットされました。'], 200);
    }
}
