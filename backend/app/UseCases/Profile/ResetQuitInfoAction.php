<?php

namespace App\UseCases\Profile;

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
        $userProfile = UserProfile::findOrFail(Auth::user()->id);

        $userProfile->quit_date = CarbonImmutable::now();
        $userProfile->save();
    }
}
