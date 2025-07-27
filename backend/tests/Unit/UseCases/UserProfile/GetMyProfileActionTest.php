<?php

namespace Tests\Unit\UseCases\UserProfile;

use App\Models\User;
use App\Models\UserProfile;
use App\UseCases\UserProfile\GetMyProfileAction;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class GetMyProfileActionTest extends TestCase
{
    use RefreshDatabase;

    private GetMyProfileAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new GetMyProfileAction();
        CarbonImmutable::setTestNow('2024-07-27 00:00:00');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        CarbonImmutable::setTestNow();
    }

    /**
     * 正常に自分のプロフィールを取得できることをテスト
     */
    public function test_get_my_profile_successfully(): void
    {
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'My Profile',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
            'quit_date' => CarbonImmutable::now()->subDays(7),
        ]);

        Auth::login($user);

        $result = $this->action->__invoke();

        $this->assertIsArray($result);
        $this->assertEquals('My Profile', $result['display_name']);
        $this->assertEquals(20, $result['daily_cigarettes']);
        $this->assertEquals(500, $result['pack_cost']);
        $this->assertEquals(7, (int)$result['quit_days_count']);
        $this->assertEquals((int)$result['quit_days_count'] * 20, (int)$result['quit_cigarettes']);
        $this->assertEquals((int)((500 * (int)$result['quit_cigarettes']) / 20), (int)$result['saved_money']);
        $this->assertEquals((int)$result['quit_cigarettes'] * 10, (int)$result['extended_life']);

        // 実際の値を確認
        $this->assertGreaterThan(0, (int)$result['quit_days_count']);
        $this->assertGreaterThan(0, (int)$result['quit_cigarettes']);
        $this->assertGreaterThan(0, (int)$result['saved_money']);
        $this->assertGreaterThan(0, (int)$result['extended_life']);
    }

    /**
     * 認証されていない場合のテスト
     */
    public function test_get_my_profile_without_authentication(): void
    {
        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage('Attempt to read property "id" on null');

        $this->action->__invoke();
    }

    /**
     * 禁煙日数が0日の場合の計算をテスト
     */
    public function test_get_my_profile_with_zero_quit_days(): void
    {
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'daily_cigarettes' => 15,
            'pack_cost' => 400,
            'quit_date' => CarbonImmutable::now(),
        ]);

        Auth::login($user);

        $result = $this->action->__invoke();

        $this->assertEquals(0, (int)$result['quit_days_count']);
        $this->assertEquals((int)$result['quit_days_count'] * 15, (int)$result['quit_cigarettes']);
        $this->assertEquals((int)((400 * (int)$result['quit_cigarettes']) / 20), (int)$result['saved_money']);
        $this->assertEquals((int)$result['quit_cigarettes'] * 10, (int)$result['extended_life']);

        // 実際の値を確認
        $this->assertEquals(0, (int)$result['quit_days_count']);
        $this->assertEquals(0, (int)$result['quit_cigarettes']);
        $this->assertEquals(0, (int)$result['saved_money']);
        $this->assertEquals(0, (int)$result['extended_life']);
    }

    /**
     * 禁煙日数が1日の場合の計算をテスト
     */
    public function test_get_my_profile_with_one_quit_day(): void
    {
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'daily_cigarettes' => 10,
            'pack_cost' => 300,
            'quit_date' => CarbonImmutable::now()->subDay(),
        ]);

        Auth::login($user);

        $result = $this->action->__invoke();

        $this->assertEquals(1, (int)$result['quit_days_count']);
        $this->assertEquals((int)$result['quit_days_count'] * 10, (int)$result['quit_cigarettes']);
        $this->assertEquals((int)((300 * (int)$result['quit_cigarettes']) / 20), (int)$result['saved_money']);
        $this->assertEquals((int)$result['quit_cigarettes'] * 10, (int)$result['extended_life']);

        // 実際の値を確認
        $this->assertGreaterThan(0, (int)$result['quit_days_count']);
        $this->assertGreaterThan(0, (int)$result['quit_cigarettes']);
        $this->assertGreaterThan(0, (int)$result['saved_money']);
        $this->assertGreaterThan(0, (int)$result['extended_life']);
    }

    /**
     * 禁煙日数が30日の場合の計算をテスト
     */
    public function test_get_my_profile_with_thirty_quit_days(): void
    {
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'daily_cigarettes' => 25,
            'pack_cost' => 600,
            'quit_date' => CarbonImmutable::now()->subDays(30),
        ]);

        Auth::login($user);

        $result = $this->action->__invoke();

        $this->assertEquals(30, (int)$result['quit_days_count']);
        $this->assertEquals((int)$result['quit_days_count'] * 25, (int)$result['quit_cigarettes']);
        $this->assertEquals((int)((600 * (int)$result['quit_cigarettes']) / 20), (int)$result['saved_money']);
        $this->assertEquals((int)$result['quit_cigarettes'] * 10, (int)$result['extended_life']);

        // 実際の値を確認
        $this->assertGreaterThan(25, (int)$result['quit_days_count']);
        $this->assertGreaterThan(600, (int)$result['quit_cigarettes']);
        $this->assertGreaterThan(18000, (int)$result['saved_money']);
        $this->assertGreaterThan(6000, (int)$result['extended_life']);
    }

    /**
     * 複数のユーザーが存在する場合でも正しいユーザーのプロフィールを取得できることをテスト
     */
    public function test_get_my_profile_with_multiple_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $userProfile1 = UserProfile::factory()->create([
            'user_id' => $user1->id,
            'display_name' => 'User 1',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
            'quit_date' => CarbonImmutable::now()->subDays(10),
        ]);

        $userProfile2 = UserProfile::factory()->create([
            'user_id' => $user2->id,
            'display_name' => 'User 2',
            'daily_cigarettes' => 15,
            'pack_cost' => 400,
            'quit_date' => CarbonImmutable::now()->subDays(5),
        ]);

        // User 1でログイン
        Auth::login($user1);
        $result1 = $this->action->__invoke();

        $this->assertEquals('User 1', $result1['display_name']);
        $this->assertEquals(10, (int)$result1['quit_days_count']);

        // User 2でログイン
        Auth::login($user2);
        $result2 = $this->action->__invoke();

        $this->assertEquals('User 2', $result2['display_name']);
        $this->assertEquals(5, (int)$result2['quit_days_count']);
    }

    /**
     * プロフィールが存在しない場合のテスト
     */
    public function test_get_my_profile_without_profile(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->expectException(ModelNotFoundException::class);

        $this->action->__invoke();
    }
}
