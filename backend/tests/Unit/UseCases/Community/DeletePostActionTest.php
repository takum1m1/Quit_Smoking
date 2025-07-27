<?php

namespace Tests\Unit\UseCases\Community;

use App\Models\Post;
use App\Models\User;
use App\UseCases\Community\DeletePostAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class DeletePostActionTest extends TestCase
{
    use RefreshDatabase;

    private DeletePostAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeletePostAction();
    }

    /**
     * 投稿が正常に削除されることをテスト
     */
    public function test_delete_post_successfully(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $result = $this->action->__invoke($post->id);

        // レスポンスの確認
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals(['message' => '投稿は正常に削除されました。'], $result->getData(true));

        // データベースから削除されていることを確認（ソフトデリート）
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    /**
     * 存在しない投稿IDでエラーが発生することをテスト
     */
    public function test_delete_post_with_invalid_id(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->expectException(ModelNotFoundException::class);

        $this->action->__invoke(999);
    }

    /**
     * 他のユーザーの投稿を削除しようとしてエラーが発生することをテスト
     */
    public function test_delete_other_users_post(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user1->id]);

        Auth::login($user2);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('無許可の行為。');

        $this->action->__invoke($post->id);
    }

    /**
     * 既に削除された投稿を削除しようとしてエラーが発生することをテスト
     */
    public function test_delete_already_deleted_post(): void
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
     * 複数の投稿を削除した場合のテスト
     */
    public function test_delete_multiple_posts(): void
    {
        $user = User::factory()->create();
        $post1 = Post::factory()->create(['user_id' => $user->id]);
        $post2 = Post::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        // 1つ目の投稿を削除
        $result1 = $this->action->__invoke($post1->id);
        $this->assertEquals(200, $result1->getStatusCode());
        $this->assertSoftDeleted('posts', ['id' => $post1->id]);

        // 2つ目の投稿を削除
        $result2 = $this->action->__invoke($post2->id);
        $this->assertEquals(200, $result2->getStatusCode());
        $this->assertSoftDeleted('posts', ['id' => $post2->id]);

        // 両方の投稿が削除されていることを確認
        $this->assertSoftDeleted('posts', ['id' => $post1->id]);
        $this->assertSoftDeleted('posts', ['id' => $post2->id]);
    }

    /**
     * 削除後に他の投稿が影響を受けないことをテスト
     */
    public function test_delete_post_does_not_affect_other_posts(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $post1 = Post::factory()->create(['user_id' => $user1->id]);
        $post2 = Post::factory()->create(['user_id' => $user2->id]);

        Auth::login($user1);

        // user1の投稿を削除
        $result = $this->action->__invoke($post1->id);
        $this->assertEquals(200, $result->getStatusCode());

        // user1の投稿は削除されている
        $this->assertSoftDeleted('posts', ['id' => $post1->id]);

        // user2の投稿は削除されていない
        $this->assertDatabaseHas('posts', ['id' => $post2->id]);
    }
}
