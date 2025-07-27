<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LikeControllerTest extends TestCase
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
     * 投稿にいいねを押せることをテスト
     */
    public function test_like_post_creates_like(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson("/api/posts/{$this->post->id}/like");

        $response->assertStatus(200)
            ->assertJson(['message' => '投稿にいいねを押しました。']);

        $this->assertDatabaseHas('likes', [
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
        ]);
    }

    /**
     * 同じ投稿に重複していいねを押そうとして失敗することをテスト
     */
    public function test_like_post_fails_when_already_liked(): void
    {
        Sanctum::actingAs($this->user);
        Like::factory()->create([
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
        ]);

        $response = $this->postJson("/api/posts/{$this->post->id}/like");

        $response->assertStatus(500);
    }

    /**
     * 存在しない投稿にいいねを押そうとして失敗することをテスト
     */
    public function test_like_post_fails_with_nonexistent_post(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/posts/999/like');

        $response->assertStatus(404);
    }

    /**
     * 投稿のいいねを取り消せることをテスト
     */
    public function test_unlike_post_deletes_like(): void
    {
        Sanctum::actingAs($this->user);
        $like = Like::factory()->create([
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
        ]);

        $response = $this->postJson("/api/posts/{$this->post->id}/unlike");

        $response->assertStatus(200)
            ->assertJson(['message' => '投稿のいいねを取り消しました。']);

        $this->assertSoftDeleted('likes', ['id' => $like->id]);
    }

    /**
     * いいねしていない投稿のいいねを取り消そうとして失敗することをテスト
     */
    public function test_unlike_post_fails_when_not_liked(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson("/api/posts/{$this->post->id}/unlike");

        $response->assertStatus(500);
    }

    /**
     * 存在しない投稿のいいねを取り消そうとして失敗することをテスト
     */
    public function test_unlike_post_fails_with_nonexistent_post(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/posts/999/unlike');

        $response->assertStatus(404);
    }

    /**
     * 他のユーザーのいいねを取り消そうとして失敗することをテスト
     */
    public function test_unlike_post_fails_for_other_users_like(): void
    {
        $otherUser = User::factory()->create();
        UserProfile::factory()->create(['user_id' => $otherUser->id]);
        Sanctum::actingAs($this->user);
        $like = Like::factory()->create([
            'user_id' => $otherUser->id,
            'post_id' => $this->post->id,
        ]);

        $response = $this->postJson("/api/posts/{$this->post->id}/unlike");

        $response->assertStatus(500);
    }

    /**
     * 未認証でいいねが失敗することをテスト
     */
    public function test_like_post_fails_when_unauthenticated(): void
    {
        $response = $this->postJson("/api/posts/{$this->post->id}/like");

        $response->assertStatus(401);
    }

    /**
     * 未認証でいいね取り消しが失敗することをテスト
     */
    public function test_unlike_post_fails_when_unauthenticated(): void
    {
        $response = $this->postJson("/api/posts/{$this->post->id}/unlike");

        $response->assertStatus(401);
    }

    /**
     * 複数のユーザーが同じ投稿にいいねを押せることをテスト
     */
    public function test_multiple_users_can_like_same_post(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        UserProfile::factory()->create(['user_id' => $user1->id]);
        UserProfile::factory()->create(['user_id' => $user2->id]);

        Sanctum::actingAs($user1);
        $response1 = $this->postJson("/api/posts/{$this->post->id}/like");
        $response1->assertStatus(200);

        Sanctum::actingAs($user2);
        $response2 = $this->postJson("/api/posts/{$this->post->id}/like");
        $response2->assertStatus(200);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user1->id,
            'post_id' => $this->post->id,
        ]);
        $this->assertDatabaseHas('likes', [
            'user_id' => $user2->id,
            'post_id' => $this->post->id,
        ]);
    }
}
