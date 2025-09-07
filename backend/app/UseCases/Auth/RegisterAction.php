<?php

namespace App\UseCases\Auth;

use App\Models\User;
use App\Models\UserProfile;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class RegisterAction
 * Handles user registration logic.
 */
class RegisterAction
{
    /**
     * Execute the registration action.
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function __invoke(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'display_name' => $data['display_name'],
                'daily_cigarettes' => $data['daily_cigarettes'],
                'pack_cost' => $data['pack_cost'],
                'quit_date' => CarbonImmutable::now(),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'token' => $token,
                'user' => $user->load('profile')
            ];
        });
    }
}
