<?php

namespace Tests\Unit\UseCases\Community;

use App\Models\Post;
use App\Models\User;
use App\UseCases\Community\ListPostsAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ListPostsActionTest extends TestCase
{
    use RefreshDatabase;

    private ListPostsAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new ListPostsAction();

        // テスト前にキャッシュをクリア
        Cache::flush();
    }

    /**
     * 投稿が存在しない場合のテスト
     */
    public function test_list_posts_when_no_posts_exist(): void
    {
        $posts = $this->action->__invoke();

        $this->assertInstanceOf(Collection::class, $posts);
        $this->assertCount(0, $posts);
    }

    /**
     * 投稿が存在する場合のテスト
     */
    public function test_list_posts_when_posts_exist(): void
    {
        // キャッシュをクリア
        Cache::flush();

        // ユーザーと投稿を作成
        $user = User::factory()->create();
        $post1 = Post::factory()->create(['user_id' => $user->id]);
        $post2 = Post::factory()->create(['user_id' => $user->id]);

        $posts = $this->action->__invoke();

        $this->assertInstanceOf(Collection::class, $posts);
        $this->assertCount(2, $posts);

        // 投稿が正しく取得されていることを確認
        $this->assertTrue($posts->contains($post1));
        $this->assertTrue($posts->contains($post2));
    }

    /**
     * リレーションがロードされていることをテスト
     */
    public function test_list_posts_with_relations_loaded(): void
    {
        // キャッシュをクリア
        Cache::flush();

        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $posts = $this->action->__invoke();

        $this->assertCount(1, $posts);
        $firstPost = $posts->first();

        // リレーションがロードされていることを確認
        $this->assertTrue($firstPost->relationLoaded('user'));
        $this->assertTrue($firstPost->relationLoaded('likes'));

        // ユーザー情報が正しく取得されていることを確認
        $this->assertEquals($user->id, $firstPost->user->id);
    }

    /**
     * 複数ユーザーの投稿が取得されることをテスト
     */
    public function test_list_posts_from_multiple_users(): void
    {
        // キャッシュをクリア
        Cache::flush();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $post1 = Post::factory()->create(['user_id' => $user1->id]);
        $post2 = Post::factory()->create(['user_id' => $user2->id]);

        $posts = $this->action->__invoke();

        $this->assertCount(2, $posts);

        // 両方のユーザーの投稿が取得されていることを確認
        $userIds = $posts->pluck('user.id')->toArray();
        $this->assertContains($user1->id, $userIds);
        $this->assertContains($user2->id, $userIds);
    }

    /**
     * 投稿の順序が正しいことをテスト（最新順）
     */
    public function test_list_posts_order(): void
    {
        // キャッシュをクリア
        Cache::flush();

        $user = User::factory()->create();

        // 古い投稿
        $oldPost = Post::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(2)
        ]);

        // 新しい投稿
        $newPost = Post::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()
        ]);

        $posts = $this->action->__invoke();

        $this->assertCount(2, $posts);

        // 投稿が作成日時順に取得されていることを確認
        $postIds = $posts->pluck('id')->toArray();
        $this->assertEquals($oldPost->id, $postIds[0]);
        $this->assertEquals($newPost->id, $postIds[1]);
    }
}
