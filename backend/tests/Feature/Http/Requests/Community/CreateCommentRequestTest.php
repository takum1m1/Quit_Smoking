<?php

namespace Tests\Feature\Http\Requests\Community;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Http\Requests\Community\CreateCommentRequest;

class CreateCommentRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正常なデータでバリデーションが通ることをテスト
     */
    public function test_validation_passes_with_valid_data(): void
    {
        $data = [
            'content' => 'これはテストコメントです。',
        ];

        $request = new CreateCommentRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    /**
     * 必須項目が不足している場合のバリデーションエラー
     */
    public function test_validation_fails_with_missing_content(): void
    {
        $data = [
            // content が不足
        ];

        $request = new CreateCommentRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('content'));
    }

    /**
     * 空のコンテンツのバリデーションエラー
     */
    public function test_validation_fails_with_empty_content(): void
    {
        $data = [
            'content' => '',
        ];

        $request = new CreateCommentRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('content'));
    }

    /**
     * 長すぎるコンテンツのバリデーションエラー
     */
    public function test_validation_fails_with_long_content(): void
    {
        $data = [
            'content' => str_repeat('a', 201), // 201文字（最大200文字超過）
        ];

        $request = new CreateCommentRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('content'));
    }

    /**
     * 文字列以外のコンテンツのバリデーションエラー
     */
    public function test_validation_fails_with_non_string_content(): void
    {
        $data = [
            'content' => 123, // 数値
        ];

        $request = new CreateCommentRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('content'));
    }

    /**
     * 最大文字数ギリギリのコンテンツでバリデーションが通ることをテスト
     */
    public function test_validation_passes_with_max_length_content(): void
    {
        $data = [
            'content' => str_repeat('a', 200), // 200文字（最大文字数）
        ];

        $request = new CreateCommentRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    /**
     * 最小文字数のコンテンツでバリデーションが通ることをテスト
     */
    public function test_validation_passes_with_min_length_content(): void
    {
        $data = [
            'content' => 'a', // 1文字
        ];

        $request = new CreateCommentRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }
}
