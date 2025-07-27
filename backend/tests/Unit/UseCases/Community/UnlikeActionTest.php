<?php

namespace Tests\Unit\UseCases\Community;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\UseCases\Community\UnlikeAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UnlikeActionTest extends TestCase
{
    use RefreshDatabase;

    private UnlikeAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UnlikeAction();
    }

    /**
     * いいねが正常に解除されることをテスト
     */
    public function test_unlike_post_successfully(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        // いいねを作成
        Like::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        Auth::login($user);

        $this->action->__invoke($post->id);

        // いいねが削除されていることを確認（ソフトデリート）
        $this->assertSoftDeleted('likes', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    /**
     * 存在しない投稿のいいねを解除しようとしてエラーが発生することをテスト
     */
    public function test_unlike_post_with_invalid_id(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->expectException(ModelNotFoundException::class);

        $this->action->__invoke(999);
    }

    /**
     * いいねしていない投稿のいいねを解除しようとしてエラーが発生することをテスト
     */
    public function test_unlike_post_not_liked(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('あなたはこの投稿に「いいね！」を押していません。');

        $this->action->__invoke($post->id);
    }

    /**
     * 他のユーザーのいいねを解除しようとしてエラーが発生することをテスト
     */
    public function test_unlike_other_users_like(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user1->id]);

        // user1がいいねを作成
        Like::factory()->create([
            'user_id' => $user1->id,
            'post_id' => $post->id,
        ]);

        Auth::login($user2);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('あなたはこの投稿に「いいね！」を押していません。');

        $this->action->__invoke($post->id);
    }

    /**
     * 複数のいいねを解除した場合のテスト
     */
    public function test_unlike_multiple_likes(): void
    {
        $user = User::factory()->create();
        $post1 = Post::factory()->create(['user_id' => $user->id]);
        $post2 = Post::factory()->create(['user_id' => $user->id]);

        // 2つの投稿にいいねを作成
        Like::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post1->id,
        ]);
        Like::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post2->id,
        ]);

        Auth::login($user);

        // post1のいいねを解除
        $this->action->__invoke($post1->id);
        $this->assertSoftDeleted('likes', [
            'user_id' => $user->id,
            'post_id' => $post1->id,
        ]);

        // post2のいいねを解除
        $this->action->__invoke($post2->id);
        $this->assertSoftDeleted('likes', [
            'user_id' => $user->id,
            'post_id' => $post2->id,
        ]);

        // 両方のいいねが削除されていることを確認
        $this->assertSoftDeleted('likes', [
            'user_id' => $user->id,
            'post_id' => $post1->id,
        ]);
        $this->assertSoftDeleted('likes', [
            'user_id' => $user->id,
            'post_id' => $post2->id,
        ]);
    }

    /**
     * 削除された投稿のいいねを解除しようとしてエラーが発生することをテスト
     */
    public function test_unlike_deleted_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        // いいねを作成
        Like::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        // 投稿を削除
        $post->delete();

        Auth::login($user);

        $this->expectException(ModelNotFoundException::class);

        $this->action->__invoke($post->id);
    }

    /**
     * 既に削除されたいいねを解除しようとしてエラーが発生することをテスト
     */
    public function test_unlike_already_deleted_like(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        // いいねを作成して削除
        $like = Like::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
        $like->delete();

        Auth::login($user);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('あなたはこの投稿に「いいね！」を押していません。');

        $this->action->__invoke($post->id);
    }

    /**
     * いいね解除後に他のいいねが影響を受けないことをテスト
     */
    public function test_unlike_does_not_affect_other_likes(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user1->id]);

        // 両方のユーザーがいいねを作成
        Like::factory()->create([
            'user_id' => $user1->id,
            'post_id' => $post->id,
        ]);
        Like::factory()->create([
            'user_id' => $user2->id,
            'post_id' => $post->id,
        ]);

        Auth::login($user1);

        // user1のいいねを解除
        $this->action->__invoke($post->id);

        // user1のいいねは削除されている
        $this->assertSoftDeleted('likes', [
            'user_id' => $user1->id,
            'post_id' => $post->id,
        ]);

        // user2のいいねは削除されていない
        $this->assertDatabaseHas('likes', [
            'user_id' => $user2->id,
            'post_id' => $post->id,
        ]);
    }
}
