<?php

namespace Tests\Unit\UseCases\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\UseCases\Auth\RegisterAction;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Hash;

class RegisterActionTest extends TestCase
{
    use RefreshDatabase;
    /**
     * ユーザー登録機能
     * - ユーザーアカウント作成
     * - ユーザープロフィール作成
     * - 禁煙開始日を今日の日付に設定
     * - トークン発行
     */
    public function testAction1()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ];
        $action = new RegisterAction();
        $token = $action($data);

        $this->assertNotEmpty($token);

        $this->assertDatabaseHas('users', ['email' =>$data['email']]);
        $this->assertDatabaseHas('user_profiles', ['display_name' => $data['display_name']]);
        $this->assertDatabaseHas('user_profiles', ['daily_cigarettes' => $data['daily_cigarettes']]);
        $this->assertDatabaseHas('user_profiles', ['pack_cost' => $data['pack_cost']]);
        $this->assertDatabaseHas('user_profiles', ['quit_date' => now()->toDateString()]);

        $user = User::where('email', $data['email'])->first();
        $this->assertTrue(Hash::check($data['password'], $user->password));
    }
}
