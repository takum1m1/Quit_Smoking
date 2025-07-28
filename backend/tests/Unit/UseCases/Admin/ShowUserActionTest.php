<?php

namespace Tests\Unit\UseCases\Admin;

use App\Models\User;
use App\UseCases\Admin\ShowUserAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowUserActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_returns_user_with_profile()
    {
        $user = User::factory()->create();
        $action = new ShowUserAction();
        $result = $action($user->id);
        $this->assertEquals($user->id, $result->id);
        $this->assertArrayHasKey('profile', $result->toArray());
    }
}
