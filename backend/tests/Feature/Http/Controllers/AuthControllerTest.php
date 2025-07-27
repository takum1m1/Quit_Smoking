<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザー登録
     */
    public function test_register(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ];

        $response = $this->post('api/register', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure(['message', 'token']);
    }

    /**
     * ログイン
     */
    public function test_login(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->post('api/login', $data);

        $response->assertStatus(200);
    }

    /**
     * ログイン失敗
     */
    public function test_login_fail(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->post('api/login', $data);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'ログインに失敗しました。メールアドレスまたはパスワードが正しくありません。']);
    }

    /**
     * ログアウト
     */
    public function test_logout(): void
    {
        $user = User::factory()->create();

        // ログインしてトークンを取得
        $loginData = [
            'email' => $user->email,
            'password' => 'password', // UserFactoryのデフォルトパスワード
        ];

        $loginResponse = $this->post('api/login', $loginData);
        $loginResponse->assertStatus(200);

        $token = $loginResponse->json('token');

        // 取得したトークンでログアウト
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/logout');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'ログアウトが成功しました']);
    }
}
