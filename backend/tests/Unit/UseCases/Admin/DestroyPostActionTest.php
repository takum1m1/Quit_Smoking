<?php

namespace Tests\Unit\UseCases\Admin;

use App\Models\Post;
use App\UseCases\Admin\DestroyPostAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyPostActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_deletes_post()
    {
        $post = Post::factory()->create();
        $action = new DestroyPostAction();
        $action($post->id);
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }
}
