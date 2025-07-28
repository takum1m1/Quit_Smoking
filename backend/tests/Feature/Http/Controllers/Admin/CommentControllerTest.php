<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCommentControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin, 'sanctum');
    }

    public function test_admin_can_list_comments()
    {
        Comment::factory()->count(3)->create();
        $response = $this->getJson('/api/admin/comments');
        $response->assertOk()->assertJsonStructure([['id', 'user', 'post']]);
    }

    public function test_admin_can_show_comment_detail()
    {
        $comment = Comment::factory()->create();
        $response = $this->getJson('/api/admin/comments/' . $comment->id);
        $response->assertOk()->assertJsonFragment(['id' => $comment->id]);
    }

    public function test_admin_can_delete_comment()
    {
        $comment = Comment::factory()->create();
        $response = $this->deleteJson('/api/admin/comments/' . $comment->id);
        $response->assertOk()->assertJson(['message' => 'コメントは正常に削除されました。']);
        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
    }
}
