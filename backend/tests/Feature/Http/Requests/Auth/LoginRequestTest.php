<?php

namespace Tests\Feature\Http\Requests\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Http\Requests\Auth\LoginRequest;

class LoginRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正常なデータでバリデーションが通ることをテスト
     */
    public function test_validation_passes_with_valid_data(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $request = new LoginRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    /**
     * 必須項目が不足している場合のバリデーションエラー
     */
    public function test_validation_fails_with_missing_required_fields(): void
    {
        $data = [
            // email が不足
            'password' => 'password123',
        ];

        $request = new LoginRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    /**
     * 無効なメールアドレスのバリデーションエラー
     */
    public function test_validation_fails_with_invalid_email(): void
    {
        $data = [
            'email' => 'invalid-email',
            'password' => 'password123',
        ];

        $request = new LoginRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    /**
     * 短すぎるパスワードのバリデーションエラー
     */
    public function test_validation_fails_with_short_password(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => '123', // 最小6文字未満
        ];

        $request = new LoginRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('password'));
    }

    /**
     * 長すぎるパスワードのバリデーションエラー
     */
    public function test_validation_fails_with_long_password(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => str_repeat('a', 129), // 129文字（最大128文字超過）
        ];

        $request = new LoginRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('password'));
    }

    /**
     * 長すぎるメールアドレスのバリデーションエラー
     */
    public function test_validation_fails_with_long_email(): void
    {
        $data = [
            'email' => str_repeat('a', 250) . '@example.com', // 256文字超過
            'password' => 'password123',
        ];

        $request = new LoginRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    /**
     * 空のパスワードのバリデーションエラー
     */
    public function test_validation_fails_with_empty_password(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => '',
        ];

        $request = new LoginRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('password'));
    }

    /**
     * 空のメールアドレスのバリデーションエラー
     */
    public function test_validation_fails_with_empty_email(): void
    {
        $data = [
            'email' => '',
            'password' => 'password123',
        ];

        $request = new LoginRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }
}
