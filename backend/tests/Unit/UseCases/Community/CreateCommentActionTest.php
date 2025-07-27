<?php

namespace Tests\Unit\UseCases\Community;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\UseCases\Community\CreateCommentAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CreateCommentActionTest extends TestCase
{
    use RefreshDatabase;

    private CreateCommentAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreateCommentAction();
    }

    /**
     * コメントが正常に作成されることをテスト
     */
    public function test_create_comment_successfully(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $commentData = [
            'content' => 'これはテストコメントです。',
        ];

        $comment = $this->action->__invoke($commentData, $post->id);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals($user->id, $comment->user_id);
        $this->assertEquals($post->id, $comment->post_id);
        $this->assertEquals($commentData['content'], $comment->content);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'post_id' => $post->id,
            'content' => $commentData['content'],
        ]);

        // リレーションがロードされていることを確認
        $this->assertTrue($comment->relationLoaded('user'));
    }

    /**
     * 空のコンテンツでコメントを作成した場合のテスト
     */
    public function test_create_comment_with_empty_content(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $commentData = [
            'content' => '',
        ];

        $comment = $this->action->__invoke($commentData, $post->id);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals('', $comment->content);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'post_id' => $post->id,
            'content' => '',
        ]);
    }

    /**
     * 長いコンテンツでコメントを作成した場合のテスト
     */
    public function test_create_comment_with_long_content(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $longContent = str_repeat('これは長いテストコメントです。', 50);
        $commentData = [
            'content' => $longContent,
        ];

        $comment = $this->action->__invoke($commentData, $post->id);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals($longContent, $comment->content);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'post_id' => $post->id,
            'content' => $longContent,
        ]);
    }

    /**
     * 複数のコメントを作成した場合のテスト
     */
    public function test_create_multiple_comments(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $commentData1 = ['content' => '1つ目のコメント'];
        $commentData2 = ['content' => '2つ目のコメント'];

        $comment1 = $this->action->__invoke($commentData1, $post->id);
        $comment2 = $this->action->__invoke($commentData2, $post->id);

        $this->assertNotEquals($comment1->id, $comment2->id);
        $this->assertEquals($user->id, $comment1->user_id);
        $this->assertEquals($user->id, $comment2->user_id);
        $this->assertEquals($post->id, $comment1->post_id);
        $this->assertEquals($post->id, $comment2->post_id);

        $this->assertDatabaseCount('comments', 2);
    }

    /**
     * 異なる投稿にコメントを作成した場合のテスト
     */
    public function test_create_comments_on_different_posts(): void
    {
        $user = User::factory()->create();
        $post1 = Post::factory()->create(['user_id' => $user->id]);
        $post2 = Post::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $commentData = ['content' => 'テストコメント'];

        $comment1 = $this->action->__invoke($commentData, $post1->id);
        $comment2 = $this->action->__invoke($commentData, $post2->id);

        $this->assertNotEquals($comment1->id, $comment2->id);
        $this->assertEquals($post1->id, $comment1->post_id);
        $this->assertEquals($post2->id, $comment2->post_id);

        $this->assertDatabaseCount('comments', 2);
    }
}
