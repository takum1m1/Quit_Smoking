<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPostControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin, 'sanctum');
    }

    public function test_admin_can_list_posts()
    {
        Post::factory()->count(3)->create();
        $response = $this->getJson('/api/admin/posts');
        $response->assertOk()->assertJsonStructure([['id', 'user', 'comments', 'likes']]);
    }

    public function test_admin_can_show_post_detail()
    {
        $post = Post::factory()->create();
        $response = $this->getJson('/api/admin/posts/' . $post->id);
        $response->assertOk()->assertJsonFragment(['id' => $post->id]);
    }

    public function test_admin_can_delete_post()
    {
        $post = Post::factory()->create();
        $response = $this->deleteJson('/api/admin/posts/' . $post->id);
        $response->assertOk()->assertJson(['message' => '投稿は正常に削除されました。']);
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }
}
