<?php

namespace Tests\Feature\Http\Requests\UserProfile;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Http\Requests\UserProfile\UpdateRequest;

class UpdateRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正常なデータでバリデーションが通ることをテスト
     */
    public function test_validation_passes_with_valid_data(): void
    {
        $data = [
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ];

        $request = new UpdateRequest();
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
            'display_name' => 'Test User',
            // daily_cigarettes が不足
            'pack_cost' => 500,
        ];

        $request = new UpdateRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('daily_cigarettes'));
    }

    /**
     * display_nameが長すぎる場合のバリデーションエラー
     */
    public function test_validation_fails_with_long_display_name(): void
    {
        $data = [
            'display_name' => str_repeat('a', 21), // 21文字（制限20文字）
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ];

        $request = new UpdateRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('display_name'));
    }

    /**
     * display_nameが空の場合のバリデーションエラー
     */
    public function test_validation_fails_with_empty_display_name(): void
    {
        $data = [
            'display_name' => '',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ];

        $request = new UpdateRequest();
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
            'display_name' => 'Test User',
            'daily_cigarettes' => 0, // 最小値1未満
            'pack_cost' => 500,
        ];

        $request = new UpdateRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('daily_cigarettes'));
    }

    /**
     * daily_cigarettesが文字列の場合のバリデーションエラー
     */
    public function test_validation_fails_with_string_daily_cigarettes(): void
    {
        $data = [
            'display_name' => 'Test User',
            'daily_cigarettes' => 'twenty', // 文字列
            'pack_cost' => 500,
        ];

        $request = new UpdateRequest();
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
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 200, // 最小値300未満
        ];

        $request = new UpdateRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('pack_cost'));
    }

    /**
     * pack_costが最大値を超える場合のバリデーションエラー
     */
    public function test_validation_fails_with_too_high_pack_cost(): void
    {
        $data = [
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 3001, // 最大値3000超過
        ];

        $request = new UpdateRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('pack_cost'));
    }

    /**
     * pack_costが文字列の場合のバリデーションエラー
     */
    public function test_validation_fails_with_string_pack_cost(): void
    {
        $data = [
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 'five hundred', // 文字列
        ];

        $request = new UpdateRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('pack_cost'));
    }

    /**
     * 最大文字数ギリギリのdisplay_nameでバリデーションが通ることをテスト
     */
    public function test_validation_passes_with_max_length_display_name(): void
    {
        $data = [
            'display_name' => str_repeat('a', 20), // 20文字（最大文字数）
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ];

        $request = new UpdateRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    /**
     * 最小値ギリギリのdaily_cigarettesでバリデーションが通ることをテスト
     */
    public function test_validation_passes_with_min_daily_cigarettes(): void
    {
        $data = [
            'display_name' => 'Test User',
            'daily_cigarettes' => 1, // 最小値
            'pack_cost' => 500,
        ];

        $request = new UpdateRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    /**
     * 範囲ギリギリのpack_costでバリデーションが通ることをテスト
     */
    public function test_validation_passes_with_boundary_pack_cost(): void
    {
        $data = [
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 300, // 最小値
        ];

        $request = new UpdateRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());

        $data['pack_cost'] = 3000; // 最大値
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }
}
