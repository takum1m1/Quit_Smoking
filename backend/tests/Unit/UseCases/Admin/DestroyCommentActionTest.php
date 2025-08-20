<?php

namespace Tests\Unit\UseCases\Admin;

use App\Models\Comment;
use App\UseCases\Admin\DestroyCommentAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyCommentActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_deletes_comment()
    {
        $comment = Comment::factory()->create();
        $action = new DestroyCommentAction();
        $action($comment->id);
        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
    }
}
