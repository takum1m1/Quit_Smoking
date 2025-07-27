<?php

namespace Tests\Unit\UseCases\UserProfile;

use App\Models\User;
use App\Models\UserProfile;
use App\UseCases\UserProfile\ResetQuitInfoAction;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ResetQuitInfoActionTest extends TestCase
{
    use RefreshDatabase;

    private ResetQuitInfoAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new ResetQuitInfoAction();
        CarbonImmutable::setTestNow('2024-07-27 00:00:00');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        CarbonImmutable::setTestNow();
    }

    /**
     * 正常に禁煙情報をリセットできることをテスト
     */
    public function test_reset_quit_info_successfully(): void
    {
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'quit_date' => CarbonImmutable::now()->subDays(10),
        ]);

        Auth::login($user);

        $this->action->__invoke();

        $userProfile->refresh();

        // 禁煙日が今日の日付に更新されていることを確認
        $this->assertEquals(
            CarbonImmutable::now()->toDateString(),
            $userProfile->quit_date->toDateString()
        );
    }

    /**
     * 認証されていない場合のテスト
     */
    public function test_reset_quit_info_without_authentication(): void
    {
        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage('Attempt to read property "id" on null');

        $this->action->__invoke();
    }

    /**
     * プロフィールが存在しない場合のテスト
     */
    public function test_reset_quit_info_without_profile(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->expectException(ModelNotFoundException::class);

        $this->action->__invoke();
    }

    /**
     * 複数回リセットしても正しく動作することをテスト
     */
    public function test_reset_quit_info_multiple_times(): void
    {
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'quit_date' => CarbonImmutable::now()->subDays(5),
        ]);

        Auth::login($user);

        // 1回目のリセット
        $this->action->__invoke();
        $userProfile->refresh();
        $firstResetDate = $userProfile->quit_date->toDateString();

        // 少し時間を置いて2回目のリセット
        sleep(1);
        $this->action->__invoke();
        $userProfile->refresh();
        $secondResetDate = $userProfile->quit_date->toDateString();

        // 両方とも今日の日付になっていることを確認
        $this->assertEquals(CarbonImmutable::now()->toDateString(), $firstResetDate);
        $this->assertEquals(CarbonImmutable::now()->toDateString(), $secondResetDate);
    }

    /**
     * 複数のユーザーが存在する場合でも正しいユーザーのプロフィールが更新されることをテスト
     */
    public function test_reset_quit_info_with_multiple_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $userProfile1 = UserProfile::factory()->create([
            'user_id' => $user1->id,
            'quit_date' => CarbonImmutable::now()->subDays(10),
        ]);

        $userProfile2 = UserProfile::factory()->create([
            'user_id' => $user2->id,
            'quit_date' => CarbonImmutable::now()->subDays(5),
        ]);

        // User 1でリセット
        Auth::login($user1);
        $this->action->__invoke();

        $userProfile1->refresh();
        $userProfile2->refresh();

        // User 1の禁煙日のみが更新されていることを確認
        $this->assertEquals(
            CarbonImmutable::now()->toDateString(),
            $userProfile1->quit_date->toDateString()
        );
        $this->assertEquals(
            CarbonImmutable::now()->subDays(5)->toDateString(),
            $userProfile2->quit_date->toDateString()
        );

        // User 2でリセット
        Auth::login($user2);
        $this->action->__invoke();

        $userProfile1->refresh();
        $userProfile2->refresh();

        // User 2の禁煙日のみが更新されていることを確認
        $this->assertEquals(
            CarbonImmutable::now()->toDateString(),
            $userProfile1->quit_date->toDateString()
        );
        $this->assertEquals(
            CarbonImmutable::now()->toDateString(),
            $userProfile2->quit_date->toDateString()
        );
    }

    /**
     * 他のプロフィール情報は変更されないことをテスト
     */
    public function test_reset_quit_info_preserves_other_data(): void
    {
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
            'quit_date' => CarbonImmutable::now()->subDays(10),
        ]);

        Auth::login($user);

        $this->action->__invoke();

        $userProfile->refresh();

        // 禁煙日のみが更新され、他の情報は変更されていないことを確認
        $this->assertEquals('Test User', $userProfile->display_name);
        $this->assertEquals(20, $userProfile->daily_cigarettes);
        $this->assertEquals(500, $userProfile->pack_cost);
        $this->assertEquals(
            CarbonImmutable::now()->toDateString(),
            $userProfile->quit_date->toDateString()
        );
    }
}
