<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        UserProfile::factory()->create(['user_id' => $this->user->id]);
    }

    /**
     * 投稿一覧を取得できることをテスト
     */
    public function test_index_returns_posts_list(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'content',
                    'user_id',
                    'created_at',
                    'updated_at',
                    'user' => [
                        'id',
                        'email',
                    ],
                ]
            ]);
    }

    /**
     * 特定の投稿を取得できることをテスト
     */
    public function test_show_returns_specific_post(): void
    {
        Sanctum::actingAs($this->user);
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $post->id,
                'content' => $post->content,
                'user_id' => $post->user_id,
            ]);
    }

    /**
     * 存在しない投稿IDで404エラーが返されることをテスト
     */
    public function test_show_returns_404_for_nonexistent_post(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/posts/999');

        $response->assertStatus(404);
    }

    /**
     * 投稿を作成できることをテスト
     */
    public function test_store_creates_post(): void
    {
        Sanctum::actingAs($this->user);

        $postData = [
            'content' => 'これは新しい投稿です。',
        ];

        $response = $this->postJson('/api/posts', $postData);

        $response->assertStatus(201)
            ->assertJson([
                'content' => 'これは新しい投稿です。',
                'user_id' => $this->user->id,
            ]);

        $this->assertDatabaseHas('posts', [
            'content' => 'これは新しい投稿です。',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * 空のコンテンツで投稿作成が失敗することをテスト
     */
    public function test_store_fails_with_empty_content(): void
    {
        Sanctum::actingAs($this->user);

        $postData = [
            'content' => '',
        ];

        $response = $this->postJson('/api/posts', $postData);

        $response->assertStatus(422);
    }

    /**
     * 投稿を更新できることをテスト
     */
    public function test_update_modifies_post(): void
    {
        Sanctum::actingAs($this->user);
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'content' => '更新された投稿内容です。',
        ];

        $response = $this->putJson("/api/posts/{$post->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $post->id,
                'content' => '更新された投稿内容です。',
                'user_id' => $this->user->id,
            ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'content' => '更新された投稿内容です。',
        ]);
    }

    /**
     * 他のユーザーの投稿を更新しようとして失敗することをテスト
     */
    public function test_update_fails_for_other_users_post(): void
    {
        $otherUser = User::factory()->create();
        UserProfile::factory()->create(['user_id' => $otherUser->id]);
        Sanctum::actingAs($this->user);
        $post = Post::factory()->create(['user_id' => $otherUser->id]);

        $updateData = [
            'content' => '更新された投稿内容です。',
        ];

        $response = $this->putJson("/api/posts/{$post->id}", $updateData);

        $response->assertStatus(403);
    }

    /**
     * 投稿を削除できることをテスト
     */
    public function test_destroy_deletes_post(): void
    {
        Sanctum::actingAs($this->user);
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    /**
     * 他のユーザーの投稿を削除しようとして失敗することをテスト
     */
    public function test_destroy_fails_for_other_users_post(): void
    {
        $otherUser = User::factory()->create();
        UserProfile::factory()->create(['user_id' => $otherUser->id]);
        Sanctum::actingAs($this->user);
        $post = Post::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(403);
    }

    /**
     * 未認証でアクセスすると401エラーが返されることをテスト
     */
    public function test_unauthenticated_access_returns_401(): void
    {
        $response = $this->getJson('/api/posts');
        $response->assertStatus(401);

        $response = $this->postJson('/api/posts', ['content' => 'test']);
        $response->assertStatus(401);
    }
}
