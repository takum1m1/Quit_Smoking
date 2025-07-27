<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        UserProfile::factory()->create(['user_id' => $this->user->id]);
        $this->post = Post::factory()->create(['user_id' => $this->user->id]);
    }

    /**
     * コメントを作成できることをテスト
     */
    public function test_store_creates_comment(): void
    {
        Sanctum::actingAs($this->user);

        $commentData = [
            'content' => 'これは新しいコメントです。',
        ];

        $response = $this->postJson("/api/posts/{$this->post->id}/comments", $commentData);

        $response->assertStatus(201)
            ->assertJson([
                'content' => 'これは新しいコメントです。',
                'user_id' => $this->user->id,
                'post_id' => $this->post->id,
            ]);

        $this->assertDatabaseHas('comments', [
            'content' => 'これは新しいコメントです。',
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
        ]);
    }

    /**
     * 空のコンテンツでコメント作成が失敗することをテスト
     */
    public function test_store_fails_with_empty_content(): void
    {
        Sanctum::actingAs($this->user);

        $commentData = [
            'content' => '',
        ];

        $response = $this->postJson("/api/posts/{$this->post->id}/comments", $commentData);

        $response->assertStatus(422);
    }

    /**
     * 存在しない投稿にコメント作成が失敗することをテスト
     */
    public function test_store_fails_with_nonexistent_post(): void
    {
        Sanctum::actingAs($this->user);

        $commentData = [
            'content' => 'これは新しいコメントです。',
        ];

        $response = $this->postJson('/api/posts/999/comments', $commentData);

        $response->assertStatus(500);
    }

    /**
     * コメントを削除できることをテスト
     */
    public function test_destroy_deletes_comment(): void
    {
        Sanctum::actingAs($this->user);
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
        ]);

        $response = $this->deleteJson("/api/posts/{$this->post->id}/comments/{$comment->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
    }

    /**
     * 他のユーザーのコメントを削除しようとして失敗することをテスト
     */
    public function test_destroy_fails_for_other_users_comment(): void
    {
        $otherUser = User::factory()->create();
        UserProfile::factory()->create(['user_id' => $otherUser->id]);
        Sanctum::actingAs($this->user);
        $comment = Comment::factory()->create([
            'user_id' => $otherUser->id,
            'post_id' => $this->post->id,
        ]);

        $response = $this->deleteJson("/api/posts/{$this->post->id}/comments/{$comment->id}");

        $response->assertStatus(404);
    }

    /**
     * 存在しないコメントを削除しようとして失敗することをテスト
     */
    public function test_destroy_fails_with_nonexistent_comment(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson("/api/posts/{$this->post->id}/comments/999");

        $response->assertStatus(404);
    }

    /**
     * 存在しない投稿のコメントを削除しようとして失敗することをテスト
     */
    public function test_destroy_fails_with_nonexistent_post(): void
    {
        Sanctum::actingAs($this->user);
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
        ]);

        $response = $this->deleteJson("/api/posts/999/comments/{$comment->id}");

        $response->assertStatus(404);
    }

    /**
     * 未認証でコメント作成が失敗することをテスト
     */
    public function test_store_fails_when_unauthenticated(): void
    {
        $commentData = [
            'content' => 'これは新しいコメントです。',
        ];

        $response = $this->postJson("/api/posts/{$this->post->id}/comments", $commentData);

        $response->assertStatus(401);
    }

    /**
     * 未認証でコメント削除が失敗することをテスト
     */
    public function test_destroy_fails_when_unauthenticated(): void
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
        ]);

        $response = $this->deleteJson("/api/posts/{$this->post->id}/comments/{$comment->id}");

        $response->assertStatus(401);
    }
}
