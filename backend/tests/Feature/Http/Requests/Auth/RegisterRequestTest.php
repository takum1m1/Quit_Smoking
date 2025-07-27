<?php

namespace Tests\Feature\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Http\Requests\Auth\RegisterRequest;

class RegisterRequestTest extends TestCase
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
            'password_confirmation' => 'password123',
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ];

        $request = new RegisterRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $this->fail('Validation failed: ' . json_encode($validator->errors()->toArray()));
        }

        $this->assertTrue($validator->passes());
    }

    /**
     * パスワード確認が一致しない場合のバリデーションエラー
     */
    public function test_validation_fails_with_mismatched_password_confirmation(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ];

        $request = new RegisterRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('password'));
    }

    /**
     * 必須項目が不足している場合のバリデーションエラー
     */
    public function test_validation_fails_with_missing_required_fields(): void
    {
        $data = [
            // email が不足
            'password' => 'password123',
            'password_confirmation' => 'password123',
            // display_name が不足
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ];

        $request = new RegisterRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
        $this->assertTrue($validator->errors()->has('display_name'));
    }

    /**
     * 無効なメールアドレスのバリデーションエラー
     */
    public function test_validation_fails_with_invalid_email(): void
    {
        $data = [
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ];

        $request = new RegisterRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    /**
     * 重複するメールアドレスのバリデーションエラー
     */
    public function test_validation_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $data = [
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ];

        $request = new RegisterRequest();
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
            'password' => '123',
            'password_confirmation' => '123',
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ];

        $request = new RegisterRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('password'));
    }

    /**
     * パスワードにアルファベットが含まれない場合のバリデーションエラー
     */
    public function test_validation_fails_with_password_without_letter(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => '12345678', // 数字のみ
            'password_confirmation' => '12345678',
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ];

        $request = new RegisterRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('password'));
    }

    /**
     * display_nameが長すぎる場合のバリデーションエラー
     */
    public function test_validation_fails_with_long_display_name(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'display_name' => str_repeat('a', 21), // 21文字（制限20文字）
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ];

        $request = new RegisterRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('display_name'));
    }

    /**
     * daily_cigarettesが範囲外の場合のバリデーションエラー
     */
    public function test_validation_fails_with_invalid_daily_cigarettes(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'display_name' => 'Test User',
            'daily_cigarettes' => 0, // 最小値1未満
            'pack_cost' => 500,
        ];

        $request = new RegisterRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('daily_cigarettes'));
    }

    /**
     * pack_costが範囲外の場合のバリデーションエラー
     */
    public function test_validation_fails_with_invalid_pack_cost(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 200, // 最小値300未満
        ];

        $request = new RegisterRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('pack_cost'));
    }
}
