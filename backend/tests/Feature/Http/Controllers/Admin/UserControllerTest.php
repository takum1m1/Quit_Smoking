<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin, 'sanctum');
    }

    public function test_admin_can_list_users()
    {
        User::factory()->count(3)->create();
        $response = $this->getJson('/api/admin/users');
        $response->assertOk()->assertJsonStructure([['id', 'email', 'profile']]);
    }

    public function test_admin_can_show_user_detail()
    {
        $user = User::factory()->create();
        $response = $this->getJson('/api/admin/users/' . $user->id);
        $response->assertOk()->assertJsonFragment(['id' => $user->id]);
    }

    public function test_admin_can_delete_user()
    {
        $user = User::factory()->create();
        $response = $this->deleteJson('/api/admin/users/' . $user->id);
        $response->assertOk()->assertJson(['message' => 'ユーザーは正常に削除されました。']);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }
}
