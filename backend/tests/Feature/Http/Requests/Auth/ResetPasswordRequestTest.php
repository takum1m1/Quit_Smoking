<?php

namespace Tests\Feature\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Http\Requests\Auth\ResetPasswordRequest;

class ResetPasswordRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正常なデータでバリデーションが通ることをテスト
     */
    public function test_validation_passes_with_valid_data(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $data = [
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $request = new ResetPasswordRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    /**
     * 必須項目が不足している場合のバリデーションエラー
     */
    public function test_validation_fails_with_missing_required_fields(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $data = [
            'email' => 'test@example.com',
            // password が不足
            'password_confirmation' => 'newpassword123',
        ];

        $request = new ResetPasswordRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('password'));
    }

    /**
     * 無効なメールアドレスのバリデーションエラー
     */
    public function test_validation_fails_with_invalid_email(): void
    {
        $data = [
            'email' => 'invalid-email',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $request = new ResetPasswordRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    /**
     * 存在しないメールアドレスのバリデーションエラー
     */
    public function test_validation_fails_with_nonexistent_email(): void
    {
        $data = [
            'email' => 'nonexistent@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $request = new ResetPasswordRequest();
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
        User::factory()->create(['email' => 'test@example.com']);

        $data = [
            'email' => 'test@example.com',
            'password' => '123', // 最小8文字未満
            'password_confirmation' => '123',
        ];

        $request = new ResetPasswordRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('password'));
    }

    /**
     * パスワード確認が一致しない場合のバリデーションエラー
     */
    public function test_validation_fails_with_mismatched_password_confirmation(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $data = [
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ];

        $request = new ResetPasswordRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('password'));
    }

    /**
     * 空のパスワードのバリデーションエラー
     */
    public function test_validation_fails_with_empty_password(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $data = [
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ];

        $request = new ResetPasswordRequest();
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
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $request = new ResetPasswordRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }
}
