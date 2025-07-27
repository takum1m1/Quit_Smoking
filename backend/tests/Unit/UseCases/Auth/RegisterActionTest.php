<?php

namespace Tests\Unit\UseCases\Auth;

use App\Models\User;
use App\Models\UserProfile;
use App\UseCases\Auth\RegisterAction;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterActionTest extends TestCase
{
    use RefreshDatabase;

    private RegisterAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new RegisterAction();
        CarbonImmutable::setTestNow('2024-07-27 00:00:00');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        CarbonImmutable::setTestNow();
    }

    /**
     * ユーザー登録機能
     * - ユーザーアカウント作成
     * - ユーザープロフィール作成
     * - 禁煙開始日を今日の日付に設定
     * - トークン発行
     */
    public function test_register_successfully(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ];

        $token = $this->action->__invoke($data);

        $this->assertNotEmpty($token);

        $this->assertDatabaseHas('users', ['email' => $data['email']]);
        $this->assertDatabaseHas('user_profiles', ['display_name' => $data['display_name']]);
        $this->assertDatabaseHas('user_profiles', ['daily_cigarettes' => $data['daily_cigarettes']]);
        $this->assertDatabaseHas('user_profiles', ['pack_cost' => $data['pack_cost']]);

        // quit_dateの検証は日付部分のみで行う
        $userProfile = UserProfile::where('display_name', $data['display_name'])->first();
        $this->assertEquals(CarbonImmutable::now()->toDateString(), $userProfile->quit_date->toDateString());

        $user = User::where('email', $data['email'])->first();
        $this->assertTrue(Hash::check($data['password'], $user->password));
    }
}
