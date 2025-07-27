<?php

namespace Tests\Unit\UseCases\Community;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\UseCases\Community\DeleteCommentAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DeleteCommentActionTest extends TestCase
{
    use RefreshDatabase;

    private DeleteCommentAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeleteCommentAction();
    }

    /**
     * コメントが正常に削除されることをテスト
     */
    public function test_delete_comment_successfully(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        Auth::login($user);

        $result = $this->action->__invoke($post->id, $comment->id);

        // レスポンスの確認
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals(['message' => 'Comment deleted successfully'], $result->getData(true));

        // データベースから削除されていることを確認（ソフトデリート）
        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
    }

    /**
     * 存在しないコメントIDでエラーが発生することをテスト
     */
    public function test_delete_comment_with_invalid_id(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $this->expectException(ModelNotFoundException::class);

        $this->action->__invoke($post->id, 999);
    }

    /**
     * 他のユーザーのコメントを削除しようとしてエラーが発生することをテスト
     */
    public function test_delete_other_users_comment(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user1->id]);
        $comment = Comment::factory()->create([
            'user_id' => $user1->id,
            'post_id' => $post->id,
        ]);

        Auth::login($user2);

        $this->expectException(ModelNotFoundException::class);

        $this->action->__invoke($post->id, $comment->id);
    }

    /**
     * 異なる投稿のコメントを削除しようとしてエラーが発生することをテスト
     */
    public function test_delete_comment_from_different_post(): void
    {
        $user = User::factory()->create();
        $post1 = Post::factory()->create(['user_id' => $user->id]);
        $post2 = Post::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post1->id,
        ]);

        Auth::login($user);

        $this->expectException(ModelNotFoundException::class);

        $this->action->__invoke($post2->id, $comment->id);
    }

    /**
     * 既に削除されたコメントを削除しようとしてエラーが発生することをテスト
     */
    public function test_delete_already_deleted_comment(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        // コメントを削除
        $comment->delete();

        Auth::login($user);

        $this->expectException(ModelNotFoundException::class);

        $this->action->__invoke($post->id, $comment->id);
    }

    /**
     * 複数のコメントを削除した場合のテスト
     */
    public function test_delete_multiple_comments(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment1 = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
        $comment2 = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        Auth::login($user);

        // 1つ目のコメントを削除
        $result1 = $this->action->__invoke($post->id, $comment1->id);
        $this->assertEquals(200, $result1->getStatusCode());
        $this->assertSoftDeleted('comments', ['id' => $comment1->id]);

        // 2つ目のコメントを削除
        $result2 = $this->action->__invoke($post->id, $comment2->id);
        $this->assertEquals(200, $result2->getStatusCode());
        $this->assertSoftDeleted('comments', ['id' => $comment2->id]);

        // 両方のコメントが削除されていることを確認
        $this->assertSoftDeleted('comments', ['id' => $comment1->id]);
        $this->assertSoftDeleted('comments', ['id' => $comment2->id]);
    }

    /**
     * 削除後に他のコメントが影響を受けないことをテスト
     */
    public function test_delete_comment_does_not_affect_other_comments(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user1->id]);

        $comment1 = Comment::factory()->create([
            'user_id' => $user1->id,
            'post_id' => $post->id,
        ]);
        $comment2 = Comment::factory()->create([
            'user_id' => $user2->id,
            'post_id' => $post->id,
        ]);

        Auth::login($user1);

        // user1のコメントを削除
        $result = $this->action->__invoke($post->id, $comment1->id);
        $this->assertEquals(200, $result->getStatusCode());

        // user1のコメントは削除されている
        $this->assertSoftDeleted('comments', ['id' => $comment1->id]);

        // user2のコメントは削除されていない
        $this->assertDatabaseHas('comments', ['id' => $comment2->id]);
    }
}
