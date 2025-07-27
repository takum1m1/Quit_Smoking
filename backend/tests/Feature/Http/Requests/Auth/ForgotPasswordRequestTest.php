<?php

namespace Tests\Feature\Http\Requests\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Http\Requests\Auth\ForgotPasswordRequest;

class ForgotPasswordRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正常なデータでバリデーションが通ることをテスト
     */
    public function test_validation_passes_with_valid_data(): void
    {
        $data = [
            'email' => 'test@example.com',
        ];

        $request = new ForgotPasswordRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    /**
     * 必須項目が不足している場合のバリデーションエラー
     */
    public function test_validation_fails_with_missing_email(): void
    {
        $data = [
            // email が不足
        ];

        $request = new ForgotPasswordRequest();
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
        ];

        $request = new ForgotPasswordRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    /**
     * 空のメールアドレスのバリデーションエラー
     */
    public function test_validation_fails_with_empty_email(): void
    {
        $data = [
            'email' => '',
        ];

        $request = new ForgotPasswordRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    /**
     * 長すぎるメールアドレスのバリデーションエラー
     */
    public function test_validation_fails_with_long_email(): void
    {
        $data = [
            'email' => str_repeat('a', 250) . '@example.com', // 256文字超過
        ];

        $request = new ForgotPasswordRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }
}
