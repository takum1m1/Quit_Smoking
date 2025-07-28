<?php

namespace Tests\Unit\UseCases\Admin;

use App\Models\Comment;
use App\UseCases\Admin\ShowCommentAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowCommentActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_returns_comment_with_relations()
    {
        $comment = Comment::factory()->create();
        $action = new ShowCommentAction();
        $result = $action($comment->id);
        $this->assertEquals($comment->id, $result->id);
        $this->assertArrayHasKey('user', $result->toArray());
        $this->assertArrayHasKey('post', $result->toArray());
    }
}
