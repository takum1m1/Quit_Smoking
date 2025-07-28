<?php

namespace Tests\Unit\UseCases\Admin;

use App\Models\Post;
use App\UseCases\Admin\ListPostsAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListPostsActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_returns_all_posts_with_relations()
    {
        Post::factory()->count(2)->create();
        $action = new ListPostsAction();
        $result = $action();
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('user', $result[0]->toArray());
        $this->assertArrayHasKey('comments', $result[0]->toArray());
        $this->assertArrayHasKey('likes', $result[0]->toArray());
    }
}
