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
use App\UseCases\UserAccount\DestroyAction;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller

{
    public function register(RegisterRequest $req, RegisterAction $action)
    {
        $token = $action($req->validated());
        return response()->json(['message' => 'ユーザー登録が成功しました。', 'token' => $token], 201);
    }

    public function login(LoginRequest $req, LoginAction $action)
    {
        if ($token = $action($req->validated())) {
            return response()->json(['message' => 'ログインが成功しました。', 'token' => $token], 200);
        }
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
        $action($req->validated(), $token);
        return response()->json(['message' => 'パスワードがリセットされました。'], 200);
    }

    public function destroy(DestroyAction $action)
    {
        $action(Auth::user());
        return response()->json(['message' => 'アカウントが削除されました。'], 200);
    }
}
