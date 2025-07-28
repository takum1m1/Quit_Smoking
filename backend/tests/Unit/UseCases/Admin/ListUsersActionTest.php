<?php

namespace Tests\Unit\UseCases\Admin;

use App\Models\User;
use App\UseCases\Admin\ListUsersAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListUsersActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_returns_all_users_with_profile()
    {
        User::factory()->count(2)->create();
        $action = new ListUsersAction();
        $result = $action();
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('profile', $result[0]->toArray());
    }
}
