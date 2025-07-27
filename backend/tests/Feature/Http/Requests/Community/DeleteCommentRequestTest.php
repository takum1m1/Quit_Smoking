<?php

namespace Tests\Feature\Http\Requests\Community;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Requests\Community\DeleteCommentRequest;

class DeleteCommentRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正常なデータでバリデーションが通ることをテスト
     */
    public function test_validation_passes_with_valid_data(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

        $data = [
            'comment_id' => $comment->id,
        ];

        $request = new DeleteCommentRequest();
        $rules = $request->rules();
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    /**
     * 必須項目が不足している場合のバリデーションエラー
     */
    public function test_validation_fails_with_missing_comment_id(): void
    {
        $data = [
            // comment_id が不足
        ];

        $request = new DeleteCommentRequest();
        $rules = $request->rules();
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('comment_id'));
    }

    /**
     * 存在しないcomment_idのバリデーションエラー
     */
    public function test_validation_fails_with_nonexistent_comment_id(): void
    {
        $data = [
            'comment_id' => 999,
        ];

        $request = new DeleteCommentRequest();
        $rules = $request->rules();
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('comment_id'));
    }

    /**
     * authorizeメソッドがtrueを返すことをテスト
     */
    public function test_authorize_returns_true(): void
    {
        $request = new DeleteCommentRequest();
        $this->assertTrue($request->authorize());
    }
}
