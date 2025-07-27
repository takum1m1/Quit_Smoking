<?php

namespace Tests\Unit\UseCases\Community;

use App\Models\Post;
use App\Models\User;
use App\UseCases\Community\CreatePostAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CreatePostActionTest extends TestCase
{
    use RefreshDatabase;

    private CreatePostAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreatePostAction();
    }

    /**
     * 投稿が正常に作成されることをテスト
     */
    public function test_create_post_successfully(): void
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create();
        Auth::login($user);

        $postData = [
            'content' => 'これはテスト投稿です。',
        ];

        // 投稿を作成
        $post = $this->action->__invoke($postData);

        // アサーション
        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals($user->id, $post->user_id);
        $this->assertEquals($postData['content'], $post->content);
        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'content' => $postData['content'],
        ]);

        // リレーションがロードされていることを確認
        $this->assertTrue($post->relationLoaded('user'));
        $this->assertTrue($post->relationLoaded('comments'));
        $this->assertTrue($post->relationLoaded('likes'));
    }

    /**
     * 空のコンテンツで投稿を作成した場合のテスト
     */
    public function test_create_post_with_empty_content(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $postData = [
            'content' => '',
        ];

        $post = $this->action->__invoke($postData);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals('', $post->content);
        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'content' => '',
        ]);
    }

    /**
     * 長いコンテンツで投稿を作成した場合のテスト
     */
    public function test_create_post_with_long_content(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $longContent = str_repeat('これは長いテスト投稿です。', 100);
        $postData = [
            'content' => $longContent,
        ];

        $post = $this->action->__invoke($postData);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals($longContent, $post->content);
        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'content' => $longContent,
        ]);
    }

    /**
     * 複数の投稿を作成した場合のテスト
     */
    public function test_create_multiple_posts(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $postData1 = ['content' => '1つ目の投稿'];
        $postData2 = ['content' => '2つ目の投稿'];

        $post1 = $this->action->__invoke($postData1);
        $post2 = $this->action->__invoke($postData2);

        $this->assertNotEquals($post1->id, $post2->id);
        $this->assertEquals($user->id, $post1->user_id);
        $this->assertEquals($user->id, $post2->user_id);

        $this->assertDatabaseCount('posts', 2);
    }
}
