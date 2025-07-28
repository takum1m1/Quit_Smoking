<?php

namespace App\UseCases\UserProfile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

        $userProfile->display_name = $data['display_name'];
        $userProfile->daily_cigarettes = $data['daily_cigarettes'];
        $userProfile->pack_cost = $data['pack_cost'];
        $userProfile->saveOrFail();

        // バッジチェックを実行
        ($this->checkAndAwardBadgesAction)();
    }
}
