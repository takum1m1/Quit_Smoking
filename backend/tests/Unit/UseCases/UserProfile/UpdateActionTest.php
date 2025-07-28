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
     * プロフィールが正常に更新されることをテスト
     */
    public function test_update_profile_successfully(): void
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

    /**
     * 部分的な更新が正常に動作することをテスト
     */
    public function test_update_profile_partially(): void
    {
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'Old Name',
            'daily_cigarettes' => 10,
            'pack_cost' => 400,
        ]);

        $this->actingAs($user, 'sanctum');

        $data = [
            'display_name' => 'New Name',
        ];

        $checkAndAwardBadgesAction = new CheckAndAwardBadgesAction();
        $action = new UpdateAction($checkAndAwardBadgesAction);
        $actual = $action($data);

        $this->assertNull($actual);
        $this->assertDatabaseHas('user_profiles', ['id' => $userProfile->id, 'display_name' => 'New Name']);
        $this->assertDatabaseHas('user_profiles', ['id' => $userProfile->id, 'daily_cigarettes' => 10]); // 変更されていない
        $this->assertDatabaseHas('user_profiles', ['id' => $userProfile->id, 'pack_cost' => 400]); // 変更されていない
    }

    /**
     * 認証されていないユーザーでエラーが発生することをテスト
     */
    public function test_update_profile_without_authentication(): void
    {
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        $data = [
            'display_name' => 'New Name',
        ];

        $checkAndAwardBadgesAction = new CheckAndAwardBadgesAction();
        $action = new UpdateAction($checkAndAwardBadgesAction);

        $this->expectException(\Exception::class);
        $action($data);
    }

    /**
     * 存在しないプロフィールでエラーが発生することをテスト
     */
    public function test_update_nonexistent_profile(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $data = [
            'display_name' => 'New Name',
        ];

        $checkAndAwardBadgesAction = new CheckAndAwardBadgesAction();
        $action = new UpdateAction($checkAndAwardBadgesAction);

        $this->expectException(\Error::class);
        $action($data);
    }
}
