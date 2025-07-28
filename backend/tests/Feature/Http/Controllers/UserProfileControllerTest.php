<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_プロフィール取得時にバッジ情報が含まれる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'quit_date' => Carbon::now()->subDays(10),
            'earned_badges' => ['one_week'],
        ]);

        Sanctum::actingAs($user);

        // Act
        $response = $this->getJson('/api/profile');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'display_name',
            'daily_cigarettes',
            'pack_cost',
            'quit_date',
            'quit_days_count',
            'quit_cigarettes',
            'saved_money',
            'extended_life',
            'badges' => [
                '*' => [
                    'code',
                    'name',
                    'description',
                ]
            ]
        ]);

        $this->assertCount(1, $response->json('badges'));
        $this->assertEquals('one_week', $response->json('badges.0.code'));
    }

    public function test_バッジチェックエンドポイントが正常に動作する(): void
    {
        // Arrange
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'quit_date' => Carbon::now()->subDays(7),
        ]);

        Sanctum::actingAs($user);

        // Act
        $response = $this->postJson('/api/profile/check-badges');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'awarded_badges' => [
                '*' => [
                    'code',
                    'name',
                    'description',
                ]
            ]
        ]);

        $this->assertCount(1, $response->json('awarded_badges'));
        $this->assertEquals('one_week', $response->json('awarded_badges.0.code'));
    }

    public function test_プロフィール更新時にバッジチェックが実行される(): void
    {
        // Arrange
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'quit_date' => Carbon::now()->subDays(7),
        ]);

        Sanctum::actingAs($user);

        // Act
        $response = $this->patchJson('/api/profile', [
            'display_name' => 'Updated Name',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ]);

        // Assert
        $response->assertStatus(200);

        // バッジが授与されていることを確認
        $this->assertContains('one_week', $userProfile->fresh()->earned_badges);
    }

    public function test_他のユーザーのプロフィール取得時にバッジ情報が含まれる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherUserProfile = UserProfile::factory()->create([
            'user_id' => $otherUser->id,
            'quit_date' => Carbon::now()->subDays(10),
            'earned_badges' => ['one_week'],
        ]);

        Sanctum::actingAs($user);

        // Act
        $response = $this->getJson("/api/profile/{$otherUser->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'display_name',
            'daily_cigarettes',
            'pack_cost',
            'quit_date',
            'quit_days_count',
            'quit_cigarettes',
            'saved_money',
            'extended_life',
            'badges' => [
                '*' => [
                    'code',
                    'name',
                    'description',
                ]
            ]
        ]);

        $this->assertCount(1, $response->json('badges'));
        $this->assertEquals('one_week', $response->json('badges.0.code'));
    }
}
