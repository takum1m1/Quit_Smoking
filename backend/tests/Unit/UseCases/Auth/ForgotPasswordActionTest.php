<?php

namespace Tests\Unit\UseCases\Auth;

use App\Models\User;
use App\UseCases\Auth\ForgotPasswordAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ForgotPasswordActionTest extends TestCase
{
    use RefreshDatabase;

    private ForgotPasswordAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new ForgotPasswordAction();
    }

    /**
     * 存在するメールアドレスでリセットリンク送信
     */
    public function test_forgot_password_with_existing_email(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $data = [
            'email' => 'test@example.com',
        ];

        // Password::sendResetLinkが呼ばれることを確認
        Password::shouldReceive('sendResetLink')
            ->once()
            ->with(['email' => 'test@example.com'])
            ->andReturn(Password::RESET_LINK_SENT);

        $this->action->__invoke($data);
    }

    /**
     * 存在しないメールアドレスで何も起きない
     */
    public function test_forgot_password_with_nonexistent_email(): void
    {
        $data = [
            'email' => 'notfound@example.com',
        ];

        // Password::sendResetLinkが呼ばれないことを確認
        Password::shouldReceive('sendResetLink')
            ->never();

        $this->action->__invoke($data);
    }

    /**
     * 複数のユーザーが存在する場合でも正しいユーザーのみ対象
     */
    public function test_forgot_password_with_multiple_users(): void
    {
        $user1 = User::factory()->create([
            'email' => 'user1@example.com',
        ]);
        $user2 = User::factory()->create([
            'email' => 'user2@example.com',
        ]);

        $data = [
            'email' => 'user1@example.com',
        ];

        // user1のメールアドレスのみでsendResetLinkが呼ばれる
        Password::shouldReceive('sendResetLink')
            ->once()
            ->with(['email' => 'user1@example.com'])
            ->andReturn(Password::RESET_LINK_SENT);

        $this->action->__invoke($data);
    }
}
