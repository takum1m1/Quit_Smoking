<?php

namespace App\UseCases\UserProfile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UpdateAction
{
    public function __construct(
        private CheckAndAwardBadgesAction $checkAndAwardBadgesAction
    ) {}

    /**
     * Execute the action to update the user's profile.
     *
     * @param array $data
     * @return void
     */
    public function __invoke(array $data): void
    {
        $user = Auth::user();

        $userProfile = $user->profile;

        // 部分的な更新に対応
        if (isset($data['display_name'])) {
            $userProfile->display_name = $data['display_name'];
        }
        if (isset($data['daily_cigarettes'])) {
            $userProfile->daily_cigarettes = $data['daily_cigarettes'];
        }
        if (isset($data['pack_cost'])) {
            $userProfile->pack_cost = $data['pack_cost'];
        }
        $userProfile->saveOrFail();

        // バッジチェックを実行
        ($this->checkAndAwardBadgesAction)();

        // ユーザープロフィールのキャッシュをクリア
        Cache::forget("user.profile.{$user->id}");
    }
}
