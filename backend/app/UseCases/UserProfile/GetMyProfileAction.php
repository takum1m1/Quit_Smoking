<?php

namespace App\UseCases\UserProfile;

use App\Models\UserProfile;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;

class GetMyProfileAction
{
    /**
     * Execute the action to get the user's profile.
     *
     * @return array
     */
    public function __invoke() : array
    {
        $user = Auth::user();
        $userProfile = UserProfile::findOrFail($user->id);

        $quitDate = $userProfile->quit_date;
        $now = CarbonImmutable::now();

        $dailyCigarettes = $userProfile->daily_cigarettes;
        $packCost = $userProfile->pack_cost;

        $quitDaysCount = $quitDate->diffInDays($now);

        $quitCigarettes = $dailyCigarettes * $quitDaysCount;
        $savedMoney = ($packCost * $quitCigarettes) / 20;
        $extendedLife = $quitCigarettes * 10; // 1本あたり10分

        return [
            'display_name'     => $userProfile->display_name,
            'daily_cigarettes' => $dailyCigarettes,
            'pack_cost'        => $packCost,
            'quit_date'        => $quitDate->toDateString(),
            'quit_days_count'  => $quitDaysCount,
            'quit_cigarettes'  => $quitCigarettes,
            'saved_money'      => $savedMoney,
            'extended_life'    => $extendedLife,
        ];
    }
}
