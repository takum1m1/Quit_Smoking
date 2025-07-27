<?php

namespace Tests\Unit\UseCases\Community;

use App\Models\Post;
use App\Models\User;
use App\UseCases\Community\UpdatePostAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class UpdatePostActionTest extends TestCase
{
    use RefreshDatabase;

    private UpdatePostAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UpdatePostAction();
    }

    /**
     * 投稿が正常に更新されることをテスト
     */
    public function test_update_post_successfully(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $updateData = [
            'content' => '更新された投稿内容です。',
        ];

        $result = $this->action->__invoke($post->id, $updateData);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals($updateData['content'], $result->content);
        $this->assertEquals($user->id, $result->user_id);

        // データベースが更新されていることを確認
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'content' => $updateData['content'],
            'user_id' => $user->id,
        ]);
    }

    /**
     * リレーションがロードされていることをテスト
     */
    public function test_update_post_with_relations_loaded(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $updateData = [
            'content' => '更新された投稿内容です。',
        ];

        $result = $this->action->__invoke($post->id, $updateData);

        // リレーションがロードされていることを確認
        $this->assertTrue($result->relationLoaded('user'));
        $this->assertTrue($result->relationLoaded('comments'));
        $this->assertTrue($result->relationLoaded('likes'));

        // ユーザー情報が正しく取得されていることを確認
        $this->assertEquals($user->id, $result->user->id);
    }

    /**
     * 存在しない投稿IDでエラーが発生することをテスト
     */
    public function test_update_post_with_invalid_id(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->expectException(ModelNotFoundException::class);

        $updateData = [
            'content' => '更新された投稿内容です。',
        ];

        $this->action->__invoke(999, $updateData);
    }

    /**
     * 他のユーザーの投稿を更新しようとしてエラーが発生することをテスト
     */
    public function test_update_other_users_post(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user1->id]);

        Auth::login($user2);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Unauthorized action.');

        $updateData = [
            'content' => '更新された投稿内容です。',
        ];

        $this->action->__invoke($post->id, $updateData);
    }

    /**
     * 空のコンテンツで投稿を更新した場合のテスト
     */
    public function test_update_post_with_empty_content(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $updateData = [
            'content' => '',
        ];

        $result = $this->action->__invoke($post->id, $updateData);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals('', $result->content);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'content' => '',
        ]);
    }

    /**
     * 長いコンテンツで投稿を更新した場合のテスト
     */
    public function test_update_post_with_long_content(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $longContent = str_repeat('これは長い更新された投稿内容です。', 100);
        $updateData = [
            'content' => $longContent,
        ];

        $result = $this->action->__invoke($post->id, $updateData);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals($longContent, $result->content);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'content' => $longContent,
        ]);
    }

    /**
     * 削除された投稿を更新しようとしてエラーが発生することをテスト
     */
    public function test_update_deleted_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        // 投稿を削除
        $post->delete();

        Auth::login($user);

        $this->expectException(ModelNotFoundException::class);

        $updateData = [
            'content' => '更新された投稿内容です。',
        ];

        $this->action->__invoke($post->id, $updateData);
    }
}
