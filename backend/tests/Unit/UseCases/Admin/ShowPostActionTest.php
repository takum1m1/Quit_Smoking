<?php

namespace Tests\Unit\UseCases\Admin;

use App\Models\Post;
use App\UseCases\Admin\ShowPostAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowPostActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_returns_post_with_relations()
    {
        $post = Post::factory()->create();
        $action = new ShowPostAction();
        $result = $action($post->id);
        $this->assertEquals($post->id, $result->id);
        $this->assertArrayHasKey('user', $result->toArray());
        $this->assertArrayHasKey('comments', $result->toArray());
        $this->assertArrayHasKey('likes', $result->toArray());
    }
}
