<?php

namespace Tests\Unit\UseCases\UserProfile;

use App\Models\User;
use App\Models\UserProfile;
use App\UseCases\UserProfile\CheckAndAwardBadgesAction;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\UseCases\UserProfile\UpdateAction;

class UpdateActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザー更新機能
     */
    public function testUpdate01(): void
    {
        $user = User::factory()->create();

        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'Old Name',
            'daily_cigarettes' => 10,
            'pack_cost' => 400,
        ]);

        // ユーザーをSanctumで認証
        $this->actingAs($user, 'sanctum');

        $data = [
            'display_name' => 'New Name',
            'daily_cigarettes' => 15,
            'pack_cost' => 450,
        ];

        $checkAndAwardBadgesAction = new CheckAndAwardBadgesAction();
        $action = new UpdateAction($checkAndAwardBadgesAction);
        $actual = $action($data);

        $this->assertNull($actual); // 戻り値はvoidなのでnullを期待
        $this->assertDatabaseHas('user_profiles', ['id' => $userProfile->id, 'display_name' => 'New Name']);
        $this->assertDatabaseHas('user_profiles', ['id' => $userProfile->id, 'daily_cigarettes' => 15]);
        $this->assertDatabaseHas('user_profiles', ['id' => $userProfile->id, 'pack_cost' => 450]);
    }
}
