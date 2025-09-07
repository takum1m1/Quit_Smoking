<?php

namespace Tests\Unit\UseCases\Auth;

use App\Models\User;
use App\UseCases\Auth\LoginAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginActionTest extends TestCase
{
    use RefreshDatabase;

    private LoginAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new LoginAction();
    }

    /**
     * 正常にログインできることをテスト
     */
    public function test_login_successfully(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $result = $this->action->__invoke($data);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertIsString($result['token']);
        $this->assertNotEmpty($result['token']);
    }

    /**
     * パスワード不一致でログイン失敗
     */
    public function test_login_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test2@example.com',
            'password' => Hash::make('password'),
        ]);

        $data = [
            'email' => 'test2@example.com',
            'password' => 'wrongpassword',
        ];

        $result = $this->action->__invoke($data);
        $this->assertNull($result);
    }

    /**
     * 存在しないメールアドレスでログイン失敗
     */
    public function test_login_with_nonexistent_email(): void
    {
        $data = [
            'email' => 'notfound@example.com',
            'password' => 'password',
        ];

        $result = $this->action->__invoke($data);
        $this->assertNull($result);
    }
}
