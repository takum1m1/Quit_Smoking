<?php

namespace Tests\Unit\UseCases\Community;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\UseCases\Community\LikeAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LikeActionTest extends TestCase
{
    use RefreshDatabase;

    private LikeAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new LikeAction();
    }

    /**
     * いいねが正常に作成されることをテスト
     */
    public function test_like_post_successfully(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $this->action->__invoke($post->id);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    /**
     * 存在しない投稿にいいねしようとしてエラーが発生することをテスト
     */
    public function test_like_post_with_invalid_id(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->expectException(ModelNotFoundException::class);

        $this->action->__invoke(999);
    }

    /**
     * 既にいいねした投稿に再度いいねしようとしてエラーが発生することをテスト
     */
    public function test_like_post_already_liked(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        // 既にいいねを作成
        Like::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        Auth::login($user);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('あなたはすでにこの投稿に「いいね！」を押しています。');

        $this->action->__invoke($post->id);
    }

    /**
     * 複数のユーザーが同じ投稿にいいねできることをテスト
     */
    public function test_multiple_users_can_like_same_post(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user1->id]);

        // user1がいいね
        Auth::login($user1);
        $this->action->__invoke($post->id);

        // user2がいいね
        Auth::login($user2);
        $this->action->__invoke($post->id);

        $this->assertDatabaseCount('likes', 2);
        $this->assertDatabaseHas('likes', [
            'user_id' => $user1->id,
            'post_id' => $post->id,
        ]);
        $this->assertDatabaseHas('likes', [
            'user_id' => $user2->id,
            'post_id' => $post->id,
        ]);
    }

    /**
     * 同じユーザーが異なる投稿にいいねできることをテスト
     */
    public function test_user_can_like_different_posts(): void
    {
        $user = User::factory()->create();
        $post1 = Post::factory()->create(['user_id' => $user->id]);
        $post2 = Post::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        // post1にいいね
        $this->action->__invoke($post1->id);

        // post2にいいね
        $this->action->__invoke($post2->id);

        $this->assertDatabaseCount('likes', 2);
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'post_id' => $post1->id,
        ]);
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'post_id' => $post2->id,
        ]);
    }

    /**
     * 削除された投稿にいいねしようとしてエラーが発生することをテスト
     */
    public function test_like_deleted_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        // 投稿を削除
        $post->delete();

        Auth::login($user);

        $this->expectException(ModelNotFoundException::class);

        $this->action->__invoke($post->id);
    }

    /**
     * いいねの重複チェックが正しく動作することをテスト
     */
    public function test_like_duplicate_check(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        // 1回目のいいね
        $this->action->__invoke($post->id);
        $this->assertDatabaseCount('likes', 1);

        // 2回目のいいね（エラーになるはず）
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('あなたはすでにこの投稿に「いいね！」を押しています。');

        $this->action->__invoke($post->id);

        // いいねの数は変わらない
        $this->assertDatabaseCount('likes', 1);
    }
}
