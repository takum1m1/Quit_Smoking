<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\UseCases\Profile\GetByIdProfileAction;
use App\UseCases\Profile\GetMyProfileAction;
use App\UseCases\Profile\ResetQuitInfoAction;
use App\UseCases\Profile\UpdateProfileAction;

class UserProfileController extends Controller
{
    public function myProfile(GetMyProfileAction $action)
    {
        // ユーザープロフィールを取得
        $profile = $action();

        // プロフィール情報を返す
        return response()->json($profile, 200);
    }

    public function update(UpdateProfileRequest $req, UpdateProfileAction $action)
    {
        $action($req->validated());

        return response()->json(['message' => 'プロフィールが更新されました。'], 200);
    }

    public function resetQuitInfo(ResetQuitInfoAction $action)
    {
        $action();

        return response()->json(['message' => '禁煙情報がリセットされました。'], 200);
    }

    public function showById(int $id, GetByIdProfileAction $action)
    {
        // 他のユーザーのプロフィールを取得
        $profile = $action($id);

        // プロフィール情報を返す
        return response()->json($profile, 200);
    }
}
