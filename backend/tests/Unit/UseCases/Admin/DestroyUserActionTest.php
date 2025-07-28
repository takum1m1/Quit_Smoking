<?php

namespace Tests\Unit\UseCases\Admin;

use App\Models\User;
use App\UseCases\Admin\DestroyUserAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyUserActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_deletes_user()
    {
        $user = User::factory()->create();
        $action = new DestroyUserAction();
        $result = $action($user->id);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertEquals('ユーザーは正常に削除されました。', $result['message']);
    }
}
