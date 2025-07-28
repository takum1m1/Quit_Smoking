<?php

namespace Tests\Unit\UseCases\Admin;

use App\Models\Comment;
use App\UseCases\Admin\ListCommentsAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListCommentsActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_returns_all_comments_with_relations()
    {
        Comment::factory()->count(2)->create();
        $action = new ListCommentsAction();
        $result = $action();
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('user', $result[0]->toArray());
        $this->assertArrayHasKey('post', $result[0]->toArray());
    }
}
