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
        $profile = $action();

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
        $profile = $action($id);

        return response()->json($profile, 200);
    }

    public function checkBadges(CheckAndAwardBadgesAction $action)
    {
        $awardedBadges = $action();

        return response()->json([
            'message' => 'バッジチェックが完了しました。',
            'awarded_badges' => $awardedBadges,
        ], 200);
    }
}
