<?php

namespace App\UseCases\UserProfile;

use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CheckAndAwardBadgesAction
{
    /**
     * Execute the action to check and award badges for the authenticated user.
     *
     * @return array
     */
    public function __invoke(): array
    {
        $user = Auth::user();
        $userProfile = $user->profile;

        $quitDate = Carbon::parse($userProfile->quit_date);
        $today = Carbon::today();
        $quitDays = $quitDate->diffInDays($today);

        $awardedBadges = [];
        $currentEarnedBadges = $userProfile->earned_badges ?? [];

        // 設定からバッジ定義を取得
        $badgeDefinitions = config('badges.badges');

        foreach ($badgeDefinitions as $badge) {
            $requiredDays = $badge['days_required'];

            // 条件を満たし、まだ授与されていない場合
            if ($quitDays >= $requiredDays && !in_array($badge['code'], $currentEarnedBadges)) {
                $awardedBadges[] = $badge;
                $currentEarnedBadges[] = $badge['code'];
            }
        }

        // 新しいバッジが授与された場合、データベースを更新
        if (!empty($awardedBadges)) {
            $userProfile->earned_badges = $currentEarnedBadges;
            $userProfile->save();

            // ユーザープロフィールのキャッシュをクリア
            Cache::forget("user.profile.{$user->id}");
        }

        return $awardedBadges;
    }
}
