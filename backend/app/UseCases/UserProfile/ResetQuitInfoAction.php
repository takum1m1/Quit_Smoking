<?php

namespace App\UseCases\UserProfile;

use App\Models\User;
use App\Models\UserProfile;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;

class ResetQuitInfoAction
{
    /**
     * Execute the action to reset the user's smoking information.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $userProfile = UserProfile::where('user_id', Auth::user()->id)->firstOrFail();

        $userProfile->quit_date = CarbonImmutable::now();
        $userProfile->save();
    }
}
