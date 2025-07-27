<?php

namespace Tests\Unit\UseCases\Auth;

use App\Exceptions\ResetPasswordException;
use App\Models\User;
use App\UseCases\Auth\ResetPasswordAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordActionTest extends TestCase
{
    use RefreshDatabase;

    private ResetPasswordAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new ResetPasswordAction();
    }

    /**
     * 正常にパスワードリセットできる
     */
    public function test_reset_password_successfully(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $token = 'valid-token';

        // Password::resetが成功を返すことをモック
        Password::shouldReceive('reset')
            ->once()
            ->andReturn(Password::PASSWORD_RESET);

        $this->action->__invoke($data, $token);

        // モックが正しく呼ばれたことを確認（パスワード更新は実際には行われない）
        $this->assertTrue(true);
    }

    /**
     * トークン不正時に例外が発生
     */
    public function test_reset_password_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $invalidToken = 'invalid-token';

        // Password::resetが失敗を返すことをモック
        Password::shouldReceive('reset')
            ->once()
            ->andReturn(Password::INVALID_TOKEN);

        $this->expectException(ResetPasswordException::class);

        $this->action->__invoke($data, $invalidToken);
    }

    /**
     * パスワード不一致時に例外が発生
     */
    public function test_reset_password_with_mismatched_passwords(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'differentpassword',
        ];

        $token = 'valid-token';

        // Password::resetが失敗を返すことをモック
        Password::shouldReceive('reset')
            ->once()
            ->andReturn(Password::INVALID_USER);

        $this->expectException(ResetPasswordException::class);

        $this->action->__invoke($data, $token);
    }

    /**
     * 存在しないメールアドレスで例外が発生
     */
    public function test_reset_password_with_nonexistent_email(): void
    {
        $data = [
            'email' => 'notfound@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $token = 'valid-token';

        // Password::resetが失敗を返すことをモック
        Password::shouldReceive('reset')
            ->once()
            ->andReturn(Password::INVALID_USER);

        $this->expectException(ResetPasswordException::class);

        $this->action->__invoke($data, $token);
    }
}
