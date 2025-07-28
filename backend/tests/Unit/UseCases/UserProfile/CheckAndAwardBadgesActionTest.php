<?php

namespace Tests\Unit\UseCases\UserProfile;

use App\Models\User;
use App\Models\UserProfile;
use App\UseCases\UserProfile\CheckAndAwardBadgesAction;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CheckAndAwardBadgesActionTest extends TestCase
{
    use RefreshDatabase;

    private CheckAndAwardBadgesAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CheckAndAwardBadgesAction();
    }

    public function test_禁煙1週間で1週間バッジを授与する(): void
    {
        // Arrange
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'quit_date' => Carbon::now()->subDays(7),
        ]);

        Sanctum::actingAs($user);

        // Act
        $result = ($this->action)();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals('one_week', $result[0]['code']);
        $this->assertContains('one_week', $userProfile->fresh()->earned_badges);
    }

    public function test_禁煙30日で1ヶ月バッジを授与する(): void
    {
        // Arrange
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'quit_date' => Carbon::now()->subDays(30),
        ]);

        Sanctum::actingAs($user);

        // Act
        $result = ($this->action)();

        // Assert
        $this->assertCount(2, $result); // 1週間バッジと1ヶ月バッジの両方が授与される
        $this->assertContains('one_week', $userProfile->fresh()->earned_badges);
        $this->assertContains('one_month', $userProfile->fresh()->earned_badges);
    }

    public function test_既に授与されたバッジは重複して授与しない(): void
    {
        // Arrange
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'quit_date' => Carbon::now()->subDays(7),
            'earned_badges' => ['one_week'],
        ]);

        Sanctum::actingAs($user);

        // Act
        $result = ($this->action)();

        // Assert
        $this->assertEmpty($result);
        $this->assertCount(1, $userProfile->fresh()->earned_badges);
    }

    public function test_複数のバッジを同時に授与する(): void
    {
        // Arrange
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'quit_date' => Carbon::now()->subDays(30),
        ]);

        Sanctum::actingAs($user);

        // Act
        $result = ($this->action)();

        // Assert
        $this->assertCount(2, $result);
        $this->assertContains('one_week', $userProfile->fresh()->earned_badges);
        $this->assertContains('one_month', $userProfile->fresh()->earned_badges);
    }


}
