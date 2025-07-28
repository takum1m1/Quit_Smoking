<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfile\UpdateRequest;
use App\UseCases\UserProfile\CheckAndAwardBadgesAction;
use App\UseCases\UserProfile\GetByIdProfileAction;
use App\UseCases\UserProfile\GetMyProfileAction;
use App\UseCases\UserProfile\ResetQuitInfoAction;
use App\UseCases\UserProfile\UpdateAction;

class UserProfileController extends Controller
{
    public function myProfile(GetMyProfileAction $action)
    {
        // ユーザープロフィールを取得
        $profile = $action();

        // プロフィール情報を返す
        return response()->json($profile, 200);
    }

    public function update(UpdateRequest $req, UpdateAction $action)
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

    public function checkBadges(CheckAndAwardBadgesAction $action)
    {
        // バッジチェックを実行
        $awardedBadges = $action();

        // バッジ情報を返す
        return response()->json([
            'message' => 'バッジチェックが完了しました。',
            'awarded_badges' => $awardedBadges,
        ], 200);
    }
}
