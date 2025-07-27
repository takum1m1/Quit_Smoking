<?php

namespace Tests\Unit\UseCases\Community;

use App\Models\Post;
use App\Models\User;
use App\UseCases\Community\ShowPostAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ShowPostActionTest extends TestCase
{
    use RefreshDatabase;

    private ShowPostAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new ShowPostAction();
    }

        /**
     * 投稿が正常に取得されることをテスト
     */
    public function test_show_post_successfully(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        // リクエストをモック
        $request = Request::create("/api/posts/{$post->id}");
        $request->setRouteResolver(function () use ($request) {
            return Route::get("/api/posts/{id}", function () {})->bind($request);
        });
        $request->route()->setParameter('id', $post->id);

        app()->instance('request', $request);

        $result = $this->action->__invoke();

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals($post->id, $result->id);
        $this->assertEquals($post->content, $result->content);
        $this->assertEquals($user->id, $result->user_id);
    }

        /**
     * リレーションがロードされていることをテスト
     */
    public function test_show_post_with_relations_loaded(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        // リクエストをモック
        $request = Request::create("/api/posts/{$post->id}");
        $request->setRouteResolver(function () use ($request) {
            return Route::get("/api/posts/{id}", function () {})->bind($request);
        });
        $request->route()->setParameter('id', $post->id);

        app()->instance('request', $request);

        $result = $this->action->__invoke();

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
    public function test_show_post_with_invalid_id(): void
    {
        $this->expectException(ModelNotFoundException::class);

        // 存在しないIDでリクエストをモック
        $request = Request::create("/api/posts/999");
        $request->setRouteResolver(function () use ($request) {
            return Route::get("/api/posts/{id}", function () {})->bind($request);
        });
        $request->route()->setParameter('id', 999);

        app()->instance('request', $request);

        $this->action->__invoke();
    }

        /**
     * 削除された投稿でエラーが発生することをテスト
     */
    public function test_show_deleted_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        // 投稿を削除
        $post->delete();

        $this->expectException(ModelNotFoundException::class);

        // リクエストをモック
        $request = Request::create("/api/posts/{$post->id}");
        $request->setRouteResolver(function () use ($request) {
            return Route::get("/api/posts/{id}", function () {})->bind($request);
        });
        $request->route()->setParameter('id', $post->id);

        app()->instance('request', $request);

        $this->action->__invoke();
    }

        /**
     * 投稿にコメントとライクが含まれている場合のテスト
     */
    public function test_show_post_with_comments_and_likes(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        // コメントとライクを作成（実際のモデルが存在する場合）
        // このテストは実際のCommentとLikeモデルが実装された後に有効になります

        // リクエストをモック
        $request = Request::create("/api/posts/{$post->id}");
        $request->setRouteResolver(function () use ($request) {
            return Route::get("/api/posts/{id}", function () {})->bind($request);
        });
        $request->route()->setParameter('id', $post->id);

        app()->instance('request', $request);

        $result = $this->action->__invoke();

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals($post->id, $result->id);

        // コメントとライクのリレーションがロードされていることを確認
        $this->assertTrue($result->relationLoaded('comments'));
        $this->assertTrue($result->relationLoaded('likes'));
    }
}
