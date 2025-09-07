<?php

namespace App\UseCases\UserProfile;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;

class GetByIdProfileAction
{
    /**
     * Execute the action to get a user's profile by ID.
     *
     * @param int $id
     * @return array
     */
    public function __invoke(int $id): array
    {
        $userProfile = User::findOrFail($id)->profile;

        $quitDate = $userProfile->quit_date;
        $now = CarbonImmutable::now();

        $dailyCigarettes = $userProfile->daily_cigarettes;
        $packCost = $userProfile->pack_cost;

        $quitDaysCount = $quitDate->diffInDays($now);

        $quitCigarettes = $dailyCigarettes * $quitDaysCount;
        $savedMoney = ($packCost * $quitCigarettes) / 20;
        $extendedLife = $quitCigarettes * 10; // 1本あたり10分

        // バッジ情報を取得
        $badges = $this->getBadgesInfo($userProfile->badges ?? []);

        return [
            'display_name'     => $userProfile->display_name,
            'daily_cigarettes' => $dailyCigarettes,
            'pack_cost'        => $packCost,
            'quit_date'        => $quitDate->toDateString(),
            'quit_days_count'  => $quitDaysCount,
            'quit_cigarettes'  => $quitCigarettes,
            'saved_money'      => $savedMoney,
            'extended_life'    => $extendedLife,
            'badges'           => $badges,
        ];

    }

    /**
     * Get badges information from earned badge codes.
     *
     * @param array $earnedBadgeCodes
     * @return array
     */
    private function getBadgesInfo(array $earnedBadgeCodes): array
    {
        $badgeDefinitions = config('badges.badges');
        $badges = [];

        foreach ($badgeDefinitions as $badge) {
            if (in_array($badge['code'], $earnedBadgeCodes)) {
                $badges[] = [
                    'code' => $badge['code'],
                    'name' => $badge['name'],
                    'description' => $badge['description'],
                ];
            }
        }

        return $badges;
    }
}
