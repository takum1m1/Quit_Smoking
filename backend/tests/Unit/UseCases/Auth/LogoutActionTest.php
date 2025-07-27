<?php

namespace Tests\Unit\UseCases\Auth;

use App\Models\User;
use App\UseCases\Auth\LoginAction;
use App\UseCases\Auth\LogoutAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutActionTest extends TestCase
{
    use RefreshDatabase;

    private LogoutAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new LogoutAction();
    }

    /**
     * 正常にトークンが削除されることをテスト
     */
    public function test_logout_deletes_token(): void
    {
        $user = User::factory()->create();
        $loginAction = new LoginAction();
        $token = $loginAction([
            'email' => $user->email,
            'password' => 'password',
        ]);
        // トークンを使って認証状態にする
        $accessToken = $user->tokens()->first();
        Auth::login($user);
        $user->withAccessToken($accessToken);

        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $accessToken->id,
        ]);

        $this->action->__invoke();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $accessToken->id,
        ]);
    }

    /**
     * 未認証時の挙動（Error例外発生）
     */
    public function test_logout_without_authentication(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to a member function tokens() on null');
        $this->action->__invoke();
    }
}
