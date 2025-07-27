<?php

namespace Tests\Unit\UseCases\UserProfile;

use App\Models\User;
use App\Models\UserProfile;
use App\UseCases\UserProfile\GetByIdProfileAction;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetByIdProfileActionTest extends TestCase
{
    use RefreshDatabase;

    private GetByIdProfileAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new GetByIdProfileAction();
        CarbonImmutable::setTestNow('2024-07-27 00:00:00');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        CarbonImmutable::setTestNow();
    }

    /**
     * 正常にユーザープロフィールを取得できることをテスト
     */
    public function test_get_profile_by_id_successfully(): void
    {
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
            'quit_date' => CarbonImmutable::now()->subDays(5),
        ]);

        $result = $this->action->__invoke($user->id);

        $this->assertIsArray($result);
        $this->assertEquals('Test User', $result['display_name']);
        $this->assertEquals(20, $result['daily_cigarettes']);
        $this->assertEquals(500, $result['pack_cost']);
        $this->assertEquals(5, (int)$result['quit_days_count']);
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
     * 存在しないユーザーIDでエラーが発生することをテスト
     */
    public function test_get_profile_with_invalid_id(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->action->__invoke(999);
    }

    /**
     * 禁煙日数が0日の場合の計算をテスト
     */
    public function test_get_profile_with_zero_quit_days(): void
    {
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'daily_cigarettes' => 15,
            'pack_cost' => 400,
            'quit_date' => CarbonImmutable::now(),
        ]);

        $result = $this->action->__invoke($user->id);

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
    public function test_get_profile_with_one_quit_day(): void
    {
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'daily_cigarettes' => 10,
            'pack_cost' => 300,
            'quit_date' => CarbonImmutable::now()->subDay(),
        ]);

        $result = $this->action->__invoke($user->id);

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
    public function test_get_profile_with_thirty_quit_days(): void
    {
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'daily_cigarettes' => 25,
            'pack_cost' => 600,
            'quit_date' => CarbonImmutable::now()->subDays(30),
        ]);

        $result = $this->action->__invoke($user->id);

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
    public function test_get_profile_with_multiple_users(): void
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

        $result1 = $this->action->__invoke($user1->id);
        $result2 = $this->action->__invoke($user2->id);

        $this->assertEquals('User 1', $result1['display_name']);
        $this->assertEquals(10, (int)$result1['quit_days_count']);
        $this->assertEquals('User 2', $result2['display_name']);
        $this->assertEquals(5, (int)$result2['quit_days_count']);
    }
}
