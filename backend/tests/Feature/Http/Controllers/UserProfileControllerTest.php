<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private UserProfile $userProfile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->userProfile = UserProfile::factory()->create([
            'user_id' => $this->user->id,
            'display_name' => 'Test User',
            'daily_cigarettes' => 20,
            'pack_cost' => 500,
        ]);
    }

    /**
     * 自分のプロフィールを取得できることをテスト
     */
    public function test_my_profile_returns_user_profile(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJson([
                'display_name' => 'Test User',
                'daily_cigarettes' => 20,
                'pack_cost' => 500,
            ])
            ->assertJsonStructure([
                'display_name',
                'daily_cigarettes',
                'pack_cost',
                'quit_date',
                'quit_days_count',
                'quit_cigarettes',
                'saved_money',
                'extended_life',
            ]);
    }

    /**
     * 未認証でプロフィール取得が失敗することをテスト
     */
    public function test_my_profile_fails_when_unauthenticated(): void
    {
        $response = $this->getJson('/api/profile');

        $response->assertStatus(401);
    }

    /**
     * プロフィールを更新できることをテスト
     */
    public function test_update_modifies_profile(): void
    {
        Sanctum::actingAs($this->user);

        $updateData = [
            'display_name' => 'Updated User',
            'daily_cigarettes' => 15,
            'pack_cost' => 400,
        ];

        $response = $this->patchJson('/api/profile', $updateData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'プロフィールが更新されました。']);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $this->user->id,
            'display_name' => 'Updated User',
            'daily_cigarettes' => 15,
            'pack_cost' => 400,
        ]);
    }

    /**
     * 禁煙情報をリセットできることをテスト
     */
    public function test_reset_quit_info_resets_quit_date(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/profile/reset');

        $response->assertStatus(200)
            ->assertJson(['message' => '禁煙情報がリセットされました。']);

        $this->userProfile->refresh();
        $this->assertEquals(now()->toDateString(), $this->userProfile->quit_date->toDateString());
    }

    /**
     * 他のユーザーのプロフィールを取得できることをテスト
     */
    public function test_show_by_id_returns_other_user_profile(): void
    {
        $otherUser = User::factory()->create();
        $otherProfile = UserProfile::factory()->create([
            'user_id' => $otherUser->id,
            'display_name' => 'Other User',
            'daily_cigarettes' => 25,
            'pack_cost' => 600,
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/profile/{$otherUser->id}");

        $response->assertStatus(200)
            ->assertJson([
                'display_name' => 'Other User',
                'daily_cigarettes' => 25,
                'pack_cost' => 600,
            ])
            ->assertJsonStructure([
                'display_name',
                'daily_cigarettes',
                'pack_cost',
                'quit_date',
                'quit_days_count',
                'quit_cigarettes',
                'saved_money',
                'extended_life',
            ]);
    }

    /**
     * 存在しないユーザーIDで404エラーが返されることをテスト
     */
    public function test_show_by_id_returns_404_for_nonexistent_user(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/profile/999');

        $response->assertStatus(404);
    }

    /**
     * 未認証でプロフィール更新が失敗することをテスト
     */
    public function test_update_fails_when_unauthenticated(): void
    {
        $updateData = [
            'display_name' => 'Updated User',
        ];

        $response = $this->patchJson('/api/profile', $updateData);

        $response->assertStatus(401);
    }

    /**
     * 未認証で禁煙情報リセットが失敗することをテスト
     */
    public function test_reset_quit_info_fails_when_unauthenticated(): void
    {
        $response = $this->postJson('/api/profile/reset');

        $response->assertStatus(401);
    }

    /**
     * 未認証で他のユーザープロフィール取得が失敗することをテスト
     */
    public function test_show_by_id_fails_when_unauthenticated(): void
    {
        $response = $this->getJson('/api/profile/1');

        $response->assertStatus(401);
    }
}
